<?php

namespace App\Http\Controllers;

use App\Models\EventGallery;
use Illuminate\Http\Request;

class EventGalleryController extends Controller

{
    public function index()
    {
        // Get all event gallery media
        $eventGalleries = EventGallery::all();

        if ($eventGalleries->isEmpty()) {
            return response()->json(['message' => 'No media found.'], 404);
        }

        return response()->json($eventGalleries);
    }
    // Store new event gallery media
    public function store(Request $request)
    {
        // Validate request
        $request->validate([
            'event_id' => 'required|exists:events,event_id',
            'uploader_id' => 'required|exists:users,id',
            'media_url' => 'required|string',
            'media_type' => 'required|in:image,video',
            'caption' => 'nullable|string',
        ]);

        // Create event gallery entry
        $eventGallery = EventGallery::create([
            'event_id' => $request->event_id,
            'uploader_id' => $request->uploader_id,
            'media_url' => $request->media_url,
            'media_type' => $request->media_type,
            'caption' => $request->caption,
            'uploaded_at' => now(),
        ]);

        return response()->json($eventGallery, 201);
    }

    // Show event gallery media by event ID
    public function show($eventId)
    {
        $eventGallery = EventGallery::where('event_id', $eventId)->get();

        if ($eventGallery->isEmpty()) {
            return response()->json(['message' => 'No media found for this event.'], 404);
        }

        return response()->json($eventGallery);
    }

    // Update existing event gallery media
    public function update(Request $request, $id)
    {
        $eventGallery = EventGallery::findOrFail($id);

        $request->validate([
            'media_url' => 'required|string',
            'media_type' => 'required|in:image,video',
            'caption' => 'nullable|string',
        ]);

        $eventGallery->update([
            'media_url' => $request->media_url,
            'media_type' => $request->media_type,
            'caption' => $request->caption,
        ]);

        return response()->json($eventGallery);
    }

    // Delete event gallery media
    public function destroy($id)
    {
        $eventGallery = EventGallery::findOrFail($id);
        $eventGallery->delete();

        return response()->json(['message' => 'Media deleted successfully.']);
    }

    

}
