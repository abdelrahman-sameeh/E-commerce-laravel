<?php

namespace App\Http\Middleware;

use App\Constants\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;



class Roles
{
    private static $allowed_roles = ['admin', 'user', 'owner', 'delivery'];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$selected_roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }


        foreach ($selected_roles as $role) {
            if (!in_array(strtolower($role), self::$allowed_roles)) {
                return response()->json([
                    "message" => "Invalid role {$role}"
                ]);
            }
        }

        $user_role_id = $user->role;
        $user_role_name = UserRole::$roles[$user_role_id];
        if (!in_array($user_role_name, $selected_roles)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
