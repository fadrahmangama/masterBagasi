<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    //
    public function register(Request $request){
        try{
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'password_confirmation' => ['required', 'string', 'min:8']
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'user' => $user,
                'token' => $token
            ],200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function login (Request $request){
        try{
            $request->validate([
                'email' => ['required', 'string', 'email', 'max:255'],
                'password' => ['required', 'string'],
            ]);

            $user = User::where('email', $request->email)->first();

            $attempt = $request->only('email','password');

            if (!Auth::attempt($attempt) || !$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email or Password is Incorrect'
                ], 401);
            }

            $user = $request->user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'User logged in successfully',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request){
        try{
            if (!$request->user()->tokens()){
                return response()->json([
                    'message' => 'No token found'
                ], 401);
            }

            $request->user()->tokens()->delete();
            return response()->json([
                'message' => 'User logged out successfully'
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function userProfile(Request $request)
    {
        try {
            $user = $request->user();
    
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid token or user not found',
                ], 401);
            }
    
            return response()->json([
                'status' => true,
                'message' => 'User profile retrieved successfully',
                'data' => $user
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
    
}
