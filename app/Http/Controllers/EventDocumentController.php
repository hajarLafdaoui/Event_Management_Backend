<?php

namespace App\Http\Controllers;

use App\Models\EventDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EventDocumentController extends Controller
{
    // Display a listing of the resource
    public function index()
    {
        // Fetch all event documents
        $documents = EventDocument::all();
        return response()->json($documents);
    }

    // Store a newly created resource in storage
    public function store(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,event_id',
            'uploader_id' => 'required|exists:users,id',
            'file' => 'required|file|mimes:pdf,jpeg,png,docx|max:10240', // Validate the uploaded file itself
            'description' => 'nullable|string',
            'uploaded_at' => 'nullable|date', // Optional uploaded_at
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Store file and get file path (this assumes the file is uploaded via the 'file' field)
        $file = $request->file('file');
        $filePath = $file->store('event_documents', 'public');

        // Create new event document record
        $eventDocument = EventDocument::create([
            'event_id' => $request->event_id,
            'uploader_id' => $request->uploader_id,
            'file_url' => $filePath, // Store the file path in the 'file_url' field
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'description' => $request->description,
            'uploaded_at' => now(), // or you can set $request->uploaded_at if needed
        ]);

        return response()->json($eventDocument, 201);
    }

    // Display the specified resource
    public function show($id)
    {
        $eventDocument = EventDocument::find($id);
        if (!$eventDocument) {
            return response()->json(['message' => 'Document not found'], 404);
        }
        return response()->json($eventDocument);
    }

    // Update the specified resource in storage
public function update(Request $request, $id)
{
    $eventDocument = EventDocument::find($id);
    if (!$eventDocument) {
        return response()->json(['message' => 'Document not found'], 404);
    }

    \Log::info('Update request data:', $request->all());

    $validator = Validator::make($request->all(), [
        'file' => 'nullable|file|mimes:pdf,jpeg,png,docx|max:10240',
        'description' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }

    if ($request->hasFile('file')) {
        if ($eventDocument->file_url) {
            Storage::disk('public')->delete($eventDocument->file_url);
        }
        $file = $request->file('file');
        $filePath = $file->store('event_documents', 'public');

        $eventDocument->file_url = $filePath;
        $eventDocument->file_name = $file->getClientOriginalName();
        $eventDocument->file_type = $file->getClientMimeType();
        $eventDocument->file_size = $file->getSize();
    }

    $eventDocument->description = $request->input('description', $eventDocument->description);

    $eventDocument->uploaded_at = now();

    $eventDocument->save();

    return response()->json($eventDocument);
}

    // Remove the specified resource from storage
    public function destroy($id)
    {
        $eventDocument = EventDocument::find($id);
        if (!$eventDocument) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        // Delete the file from storage
        Storage::delete($eventDocument->file_url);

        // Delete the record from database
        $eventDocument->delete();

        return response()->json(['message' => 'Document deleted successfully']);
    }
}
