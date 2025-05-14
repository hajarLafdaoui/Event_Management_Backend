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
        $filePath = $file->store('event_documents');

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

        // Validate request data
        $validator = Validator::make($request->all(), [
            'event_id' => 'nullable|exists:events,event_id',
            'uploader_id' => 'nullable|exists:users,id',
            'file_url' => 'nullable|string|max:255',
            'file_name' => 'nullable|string|max:255',
            'file_type' => 'nullable|string|max:50',
            'file_size' => 'nullable|integer',
            'description' => 'nullable|string',
            'uploaded_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // If a new file is uploaded, delete the old file and store the new one
        if ($request->hasFile('file')) {
            // Delete the old file
            Storage::delete($eventDocument->file_url);

            // Store the new file
            $file = $request->file('file');
            $filePath = $file->store('event_documents');

            // Update file details
            $eventDocument->file_url = $filePath;
            $eventDocument->file_name = $file->getClientOriginalName();
            $eventDocument->file_type = $file->getClientMimeType();
            $eventDocument->file_size = $file->getSize();
        }

        // Update other fields
        $eventDocument->event_id = $request->event_id ?? $eventDocument->event_id;
        $eventDocument->uploader_id = $request->uploader_id ?? $eventDocument->uploader_id;
        $eventDocument->description = $request->description ?? $eventDocument->description;
        $eventDocument->uploaded_at = $request->uploaded_at ?? now();

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
