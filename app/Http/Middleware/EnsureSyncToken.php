<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSyncToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $serverToken = config('app.sync_token', env('SYNC_TOKEN'));
        $clientToken = $request->header('X-Sync-Token') ?? $request->input('token');

        if (!$serverToken || !$clientToken || $serverToken !== $clientToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid Sync Token',
            ], 401);
        }

        return $next($request);
    }
}
