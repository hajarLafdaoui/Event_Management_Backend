<?php

namespace App\Http\Controllers\vendor;

use Illuminate\Http\Request;
use App\Models\Vendor\Vendor;
use App\Http\Controllers\Controller;
use App\Models\Vendor\VendorPortfolio;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class VendorPortfolioController extends Controller
{
    /**
     * Retrieves all portfolio items for a specific vendor
     */
    public function getVendorPortfolios($vendorId)
    {
        $vendor = Vendor::find($vendorId);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        $portfolios = $vendor->portfolios()->orderBy('created_at', 'desc')->get();

        // Convert storage paths to full URLs
        $portfolios->transform(function ($item) {
            if ($item->type === 'image' && $item->url) {
                $item->url = Storage::disk('public')->url($item->url);
            }
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $portfolios
        ]);
    }

    /**
     * Create new portfolio item
     */
    public function createPortfolioItem(Request $request, $vendorId)
    {
        $vendor = Vendor::find($vendorId);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:image,video',
            'file' => $request->type === 'image' ? 'required|string' : 'required|string', // Base64 for image, URL for video
            'caption' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = [
            'type' => $request->type,
            'caption' => $request->caption,
        ];

        // Handle file upload based on type
        if ($request->type === 'image') {
            $data['url'] = $this->handleBase64Image($request->file);
        } else {
            // For videos, we'll just store the URL
            $data['url'] = $request->file;
        }

        $portfolio = $vendor->portfolios()->create($data);

        // Convert storage path to full URL for response
        if ($portfolio->type === 'image') {
            $portfolio->url = Storage::disk('public')->url($portfolio->url);
        }

        return response()->json([
            'success' => true,
            'data' => $portfolio,
            'message' => 'Portfolio item created successfully'
        ], 201);
    }

    /**
     * Get single portfolio item
     */
    public function getPortfolioItem($vendorId, $portfolioId)
    {
        $portfolio = VendorPortfolio::where('vendor_id', $vendorId)
                        ->find($portfolioId);

        if (!$portfolio) {
            return response()->json([
                'success' => false,
                'message' => 'Portfolio item not found'
            ], 404);
        }

        // Convert storage path to full URL if it's an image
        if ($portfolio->type === 'image' && $portfolio->url) {
            $portfolio->url = Storage::disk('public')->url($portfolio->url);
        }

        return response()->json([
            'success' => true,
            'data' => $portfolio
        ]);
    }

    /**
     * Update portfolio item
     */
    public function updatePortfolioItem(Request $request, $vendorId, $portfolioId)
    {
        $portfolio = VendorPortfolio::where('vendor_id', $vendorId)
                        ->find($portfolioId);

        if (!$portfolio) {
            return response()->json([
                'success' => false,
                'message' => 'Portfolio item not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'sometimes|in:image,video',
            'file' => 'sometimes|string', // Can be base64 image or video URL
            'caption' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['caption']);

        if ($request->has('type')) {
            $data['type'] = $request->type;
        }

        // Handle file update if provided
        if ($request->has('file')) {
            if ($request->type === 'image' || $portfolio->type === 'image') {
                // Delete old image if exists
                if ($portfolio->url && Storage::disk('public')->exists($portfolio->url)) {
                    Storage::disk('public')->delete($portfolio->url);
                }
                $data['url'] = $this->handleBase64Image($request->file);
            } else {
                // For videos, just update the URL
                $data['url'] = $request->file;
            }
        }

        $portfolio->update($data);

        // Convert storage path to full URL if it's an image
        if ($portfolio->type === 'image' && $portfolio->url) {
            $portfolio->url = Storage::disk('public')->url($portfolio->url);
        }

        return response()->json([
            'success' => true,
            'data' => $portfolio,
            'message' => 'Portfolio item updated successfully'
        ]);
    }

    /**
     * Delete portfolio item
     */
    public function deletePortfolioItem($vendorId, $portfolioId)
    {
        $portfolio = VendorPortfolio::where('vendor_id', $vendorId)
                        ->find($portfolioId);

        if (!$portfolio) {
            return response()->json([
                'success' => false,
                'message' => 'Portfolio item not found'
            ], 404);
        }

        // Delete associated file if it's an image
        if ($portfolio->type === 'image' && $portfolio->url && Storage::disk('public')->exists($portfolio->url)) {
            Storage::disk('public')->delete($portfolio->url);
        }

        $portfolio->delete();

        return response()->json([
            'success' => true,
            'message' => 'Portfolio item deleted successfully'
        ]);
    }

    /**
     * Handle base64 image upload
     */
    private function handleBase64Image($base64Image, $oldImagePath = null)
    {
        if (!$base64Image) {
            return null;
        }

        // Decode the base64 image
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));

        // Generate a unique filename
        $extension = 'png'; 
        $filename = 'portfolio_images/' . uniqid() . '.' . $extension;

        // Save the image to storage
        Storage::disk('public')->put($filename, $imageData);

        // Delete the old image if it exists
        if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return $filename;
    }
}