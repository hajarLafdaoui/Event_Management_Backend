<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\Models\Vendor\Vendor;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Vendor\VendorApprovalController;

class VendorController extends Controller
{
    /**
     * Get all vendors with pagination
     */
    public function getVendors(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $vendors = Vendor::with(['category', 'user'])
                    ->orderBy('business_name')
                    ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $vendors->items(),
            'meta' => [
                'total' => $vendors->total(),
                'per_page' => $vendors->perPage(),
                'current_page' => $vendors->currentPage(),
            ]
        ]);
    }

    /**
     * Create new vendor
     */
    public function createVendor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'vendor_category_id' => 'required|exists:vendor_categories,id',
            'business_name' => 'required|string|max:255',
            'description' => 'required|string',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string',
            'country' => 'required|string', // Added based on migration
            'city' => 'required|string',    // Added based on migration
            'street_address' => 'required|string', // Added based on migration
            'website' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $vendor = Vendor::create(array_merge(
            $validator->validated(),
            ['status' => 'pending']
        ));

        return response()->json([
            'success' => true,
            'data' => $vendor,
            'message' => 'Vendor created successfully'
        ], 201);
    }

    /**
     * Get single vendor
     */
    public function getVendor($id)
    {
        $vendor = Vendor::with(['category', 'user', 'portfolios', 'services'])->find($id);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $vendor
        ]);
    }

    /**
     * Update vendor
     */
    public function updateVendor(Request $request, $id)
    {
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'vendor_category_id' => 'exists:vendor_categories,id',
            'business_name' => 'string|max:255',
            'description' => 'string',
            'contact_email' => 'email',
            'contact_phone' => 'string',
            'country' => 'string',  // Added based on migration
            'city' => 'string',     // Added based on migration
            'street_address' => 'string', // Added based on migration
            'website' => 'nullable|url',
            'status' => 'in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string|required_if:status,rejected'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $vendor->update($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $vendor,
            'message' => 'Vendor updated successfully'
        ]);
    }

    /**
     * Delete vendor
     */
    public function deleteVendor($id)
    {
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        // Prevent deletion if vendor has relationships
        if ($vendor->services()->exists() || $vendor->portfolios()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete vendor with associated records'
            ], 422);
        }

        $vendor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vendor deleted successfully'
        ]);
    }
}
