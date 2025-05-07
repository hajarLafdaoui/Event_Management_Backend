<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\Models\Vendor\Vendor;
use App\Http\Controllers\Controller;
use App\Models\Vendor\VendorService;
use Illuminate\Support\Facades\Validator;

class VendorServiceController extends Controller
{
    /**
     * Get all services for a vendor
     */
    public function getVendorServices($vendorId)
    {
        $vendor = Vendor::find($vendorId);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        $services = $vendor->services()->with('pricingPackages')->get();

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    /**
     * Create a new service for a vendor
     */
    public function createVendorService(Request $request, $vendorId)
    {
        $vendor = Vendor::find($vendorId);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $service = $vendor->services()->create($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $service,
            'message' => 'Service created successfully'
        ], 201);
    }

    /**
     * Get a single service
     */
    public function getVendorService($vendorId, $serviceId)
    {
        $service = VendorService::where('vendor_id', $vendorId)
                        ->with('pricingPackages')
                        ->find($serviceId);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $service
        ]);
    }

    /**
     * Update a service
     */
    public function updateVendorService(Request $request, $vendorId, $serviceId)
    {
        $service = VendorService::where('vendor_id', $vendorId)
                        ->find($serviceId);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $service->update($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $service,
            'message' => 'Service updated successfully'
        ]);
    }

    /**
     * Delete a service
     */
    public function deleteVendorService($vendorId, $serviceId)
    {
        $service = VendorService::where('vendor_id', $vendorId)
                        ->find($serviceId);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        // Check if there are pricing packages associated
        if ($service->pricingPackages()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete service with associated pricing packages'
            ], 422);
        }

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully'
        ]);
    }
}