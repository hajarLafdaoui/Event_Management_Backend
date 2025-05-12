<?php

namespace App\Http\Controllers;

use App\Models\VendorPayment;
use App\Models\BookingRequest;
use App\Models\User;
use App\Models\Vendor\Vendor;
use Illuminate\Http\Request;

class VendorPaymentController extends Controller
{
    public function index()
    {
        // Get all vendor payments with related models
        $vendorPayments = VendorPayment::with(['bookingRequest', 'client', 'vendor'])->get();
        return response()->json($vendorPayments);
    }

    public function store(Request $request)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'booking_id' => 'required|exists:booking_requests,booking_id',
            'client_id' => 'required|exists:users,id',
            'vendor_id' => 'required|exists:vendors,vendor_id',
            'amount' => 'required|numeric',
            'payment_method' => 'required|in:stripe,paypal',
            'transaction_id' => 'nullable|string|max:255',
            'payment_status' => 'required|in:pending,processing,completed,failed,refunded',
            'payment_date' => 'nullable|date',
        ]);

        // Create the new vendor payment
        $payment = VendorPayment::create($validated);

        // Return the newly created payment
        return response()->json($payment, 201);
    }

    public function show($payment_id)
    {
        // Find the vendor payment with related models
        $payment = VendorPayment::with(['bookingRequest', 'client', 'vendor'])->findOrFail($payment_id);
        return response()->json($payment);
    }

    public function update(Request $request, $payment_id)
    {
        // Find the vendor payment and update it
        $payment = VendorPayment::findOrFail($payment_id);
        // Validate the incoming data
        $validated = $request->validate([
            'booking_id' => 'required|exists:booking_requests,booking_id',
            'client_id' => 'required|exists:users,id',
            'vendor_id' => 'required|exists:vendors,vendor_id',
            'amount' => 'required|numeric',
            'payment_method' => 'required|in:stripe,paypal',
            'transaction_id' => 'nullable|string|max:255',
            'payment_status' => 'required|in:pending,processing,completed,failed,refunded',
            'payment_date' => 'nullable|date',
        ]);

        
        $payment->update($validated);

        // Return the updated payment
        return response()->json($payment);
    }
    public function destroy($payment_id)
    {
        // Find the vendor payment and delete it
        $payment = VendorPayment::findOrFail($payment_id);
        $payment->delete();

        // Return success response
        return response()->json(['message' => 'Payment deleted successfully']);
    }
}
