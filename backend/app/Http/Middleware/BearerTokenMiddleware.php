<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class BearerTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Token não fornecido.'], 401);
        }

        $user = Cache::get("auth_token:{$token}");

        if (! $user) {
            return response()->json(['message' => 'Token inválido ou expirado.'], 401);
        }

        $request->attributes->set('auth_user', $user);

        return $next($request);
    }
}
