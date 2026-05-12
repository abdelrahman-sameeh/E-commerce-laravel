<?php

use App\Http\Controllers\AddressController;
use Illuminate\Support\Facades\Route;


Route::middleware(["auth:sanctum"])->group(function () {

  Route::post("/addresses", [AddressController::class, 'create']);
  Route::get("/addresses", [AddressController::class, 'list']);
  Route::get("/addresses/{address}", [AddressController::class, 'find_one']);
  Route::put("/addresses/{address}", [AddressController::class, 'update_one']);
  Route::delete("/addresses/{address}", [AddressController::class, 'delete_one']);



});

