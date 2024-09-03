<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response->getStatusCode() === 200) {
            $status = 'SUCCESS';
        } else {
            $status = 'ERROR';
        }

        $data = $response->original;

        return response()->json([
            'data' => $data['data'] ?? $data,
            'status' => $status,
            'message' => $data['message'] ?? '',
        ], $response->getStatusCode());
    }
}
