<?php

namespace App\Http\Controllers;


// [
//             {
//                 "id": 2,
//                 "cart_id": 1,
//                 "product_id": 2,
//                 "quantity": 3,
//                 "updated_at": "2026-06-06T18:27:31.000000Z",
//                 "product": {
//                     "id": 2,
//                     "title": "Iphone x",
//                     "slug": "iphone-x",
//                     "cover_image": "/storage/products/covers/hqTEBcGmyK5hP7R4PtQToZ1qEDqbEi4lEVjI5HiM.webp",
//                     "price": "20000.00",
//                     "sold_count": 0,
//                     "seller_id": 1,
//                     "cover_image_url": "http://localhost:8000/storage/products/covers/hqTEBcGmyK5hP7R4PtQToZ1qEDqbEi4lEVjI5HiM.webp",
//                     "pictures": []
//                 }
//             },
//             {
//                 "id": 1,
//                 "cart_id": 1,
//                 "product_id": 1,
//                 "quantity": 2,
//                 "updated_at": "2026-06-06T18:17:30.000000Z",
//                 "product": {
//                     "id": 1,
//                     "title": "addidas shirt",
//                     "slug": "addidas-shirt",
//                     "cover_image": "/storage/products/covers/7Dx3GzIbDFGfnabqpBpzhJkyRCgBPy69ICfX835J.webp",
//                     "price": "1500.00",
//                     "sold_count": 0,
//                     "seller_id": 1,
//                     "cover_image_url": "http://localhost:8000/storage/products/covers/7Dx3GzIbDFGfnabqpBpzhJkyRCgBPy69ICfX835J.webp",
//                     "pictures": [
//                         {
//                             "id": 1,
//                             "product_id": 1,
//                             "picture": "/storage/products/gallery/U8nNE05WagOePq1D0S0xBwDKiR4VMRqL3blk95nR.webp",
//                             "picture_url": "http://localhost:8000/storage/products/gallery/U8nNE05WagOePq1D0S0xBwDKiR4VMRqL3blk95nR.webp"
//                         }
//                     ]
//                 }
//             }
//         ]

use App\Models\Cart\CartCoupon;
use App\Models\Coupon;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Notifications\Action;

class CartController extends Controller
{

  private function calculate_group_summary($group)
  {
    $subtotal = 0;
    $discount = 0;
    foreach ($group['items'] as $item) {
      $subtotal += $item['quantity'] * $item['product']['price'];
    }

    $discount = 0;
    $coupon = $group['coupon'];

    if ($coupon && isset($coupon) && Carbon::parse($coupon['expire_date'])->isFuture()) {
      $discount = $subtotal * ($coupon['percentage'] / 100);
    }

    return [
      "sub_total" => $subtotal,
      "discount" => $discount,
      "total" => $subtotal - $discount,
    ];
  }

  public function index(Request $request)
  {
    $cart = $request->user()->cart;
    if (!$cart) {
      return response()->json([
        "message" => "Cart is not exist"
      ]);
    }
    $groups = [];

    foreach ($cart->items()->get() as $item) {
      $sellerId = $item->product->seller_id;
      if (!isset($groups[$sellerId])) {
        $groups[$sellerId]["seller_id"] = $sellerId;
        $groups[$sellerId]['items'] = [];
      }
      $groups[$sellerId]["items"][] = [
        'id' => $item->id,
        'quantity' => $item->quantity,
        'product' => [
          'id' => $item->product->id,
          'title' => $item->product->title,
          'slug' => $item->product->slug,
          'price' => $item->product->price,
          'cover_image' => $item->product->cover_image_url,
          "stock" => $item->product->quantity
        ]
      ];
    }

    $cartCoupons = $cart->coupons()
      ->with('coupon:id,code,percentage,expire_date,max_usage,seller_id')
      ->get();

    foreach ($cartCoupons as $cartCoupon) {
      $groups[$cartCoupon->coupon->seller_id]["coupon"] = [
        "percentage" => $cartCoupon->coupon->percentage,
        "expire_date" => $cartCoupon->coupon->expire_date,
      ];
    }

    $cartSubtotal = 0;
    $cartDiscount = 0;

    foreach ($groups as &$group) {
      $groupSummary = $this->calculate_group_summary($group);
      $group['summary'] = $groupSummary;
      $cartSubtotal += $groupSummary['sub_total'];
      $cartDiscount += $groupSummary['discount'];
    }

    return response()->json([
      "cart" => [
        "id" => $cart->id,
        "items_count" => $cart->items()->count(),
        "groups" => array_values($groups),
        "summary_cart" => [
          "subtotal" => $cartSubtotal,
          "discount" => $cartDiscount,
          "total" => $cartSubtotal - $cartDiscount
        ]
      ],
    ], 201);
  }

  public function add_items_to_cart(Request $request)
  {
    $cart = $request->user()->cart;
    if (!$cart) {
      $cart = $request->user()->cart()->create();
    }

    $validated = $request->validate([
      "items" => "required|array",
      "items.*.id" => "required|integer|exists:products,id",
      "items.*.quantity" => "required|integer|min:1",
    ]);

    foreach ($validated['items'] as $item) {
      $cart_item = $cart->items()
        ->where('product_id', $item['id'])
        ->first();

      if ($cart_item) {
        $cart_item->quantity = $cart_item->quantity + $item['quantity'];
        $cart_item->save();
      } else {
        $cart->items()->create(["product_id" => $item['id'], "quantity" => $item['quantity']]);
      }
    }

    return response()->json([
      "message" => "Cart updated successfully",
      "cart" => [
        "id" => $cart->id,
        "items_count" => $cart->items()->count(),
        "items" => $cart->load('items.product')->items,
      ],
    ], 201);

  }

  public function update_cart_item_quantity(Request $request, int $item_id)
  {
    $validated = $request->validate([
      "quantity" => "required|integer|min:1",
    ]);
    $item = $request->user()->cart->items()->findOrFail($item_id);
    $item->update(["quantity" => $validated['quantity']]);
    return response()->json([
      "message" => "Item quantity updated successfully",
      "data" => $item
    ]);
  }

  public function delete_cart_item(Request $request, int $item_id)
  {
    $request->user()->cart->items()->findOrFail($item_id)->delete();
    return response()->noContent();
  }

  public function delete_cart(Request $request)
  {
    $request->user()->cart()->delete();
    return response()->noContent();
  }

  public function apply_coupon(Request $request)
  {
    $cart = $request->user()->cart;

    if (!$cart) {
      return response()->json([
        "message" => "Cart not found"
      ], 404);
    }

    $validated = $request->validate([
      "coupon_id" => "required|exists:coupons,id",
    ]);

    $coupon = Coupon::findOrFail($validated['coupon_id']);

    if ($cart->coupons()->where('coupon_id', $coupon->id)->exists()) {
      return response()->json([
        "message" => "Coupon already applied"
      ], 400);
    }

    if ($coupon->is_invalid()) {
      return response()->json([
        "message" => "Invalid coupon"
      ], 400);
    }

    $hasSeller = $cart->items()
      ->whereHas(
        'product',
        fn($q) =>
        $q->where('seller_id', $coupon->seller_id)
      )
      ->exists();

    if (!$hasSeller) {
      return response()->json([
        'message' => 'Your cart does not contain any products eligible for this coupon.'
      ], 400);
    }

    CartCoupon::create([
      'cart_id' => $cart->id,
      'coupon_id' => $coupon->id,
    ]);

    return response()->json([
      "message" => "Coupon applied successfully"
    ], 201);
  }

  public function remove_coupon(Request $request)
  {
    $cart = $request->user()->cart;
    if (!$cart) {
      return response()->json(["message" => "Cart not found"], 404);
    }

    $validated = $request->validate([
      "coupon_id" => "required|exists:coupons,id",
    ]);

    $deleted = DB::table('cart_coupons')
      ->where('cart_id', $cart->id)
      ->where('coupon_id', $validated['coupon_id'])
      ->delete();

    if ($deleted === 0) {
      return response()->json([
        'message' => 'Coupon not found in cart'
      ], 404);
    }

    return response()->noContent();
  }




}
