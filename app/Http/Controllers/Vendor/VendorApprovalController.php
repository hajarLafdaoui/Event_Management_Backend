<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Vendor\Vendor;
use App\Models\Vendor\VendorApproval;
use Illuminate\Support\Facades\Validator;

class VendorApprovalController extends Controller
{
    /**
     * Get all vendor approvals (admin only)
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        
        $approvals = VendorApproval::with(['vendor', 'admin'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $approvals->items(),
            'meta' => [
                'total' => $approvals->total(),
                'per_page' => $approvals->perPage(),
                'current_page' => $approvals->currentPage(),
            ]
        ]);
    }

    /**
     * Get pending vendor approvals (admin only)
     */
    public function pending(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        
        $pendingVendors = Vendor::where('status', 'pending')
            ->with(['user', 'category'])
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $pendingVendors->items(),
            'meta' => [
                'total' => $pendingVendors->total(),
                'per_page' => $pendingVendors->perPage(),
                'current_page' => $pendingVendors->currentPage(),
            ]
        ]);
    }

    /**
     * Get approval history for a specific vendor
     */
    public function show($vendorId)
    {
        $vendor = Vendor::with(['approvals.admin'])->find($vendorId);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $vendor->approvals
        ]);
    }

    /**
     * Approve/Reject a vendor (admin only)
     */
    public function store(Request $request, $vendorId)
    {
        $vendor = Vendor::find($vendorId);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'action' => 'required|in:approved,rejected',
            'notes' => 'nullable|string',
            'rejection_reason' => 'required_if:action,rejected|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Update vendor status and rejection reason
        $vendor->status = $request->action;
        
        if ($request->action === 'rejected') {
            $vendor->rejection_reason = $request->rejection_reason;
        } else {
            $vendor->rejection_reason = null; // Clear rejection reason if approving
        }
        
        $vendor->save();

        // Create approval record
        $approval = VendorApproval::create([
            'vendor_id' => $vendor->id,
            'admin_id' => auth()->id(),
            'action' => $request->action,
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'approval' => $approval,
                'vendor' => $vendor->fresh()
            ],
            'message' => 'Vendor has been ' . $request->action
        ]);
    }
}