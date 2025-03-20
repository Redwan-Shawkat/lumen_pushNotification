<?php

namespace App\Http\Middleware;

use Closure;

class StaticTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Pre-Middleware Action

        // TODO: Static Token
        $staticToken = env('STATIC_API_TOKEN');

        if ($request->header('Authorization') !== "Bearer $staticToken") {
            return response()->json(['error' => 'Unauthroized'], 401);
        }

        $response = $next($request);

        // Post-Middleware Action

        return $response;
    }
}
