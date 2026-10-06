<?php

namespace App\Http\Middleware;

use App\Models\UserIntegrasi;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateIntegrasiApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            $header = $request->header('Authorization', '');
            if (stripos($header, 'Bearer ') === 0) {
                $token = trim(substr($header, 7));
            }
        }

        if (! $token) {
            return response()->json([
                'status' => false,
                'message' => 'Bearer token diperlukan',
                'data' => null,
            ], 401);
        }

        $user = UserIntegrasi::where('access_token', $token)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

        if (! $user || ! $user->isAccessTokenValid($token)) {
            return response()->json([
                'status' => false,
                'message' => 'Token tidak valid atau sudah kadaluarsa',
                'data' => null,
            ], 401);
        }

        $request->attributes->set('user_integrasi', $user);
        $request->attributes->set('id_integration', $user->id);

        return $next($request);
    }
}
