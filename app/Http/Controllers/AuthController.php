<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'bail|required|email',
                'password' => 'required',
            ]);
        } catch (ValidationException $e) {
            // Validation failed
            $errors = $e->validator->errors()->toArray();

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $errors,
            ], 422);
        }

        $credentials = request(['email', 'password']);

        if (!auth()->attempt($credentials)) {
            return response()->json([
                "message" => "Invalid Credentials",
                'errors' => [
                    'email/password' => [
                        'The provided credentials are incorrect.'
                    ]
                ]
            ], status: 422);
        }

        $user = User::where('email', $request->email)->first();
        $authToken = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Successfully logged in',
            'access_token' => $authToken,
        ]);
    }

    public function logout(Request $request)
    {
        $token = PersonalAccessToken::findToken($request->bearerToken());

        $token->delete();

        return response()->json(
            [
                'message' => 'Successfully logged out',
            ],
        );
    }
}
