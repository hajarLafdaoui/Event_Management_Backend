<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Vendor\Vendor;
use App\Models\Vendor\VendorAvailability;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class VendorAvailabilityController extends Controller
{
    /**
     * Get all availabilities for a vendor
     */
    public function index($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);

        $availabilities = $vendor->availabilities()
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $availabilities
        ]);
    }

    /**
     * Create new availability slot
     */
    public function store(Request $request, $vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);

        $validator = Validator::make($request->all(), [
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_available' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['is_available'] = $data['is_available'] ?? true;

        // Check for time slot conflicts
        if ($this->hasTimeConflict($vendor, $data)) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot conflicts with an existing availability'
            ], 409);
        }

        $availability = $vendor->availabilities()->create($data);

        return response()->json([
            'success' => true,
            'data' => $availability,
            'message' => 'Availability slot created'
        ], 201);
    }

    /**
     * Update availability slot
     */
    public function update(Request $request, $vendorId, $availabilityId)
    {
        $availability = VendorAvailability::where('vendor_id', $vendorId)
            ->findOrFail($availabilityId);

        $validator = Validator::make($request->all(), [
            'date' => 'sometimes|date|after_or_equal:today',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'is_available' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Check for time slot conflicts excluding current slot
        if (isset($data['date']) || isset($data['start_time']) || isset($data['end_time'])) {
            $checkData = array_merge($availability->toArray(), $data);
            if ($this->hasTimeConflict($availability->vendor, $checkData, $availabilityId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This time slot conflicts with an existing availability'
                ], 409);
            }
        }

        $availability->update($data);

        return response()->json([
            'success' => true,
            'data' => $availability,
            'message' => 'Availability updated'
        ]);
    }

    /**
     * Delete availability slot
     */
    public function destroy($vendorId, $availabilityId)
    {
        $availability = VendorAvailability::where('vendor_id', $vendorId)
            ->findOrFail($availabilityId);

        $availability->delete();

        return response()->json([
            'success' => true,
            'message' => 'Availability slot deleted'
        ]);
    }

    /**
     * Bulk create/update availabilities
     */
    public function bulkUpdate(Request $request, $vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);

        $validator = Validator::make($request->all(), [
            'availabilities' => 'required|array',
            'availabilities.*.date' => 'required|date|after_or_equal:today',
            'availabilities.*.start_time' => 'required|date_format:H:i',
            'availabilities.*.end_time' => 'required|date_format:H:i|after:availabilities.*.start_time',
            'availabilities.*.is_available' => 'sometimes|boolean',
            'clear_existing' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Delete existing availabilities if requested
        if ($request->clear_existing) {
            $vendor->availabilities()->delete();
        }

        $created = [];
        foreach ($request->availabilities as $slot) {
            // Check for conflicts for each slot
            if (!$this->hasTimeConflict($vendor, $slot)) {
                $created[] = $vendor->availabilities()->create([
                    'date' => $slot['date'],
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'is_available' => $slot['is_available'] ?? true
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $created,
            'message' => count($created) . ' availability slots created'
        ]);
    }

    /**
     * Check for time slot conflicts
     */
    private function hasTimeConflict(Vendor $vendor, array $data, $excludeId = null)
    {
        $query = $vendor->availabilities()
            ->where('date', $data['date'])
            ->where(function($q) use ($data) {
                $q->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                  ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                  ->orWhere(function($inner) use ($data) {
                      $inner->where('start_time', '<=', $data['start_time'])
                            ->where('end_time', '>=', $data['end_time']);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}