<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use Illuminate\Http\Request;

class BookingRequestController extends Controller
{
    // Display a list of booking requests
    public function index()
    {
        $bookingRequests = BookingRequest::with(['event', 'vendor', 'service', 'package'])->get();
        return response()->json($bookingRequests);
    }

    // Store a new booking request
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,event_id',
            'vendor_id' => 'required|exists:vendors,id',
            'service_id' => 'required|exists:vendor_services,id',
            'package_id' => 'nullable|exists:vendor_pricing_packages,id',
            'requested_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'special_requests' => 'nullable|string',
            'estimated_price' => 'nullable|numeric',
            'status' => 'in:pending,accepted,rejected,cancelled',
            'rejection_reason' => 'nullable|string',
        ]);

        $bookingRequest = BookingRequest::create($validated);

        return response()->json($bookingRequest, 201);
    }

    // Show a specific booking request
    public function show($id)
    {
        $bookingRequest = BookingRequest::with(['event', 'vendor', 'service', 'package'])->findOrFail($id);
        return response()->json($bookingRequest);
    }

    // Update a booking request
    public function update(Request $request, $id)
    {
        $bookingRequest = BookingRequest::findOrFail($id);

        $validated = $request->validate([
            'status' => 'in:pending,accepted,rejected,cancelled',
            'rejection_reason' => 'nullable|string',
            'special_requests' => 'nullable|string',
            'estimated_price' => 'nullable|numeric',
            'requested_date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
        ]);

        $bookingRequest->update($validated);

        return response()->json($bookingRequest);
    }

    // Delete a booking request
    public function destroy($id)
    {
        $bookingRequest = BookingRequest::findOrFail($id);
        $bookingRequest->delete();

        return response()->json(['message' => 'Booking request deleted']);
    }
}
