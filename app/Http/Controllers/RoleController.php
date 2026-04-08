<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseHelper;

class RoleController extends Controller
{
    public function index(){
        $roles = Role::all();
        return response()->json([
            'status' => true,
            'message' => 'Get user success',
            'data' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'error' => $validator->errors()
                ], 422);
            }

            //query insert
            $role = Role::create([
                'name' => $request->name,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Success',
                'data' => $role,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Internal server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        $role = Role::find($id);
        return response()->json([
            'status' => true,
            'message' => 'Get user by id success',
            'data' => $role,
        ]);
    }

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
            ];
            $user = Role::find($id);

            //jika user mengisi password
            if ($request->password) {
                $data['password'] = $request->password;
            } else {
                $data['password'] = $user->password;
            }

            $user = Role::find($id);
            $user->update($data);
            return response()->json([
                'status' => true,
                'message' => 'Update user success',
                'data' => $user,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Internal server error',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $role = Role::destroy($id);
            return response()->json([
                'status' => true,
                'message' => 'Delete user success'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Internal server error',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
