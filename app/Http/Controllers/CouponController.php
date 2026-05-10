<?php

namespace App\Http\Controllers;
use App\Models\Coupon;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController
{
  function create(Request $request)
  {
    $validated = $request->validate([
      "code" => [
        'required',
        'string',
        'min:2',
        'max:50',
        'alpha_dash',
        Rule::unique(Coupon::class, 'code')->where(function ($query) {
          $query->where("owner_id", Auth::id());
        })
      ],
      "percentage" => [
        "required",
        "numeric",
        "between:0,100"
      ],
      "max_usage" => [
        "required",
        "integer",
        "min:1"
      ],
      "expire_date" => [
        'required',
        'date',
        'date_format:Y-m-d',
        'after:yesterday',
      ],
      "is_active" => [
        'sometimes',
        'boolean',
      ],
    ]);

    $validated['owner_id'] = Auth::id();
    $coupon = Coupon::create($validated);
    return $coupon;
  }

  function find(Request $request)
  {
    return Coupon::where(["owner_id" => Auth::id()])->get();
  }

  function update(Request $request, Coupon $coupon)
  {
    abort_if($coupon->owner_id != Auth::id(), 403, "You are not allowed to access this coupon.");

    $validated = $request->validate([
      "code" => [
        'sometimes',
        'string',
        'min:2',
        'max:50',
        'alpha_dash',
        Rule::unique(Coupon::class, 'code')->where(
          fn($query) =>
          $query->where('owner_id', Auth::id())
        )->ignore($coupon->id)
      ],
      "percentage" => [
        "sometimes",
        "numeric",
        "between:0,100"
      ],
      "max_usage" => [
        "sometimes",
        "integer",
        "min:1"
      ],
      "expire_date" => [
        'sometimes',
        'date',
        'date_format:Y-m-d',
        'after:yesterday',
      ],
      "is_active" => [
        'sometimes',
        'boolean',
      ],
    ], [
      "code.unique" => "This coupon code already exists for your platform."
    ]);

    $coupon->update($validated);
    return $coupon;
  }

  function find_one(Coupon $coupon)
  {
    abort_if($coupon->owner_id !== Auth::id(), 403, "You are not allowed to access this coupon.");
    return $coupon;
  }

  function delete_one(Coupon $coupon)
  {
    abort_if($coupon->owner_id !== Auth::id(), 403, "You are not allowed to access this coupon.");
    $coupon->delete();
    return "coupon deleted successfully";
  }

  function delete_all()
  {
    Coupon::where(["owner_id" => Auth::id()])->delete();
    return "coupons deleted successfully";
  }

}