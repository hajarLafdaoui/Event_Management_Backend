<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\Models\Vendor\VendorService;
use App\Http\Controllers\Controller;
use App\Models\Vendor\VendorPricingPackage;
use Illuminate\Support\Facades\Validator;

class VendorPricingPackageController extends Controller
{
    /**
     * Get all pricing packages for a service
     */
    public function getPricingPackages($vendorId, $serviceId)
    {
        $service = VendorService::where('vendor_id', $vendorId)
                        ->find($serviceId);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        $packages = $service->pricingPackages;

        return response()->json([
            'success' => true,
            'data' => $packages
        ]);
    }

    /**
     * Create a new pricing package
     */
    public function createPricingPackage(Request $request, $vendorId, $serviceId)
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
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'features' => 'nullable|array',
            'features.*' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $package = $service->pricingPackages()->create([
            'name' => $request->name,
            'price' => $request->price,
            'features' => $request->features
        ]);

        return response()->json([
            'success' => true,
            'data' => $package,
            'message' => 'Pricing package created successfully'
        ], 201);
    }

    /**
     * Get a single pricing package
     */
    public function getPricingPackage($vendorId, $serviceId, $packageId)
    {
        $package = VendorPricingPackage::where('vendor_service_id', $serviceId)
                        ->find($packageId);

        if (!$package) {
            return response()->json([
                'success' => false,
                'message' => 'Pricing package not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $package
        ]);
    }

    /**
     * Update a pricing package
     */
    public function updatePricingPackage(Request $request, $vendorId, $serviceId, $packageId)
    {
        $package = VendorPricingPackage::where('vendor_service_id', $serviceId)
                        ->find($packageId);

        if (!$package) {
            return response()->json([
                'success' => false,
                'message' => 'Pricing package not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'features' => 'nullable|array',
            'features.*' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $package->update($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $package,
            'message' => 'Pricing package updated successfully'
        ]);
    }

    /**
     * Delete a pricing package
     */
    public function deletePricingPackage($vendorId, $serviceId, $packageId)
    {
        $package = VendorPricingPackage::where('vendor_service_id', $serviceId)
                        ->find($packageId);

        if (!$package) {
            return response()->json([
                'success' => false,
                'message' => 'Pricing package not found'
            ], 404);
        }

        $package->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pricing package deleted successfully'
        ]);
    }
}