<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:sanctum", "roles:owner"]], function () {
  Route::post('products', [ProductController::class, 'create']);
  Route::delete('products/{id}', [ProductController::class, 'delete_one']);
  Route::get('products/{id}', [ProductController::class, 'find_one']);
  // update main
  // update rating
});

Route::get('products', [ProductController::class, 'find']);


