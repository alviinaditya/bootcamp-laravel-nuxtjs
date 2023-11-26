<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Validate Request
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            // Find user by email
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error('Unautorized', 404);
            }

            $user = User::where('email', $request->email)->first();

            if (!Hash::check($request->password, $user->password)) {
                throw new Exception('Invalid Password');
            }

            // Generate Token
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            // Return Response
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Login Success');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage());
        }
    }

    public function register(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', new Password]
            ]);

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            // Generate token
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            // Return response
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Register Success');
        } catch (Exception $e) {
            // Return error response
            return ResponseFormatter::error($e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        // Revoke Token
        $token = $request->user()->currentAccessToken()->delete();

        // Return Response
        return ResponseFormatter::success($token, 'Logout Success');
    }

    public function fetch(Request $request)
    {
        // Get User
        $user = $request->user();

        // Return Response
        return ResponseFormatter::success($user, 'Fetching user data success');
    }
}
