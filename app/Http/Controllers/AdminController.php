<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'admin']);
    }

    // Get all users (paginated)
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $users = User::withTrashed()->paginate($perPage);

        return response()->json($users);
    }

    // Get a specific user
    public function show($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        return response()->json($user);
    }

    // Update a user
    public function update(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'role' => 'sometimes|in:client,vendor,admin',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user->update($request->all());

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    // Soft delete a user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    // Restore a soft-deleted user
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return response()->json(['message' => 'User restored successfully']);
    }

    // Permanently delete a user
    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();

        return response()->json(['message' => 'User permanently deleted']);
    }
}