<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiToken
{
    /**
     * Authenticate incoming API requests using a Bearer token from `api_tokens`.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, ?string $ability = null): Response
    {
        $bearer = (string) $request->bearerToken();
        $apiToken = ApiToken::findValidToken($bearer);

        if (! $apiToken || ! $apiToken->user) {
            return response()->json([
                'message' => __('Unauthenticated. Invalid or expired API token.'),
            ], 401);
        }

        if ($ability !== null && ! $apiToken->can($ability)) {
            return response()->json([
                'message' => __('Forbidden. Token lacks required ability: :ability.', ['ability' => $ability]),
            ], 403);
        }

        $apiToken->forceFill([
            'last_used_at' => now(),
            'last_used_ip' => $request->ip(),
        ])->save();

        $request->setUserResolver(static fn () => $apiToken->user);
        $request->attributes->set('api_token', $apiToken);

        return $next($request);
    }
}
