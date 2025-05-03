<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Vendor\VendorCategory;
use App\Http\Controllers\Controller;

class VendorCategoryController extends Controller
{
    /**
     * Get all vendor categories (with optional pagination)
     */
    public function getVendorCategories(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $categories = VendorCategory::orderBy('name')
                        ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $categories->items(),
            'meta' => [
                'total' => $categories->total(),
                'per_page' => $categories->perPage(),
                'current_page' => $categories->currentPage(),
            ]
        ]);
    }

    /**
     * Create new vendor category
     */
    public function createVendorCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:vendor_categories',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $category = VendorCategory::create($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Vendor category created successfully'
        ], 201);
    }

    /**
     * Get single vendor category
     */
    public function getVendorCategory($id)
    {
        $category = VendorCategory::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor category not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Update vendor category
     */
    public function updateVendorCategory(Request $request, $id)
    {
        $category = VendorCategory::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor category not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:vendor_categories,name,'.$id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $category->update($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Vendor category updated successfully'
        ]);
    }

    /**
     * Delete vendor category
     */
    public function deleteVendorCategory($id)
    {
        $category = VendorCategory::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor category not found'
            ], 404);
        }

        if ($category->vendors()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with associated vendors'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vendor category deleted successfully'
        ]);
    }
}