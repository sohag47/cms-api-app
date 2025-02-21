<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Carbon;


class EnsureTokenHasNotExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expirationMinutes = config('sanctum.expiration');
        if ($expirationMinutes) {
            $token = $request->user()->currentAccessToken();
            $tokenCreationTime = $token->created_at;

            if (Carbon::parse($tokenCreationTime)->addMinutes($expirationMinutes)->isPast()) {
                $token->delete();
                return response()->json(['message' => 'Token has expired'], 401);
            }
        }
        return $next($request);
    }
}
