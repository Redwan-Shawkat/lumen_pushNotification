<?php

namespace App\Http\Middleware;

use Closure;

class ApiTokenMiddleware
{

    public function handle($request, Closure $next)
    {
        // Pre-Middleware Action

        // TODO: Static Token
        $apiToken = env('API_TOKEN');

        if ($request->header('Authorization') !== "Bearer $apiToken") {
            return response()->json(['error' => 'Unauthroized'], 401);
        }

        $response = $next($request);

        // Post-Middleware Action

        return $response;
    }
}
