<?php

namespace App\Http\Controllers;

use App\Models\Cart\CartCoupon;
use App\Models\Coupon;
use DB;
use Illuminate\Http\Request;

class CartController extends Controller
{

  public function index(Request $request)
  {
    $cart = $request->user()->cart;
    if (!$cart) {
      return response()->json([
        "message" => "Cart is not exist"
      ]);
    }
    return response()->json([
      "cart" => [
        "id" => $cart->id,
        "items_count" => $cart->items()->count(),
        "items" => $cart->items()
          ->with([
            'product:id,title,slug,cover_image,price,sold_count',
            'product.pictures:id,product_id,picture',
          ])
          ->select('id', 'cart_id', 'product_id', 'quantity', 'updated_at')
          ->orderBy('updated_at', 'desc')
          ->get(),

        "coupons" => $cart->coupons()
          ->with('coupon:id,code,percentage,expire_date,max_usage')
          ->select('id', 'cart_id', 'coupon_id')
          ->get(),
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
