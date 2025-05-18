<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\GuestList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Imports\GuestsImport;
use Maatwebsite\Excel\Facades\Excel;

class GuestListController extends Controller
{
    // List all guests for an event
    public function index($eventId)
    {
        $event = Event::findOrFail($eventId);
        $guests = $event->guests()->with('invitation')->get();
        
        return response()->json([
            'success' => true,
            'data' => $guests
        ]);
    }

    // Add a single guest
    public function store(Request $request, $eventId)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'plus_one_allowed' => 'boolean',
            'plus_one_name' => 'nullable|string|max:100',
            'dietary_restrictions' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $guest = GuestList::create([
            'event_id' => $eventId,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'plus_one_allowed' => $request->plus_one_allowed ?? false,
            'plus_one_name' => $request->plus_one_name,
            'dietary_restrictions' => $request->dietary_restrictions,
            'qr_code' => Str::random(20),
        ]);

        return response()->json([
            'success' => true,
            'data' => $guest
        ], 201);
    }

    // Import guests from CSV/Excel
    public function import(Request $request, $eventId)
{
    // Validate the request
    \Log::info('Request files:', $request->allFiles());
    \Log::info('Request data:', $request->all());

    // Then validate
    $validator = Validator::make($request->all(), [
        'file' => 'required|file|mimes:csv,xlsx,xls'
    ]);

    if ($validator->fails()) {
        \Log::error('Validation failed:', $validator->errors()->toArray());
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }


    // Check if file exists
    if (!$request->hasFile('file')) {
        return response()->json([
            'success' => false,
            'message' => 'No file was uploaded'
        ], 400);
    }

    $file = $request->file('file');

    // Verify file is valid
    if (!$file->isValid()) {
        return response()->json([
            'success' => false,
            'message' => 'Uploaded file is not valid'
        ], 400);
    }

    try {
        Excel::import(new GuestsImport($eventId), $file);
        
        return response()->json([
            'success' => true,
            'message' => 'Guests imported successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error importing guests: ' . $e->getMessage()
        ], 500);
    }
}

    // Update a guest
    public function update(Request $request, $eventId, $guestId)
    {
        $guest = GuestList::where('event_id', $eventId)->findOrFail($guestId);

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'plus_one_allowed' => 'boolean',
            'plus_one_name' => 'nullable|string|max:100',
            'dietary_restrictions' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $guest->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $guest
        ]);
    }

    // Delete a guest
    public function destroy($eventId, $guestId)
    {
        $guest = GuestList::where('event_id', $eventId)->findOrFail($guestId);
        $guest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Guest deleted successfully'
        ]);
    }
}