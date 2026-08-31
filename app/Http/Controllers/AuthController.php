<?php

namespace App\Http\Controllers;

use App\Constants\UserRole;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
  function register(RegisterRequest $request)
  {
    $validated = $request->validated();

    $role = match (strtolower($validated['role'] ?? "user")) {
      "admin" => UserRole::ADMIN,
      "delivery" => UserRole::DELIVERY,
      "seller" => UserRole::SELLER,
      default => UserRole::USER,
    };

    $user = User::create([
      "first_name" => $validated["first_name"],
      "last_name" => $validated["last_name"],
      "email" => $validated["email"],
      "password" => Hash::make($validated["password"]),
      "role" => $role,
      "password_confirmation" => Hash::make($validated["password"])
    ]);

    return response()->json([
      'success' => true,
      'user' => $user
    ], 201);
  }

  function login(LoginRequest $request)
  {

    $credentials = $request->validated();

    if (!Auth::attempt($credentials)) {
      return response()->json([
        'message' => 'Email or password is incorrect'
      ], 401);
    }

    $user = Auth::user();
    $token = $user->createToken("auth_token")->plainTextToken;

    return response()->json([
      "access_token" => $token,
      "token_type" => "Bearer",
      "user" => $user
    ]);
  }

  function logout(Request $request){
    $request->user()->currentAccessToken()->delete();
    return response()->json(["message" => "logged out successfully"]);
  }

}




