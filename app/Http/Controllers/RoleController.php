<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json([
            'status' => true,
            'message' => 'Get role success',
            'data'  => $roles,
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
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'error' => $validator->errors()
                ], 422);
            }
            // query insert
            $role = Role::create([
                'name' => $request->name,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Create role success',
                'data'    => $role,
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
        $role = Role::find($id);
        return response()->json([
            'status' => true,
            'message' => 'Get role by id success',
            'data'  => $role,
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
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error'
                ], 422);
            }

            $data = [
                'name' => $request->name,
            ];
            $role = Role::find($id);

            // jika user mengisi password


            $role->update($data);
            return response()->json([
                'status' => true,
                'message' => 'Update role success',
                'data'   => $role,
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
            $role = Role::destroy($id);
            return response()->json([
                'status' => true,
                'message' => 'Delete role success',
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
