<?php

use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth:sanctum', 'roles:user'])->group(function () {

  Route::get('cart', [CartController::class, 'index']);
  Route::post('cart/items', [CartController::class, 'add_items_to_cart']);
  Route::put('cart/items/{id}', [CartController::class, 'update_cart_item_quantity']);
  Route::delete('cart/items/{id}', [CartController::class, 'delete_cart_item']);
  Route::delete('cart', [CartController::class, 'delete_cart']);
  Route::post('cart/coupon', [CartController::class, 'apply_coupon']);
  Route::delete('cart/coupon', [CartController::class, 'remove_coupon']);
  Route::post('cart/validate', [CartController::class, 'validateCart']);

});


