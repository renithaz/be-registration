<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseHelper;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('role')->get();
        return response()->json([
            'status' => true,
            'message' => 'Get user success',
            'data'  => $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'error' => $validator->errors()
                ], 422);
            }
            // query insert
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'role_id' => $request->role_id
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        return response()->json([
            'status' => true,
            'message' => 'Get user by id success',
            'data'  => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email,' . $id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error'
                ], 422);
            }

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'role_id' => $request->role_id
            ];
            $user = User::find($id);

            // jika user mengisi password
            if ($request->filled('password')) {
                $data['password'] = $request->password;
            } else {
                $data['password'] = $user->password;
            }


            $user->update($data);
            return response()->json([
                'status' => true,
                'message' => 'Update user success',
                'data'   => $user,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Internal server error',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::destroy($id);
            return response()->json([
                'status' => true,
                'message' => 'Delete user success',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Internal server error',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }
}
