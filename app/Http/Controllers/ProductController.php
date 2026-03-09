<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Str;


class ProductController
{

  /** @var \App\Models\User $user */


  function create(StoreProductRequest $request)
  {
    $user = Auth::user();

    // check if product already exists
    $exist_product = $user->products()->whereSlug(Str::slug($request->title))->first();

    if ($exist_product) {
      return response()->json([
        'message' => 'product already exist',
        'data' => new ProductResource(
          $exist_product->load('sub_categories', 'pictures', 'attributes')
        ),
      ], 409);

    }
    ;

    $product = DB::transaction(function () use ($request, $user) {
      // create product
      $product = $user->products()->create([
        "title" => $request->title,
        "description" => $request->description,
        "price" => $request->price,
        "quantity" => $request->quantity,
      ]);

      // save cover image
      $coverPath = $request->file('cover_image')->store('products/covers', 'public');

      $product->update([
        'cover_image' => Storage::url($coverPath)
      ]);

      // save gallery
      if ($request->hasFile('product_pictures')) {
        $product_pictures = [];
        foreach ($request->file('product_pictures') as $image) {
          $path = $image->store('products/gallery', 'public');
          $product_pictures[] = ['picture' => Storage::url($path)];
        }
        $product->pictures()->createMany($product_pictures);
      };

      // sub categories
      $product->sub_categories()->sync($request->sub_categories ?? []);

      // attributes
      if ($request->filled('attributes')) {
        foreach ($request->input('attributes') as $attr) {
          $product->attributes()->create([
            "key" => strtolower($attr['key']),
            "value" => $attr['value'],
          ]);
        }
      }

      return $product;
    });



    return response()->json([
      "message" => "product created successfully",
      "data" => new ProductResource(
        $product->load('sub_categories', 'pictures', 'attributes')
      )
    ], 201);

  }


  function delete_one(int $id)
  {
    $user = Auth::user();
    $product = $user->products()->find($id);
    if (!$product) {
      return response()->json([
        "message" => "product not found"
      ], 404);
    }
    $product->delete();
    return response()->json(null, 204);
  }


  function find_one(int $id)
  {
    $user = Auth::user();
    $product = $user->products()->find($id);

    if (!$product) {
      return response()->json([
        "message" => "product not found"
      ], 404);
    }

    return response()->json(
      new ProductResource($product->load('attributes', 'sub_categories', 'pictures')),
    );
  }


  function find(Request $request)
  {
    $word = $request->query('word');
    $min_price = $request->query('min_price');
    $max_price = $request->query('max_price');
    $best_seller = $request->query('best_seller');
    $rating = $request->query('rating');
    $min_ratings = $request->query('min_ratings') ?? 0;
    $limit = $request->query('limit') ?? 10;
    $query = Product::query();

    if (!is_null($word)) {
      $query->where(function ($q) use ($word) {
        $q->where('title', 'like', '%' . $word . '%')
          ->orWhere('slug', 'like', '%' . $word . '%')
          ->orWhere('description', 'like', '%' . $word . '%');
      });
    }
    if (!is_null($min_price)) {
      $query->where('price', '>=', $min_price);
    }
    if (!is_null($max_price)) {
      $query->where('price', '<=', $max_price);
    }
    if (!is_null($best_seller) && in_array($best_seller, ['1', '0'], true)) {
      $query->orderBy('sold_count', (int) $best_seller ? 'desc' : 'asc');
    }
    if (!is_null($rating) && in_array($rating, ['high', 'low'], true)) {
      $query
        ->where('rating_count', '>=', $min_ratings)
        ->orderBy('rating_avg', $rating == 'high' ? 'desc' : 'asc');
    }
    $products = $query->paginate($limit);
    return response()->json([
      'data' => ProductResource::collection($products),
      'meta' => [
        'current_page' => $products->currentPage(),
        'prev_page' => $products->previousPageUrl(),
        'next_page' => $products->nextPageUrl(),
        'last_page' => $products->lastPage(),
        'per_page' => $products->perPage(),
        'total' => $products->total(),
      ]
    ]);
  }


  


}