<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ResponseHelper;

class AuthController extends Controller
{
    public function registration(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6|confirmed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            // query insert
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Registration success',
                'data'    => $user,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Internal server error',
                'error'  => $th->getMessage() //tdak boleh dimunculkan di prod
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6'
            ]);

            if ($validator->fails()) {
                return ResponseHelper::error('Validation error', $validator->errors(), 422);
            }

            $user = User::where('email', $request->email)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {

                return ResponseHelper::error('Invalid Credential!!', '', 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => true,
                'message' => 'Login success',
                'data'  => $user,
                'token' =>  $token,
            ]);
        } catch (\Throwable $th) {
            return ResponseHelper::error('Internal server error', $th->getMessage(),  500);
        }
    }
}
