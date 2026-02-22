<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed

    {
        // if (!$request->user() || !in_array($request->user()->role->value, $roles)) {
        //     return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        // }
        return $next($request);
    }
}
