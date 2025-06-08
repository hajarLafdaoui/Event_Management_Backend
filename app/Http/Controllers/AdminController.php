<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    // Get all users (paginated)
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $users = User::withTrashed()
                    ->where('id', '!=', auth()->id()) // Exclude current user
                    ->paginate($perPage);
    
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
        'first_name' => 'nullable|string|max:100',
        'last_name' => 'nullable|string|max:100',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'phone' => 'nullable|string|max:20',
        'role' => 'required|in:client,vendor,admin',
        'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
        'address' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:100',
        'country' => 'nullable|string|max:100',
        'facebook_url' => 'nullable|url|max:255',
        'instagram_url' => 'nullable|url|max:255',
        'tiktok_url' => 'nullable|url|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // Update only the specified fields
    $user->update($request->only([
        'first_name', 'last_name', 'email', 'phone', 'role',
        'gender', 'address', 'city', 'country',
        'facebook_url', 'instagram_url', 'tiktok_url'
    ]));

    return response()->json([
        'message' => 'User updated successfully',
        'user' => $user->fresh()
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
