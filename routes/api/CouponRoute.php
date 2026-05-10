<?php

use App\Http\Controllers\CouponController;
use Illuminate\Support\Facades\Route;




Route::middleware(['auth:sanctum', "roles:owner"])->group(function() {
  Route::post("coupons", [CouponController::class, 'create']);
  Route::get("coupons", [CouponController::class, 'find']);
  Route::put("coupons/{id}", [CouponController::class, 'update']);
  Route::get("coupons/{coupon}", [CouponController::class, 'find_one']);
  Route::delete("coupons/{coupon}", [CouponController::class, 'delete_one']);
  Route::delete("coupons", [CouponController::class, 'delete_all']);
});


