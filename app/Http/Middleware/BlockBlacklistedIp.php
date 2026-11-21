<?php

namespace App\Http\Middleware;

use App\Models\IpBlacklist;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockBlacklistedIp
{
    /**
     * Block incoming requests from actively blacklisted IP addresses.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $match = IpBlacklist::findActiveMatch($request->ip());

        if ($match) {
            IpBlacklist::recordHit((int) $match->id);

            $message = __('Your IP address (:ip) has been blocked.', ['ip' => $request->ip()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'reason' => $match->reason,
                ], 403);
            }

            abort(403, $message);
        }

        return $next($request);
    }
}
