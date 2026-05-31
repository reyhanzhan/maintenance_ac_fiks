<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $plainToken = $request->bearerToken();

        if (!$plainToken) {
            return response()->json(['message' => 'Token tidak ditemukan.'], 401);
        }

        $accessToken = ApiToken::with('user')
            ->where('token_hash', hash('sha256', $plainToken))
            ->first();

        if (!$accessToken || !$accessToken->user) {
            return response()->json(['message' => 'Token tidak valid.'], 401);
        }

        if ($roles && !in_array($accessToken->user->role, $roles, true)) {
            return response()->json(['message' => 'Akses ditolak untuk role ini.'], 403);
        }

        $accessToken->forceFill(['last_used_at' => now()])->save();

        $request->setUserResolver(fn () => $accessToken->user);
        $request->attributes->set('api_token', $accessToken);

        return $next($request);
    }
}
