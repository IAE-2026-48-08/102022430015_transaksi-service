<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-IAE-KEY');

        if (!$apiKey || $apiKey !== config('app.iae_api_key')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized. X-IAE-KEY header is missing or invalid.',
                'errors'  => null,
            ], 401);
        }

        return $next($request);
    }
}
