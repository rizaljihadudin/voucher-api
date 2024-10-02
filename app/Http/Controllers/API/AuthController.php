<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $request->validate([
            'name'      => 'required|string',
            'email'     => 'required|string|email:rfc,dns|max:255|unique:users',
            'password'  => 'required|string|min:6|max:255',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $token =  auth()->login($user);

            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully!',
                'data' => [
                    'user' => $user,
                    'access_token' => [
                        'token' => $token,
                        'type' => 'Bearer',
                    ],
                ],
            ], 200);
        } catch (\Throwable $th) {
           return response()->json([
               'status' => 'error',
               'message' => 'Registration failed: ' . $th->getMessage(),
           ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $token = Auth::attempt([
            'email' => $request['email'],
            'password' => $request['password'],
        ]);

        if ($token){
            return response()->json([
                'status' => 'success',
                'message' => 'User logged in successfully.',
                'data' => [
                    'user' => auth()->user(),
                    'access_token' => [
                        'token' => $token,
                        'type' => 'Bearer',
                    ],
                ],
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials.',
        ], 401);
    }

    public function logout()
    {
        $token = JWTAuth::getToken();
        $invalidate = JWTAuth::invalidate($token);
        if($invalidate) {
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged out',
                'data' => [],
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to logout',
            'data' => [],
        ], 500);
    }
}
