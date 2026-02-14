<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API Key is required',
            ], 401);
        }

        $validKey = ApiKey::where('api_key', $apiKey)
            ->where('status', true)
            ->first();

        if (!$validKey) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive API Key',
            ], 401);
        }

        return $next($request);
    }
}
