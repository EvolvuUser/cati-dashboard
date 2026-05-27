<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-CATI-KEY');

        // check if header exists
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key missing'
            ], 401);
        }

        // optional: validate key
        if ($apiKey !== env('CUSTOM_API_KEY')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key'
            ], 403);
        }

        return $next($request);
    }
}
