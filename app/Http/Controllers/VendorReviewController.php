<?php

namespace App\Http\Controllers;

use App\Models\VendorReview;
use Illuminate\Http\Request;

class VendorReviewController extends Controller
{
    // Get all reviews
    public function index()
    {
        $reviews = VendorReview::with(['vendor', 'client', 'booking'])->get();
        return response()->json($reviews);
    }

    // Get a single review by ID
    public function show($id)
    {
        $review = VendorReview::with(['vendor', 'client', 'booking'])->find($id);
        
        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }

        return response()->json($review);
    }

    // Create a new review
    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'client_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string',
            'is_approved' => 'nullable|boolean',
        ]);

        $review = VendorReview::create([
            'vendor_id' => $request->vendor_id,
            'client_id' => $request->client_id,
            'booking_id' => $request->booking_id,
            'rating' => $request->rating,
            'review_text' => $request->review_text,
            'is_approved' => $request->is_approved ?? true,
        ]);

        return response()->json($review, 201);
    }

    // Update a review
    public function update(Request $request, $id)
    {
        $review = VendorReview::find($id);

        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }

        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'client_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string',
            'is_approved' => 'nullable|boolean',
        ]);

        $review->update([
            'vendor_id' => $request->vendor_id,
            'client_id' => $request->client_id,
            'booking_id' => $request->booking_id,
            'rating' => $request->rating,
            'review_text' => $request->review_text,
            'is_approved' => $request->is_approved ?? true,
        ]);

        return response()->json($review);
    }

    // Delete a review
    public function destroy($id)
    {
        $review = VendorReview::find($id);

        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }

        $review->delete();
        return response()->json(['message' => 'Review deleted successfully']);
    }
}
