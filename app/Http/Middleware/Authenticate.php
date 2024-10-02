<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Login required',
            ], 500);
        }
    }


    protected function unauthenticated($request, array $guards)
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Login required',
        ], 401);
    }
}
