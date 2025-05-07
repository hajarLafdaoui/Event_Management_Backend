<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Vendor\Vendor;
use App\Models\Vendor\VendorApproval;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class VendorApprovalController extends Controller
{
    /**
     * Get all approval history for a vendor
     */
    public function index($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);

        $approvals = $vendor->approvals()
            ->with(['admin' => function($query) {
                $query->select('id', 'name', 'email');
            }])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $approvals
        ]);
    }

    /**
     * Approve or reject a vendor
     */
    public function store(Request $request, $vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);

        $validator = Validator::make($request->all(), [
            'action' => 'required|in:approved,rejected',
            'notes' => 'required_if:action,rejected|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Update vendor status
        $vendor->status = $request->action;
        if ($request->action === 'rejected') {
            $vendor->rejection_reason = $request->notes;
        } else {
            $vendor->rejection_reason = null;
        }
        $vendor->save();

        // Create approval record
        $approval = $vendor->approvals()->create([
            'admin_id' => Auth::id(),
            'action' => $request->action,
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'data' => $approval->load('admin:id,name,email'),
            'message' => 'Vendor has been ' . $request->action
        ], 201);
    }

    /**
     * Get specific approval record
     */
    public function show($vendorId, $approvalId)
    {
        $approval = VendorApproval::with(['vendor', 'admin:id,name,email'])
            ->where('vendor_id', $vendorId)
            ->findOrFail($approvalId);

        return response()->json([
            'success' => true,
            'data' => $approval
        ]);
    }
}