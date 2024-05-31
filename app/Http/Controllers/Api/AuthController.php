<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            // check validation
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|max:255|unique:users',
                'password' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            // insert into db
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'data' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]);

        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }

    public function login(Request $request)
    {
        try {
            // check validation
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|max:255',
                'password' => 'required|string'
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            // check authenticated user or not
            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'message' => 'User not found'
                ], 401);
            }

            /* If successfully validated as a registered user then we will generate a token, 
            but if it fails then we will give the response “user not found”*/
            $user = User::where('email', $request->email)->firstOrFail();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login success',
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]);

        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }

    public function logout()
    {
        try{
            Auth::user()->tokens()->delete();
            return response()->json([
                'message'=>'Logout successful'
            ]);
        }catch(\Throwable $th){
            return response()->json($th->getMessage());
        }
    }
}
