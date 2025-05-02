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
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:100',
            'state' => 'sometimes|string|max:100',
            'country' => 'sometimes|string|max:100',
            'postal_code' => 'sometimes|string|max:20',
            'role' => 'sometimes|in:client,vendor,admin',
            'is_active' => 'sometimes|boolean',
            'email_verified_at' => 'sometimes|nullable|date', // Admin can manually verify email
            'password' => 'sometimes|string|min:8|confirmed', // Admin can reset password
            'date_of_birth' => 'sometimes|nullable|date',
            'gender' => 'sometimes|nullable|in:male,female,other',
            'profile_picture' => 'sometimes|nullable|string', // URL to image
            'company_name' => 'sometimes|nullable|string|max:100', // For vendors
            'tax_id' => 'sometimes|nullable|string|max:50', // For vendors
            'website' => 'sometimes|nullable|url|max:255',
            'notes' => 'sometimes|nullable|string', // Admin notes about user
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Handle password update separately to hash it
        $data = $request->except('password');
        if ($request->has('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

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

// {
//     "first_name": "Jane",
//     "last_name": "Smith",
//     "email": "jane.smith@example.com",
//     "phone": "+1987654321",
//     "address": "456 Oak Ave",
//     "city": "Los Angeles",
//     "state": "CA",
//     "country": "USA",
//     "postal_code": "90001",
//     "role": "vendor",
//     "is_active": true,
//     "date_of_birth": "1990-05-15",
//     "gender": "female",
//     "profile_picture": "https://example.com/profiles/jane.jpg",
//     "company_name": "Smith Enterprises",
//     "tax_id": "TAX123456",
//     "website": "https://smith-enterprises.com",
//     "notes": "VIP client - prefers email communication"
// }