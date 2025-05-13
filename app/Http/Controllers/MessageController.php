<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::with(['sender', 'receiver', 'booking'])->get();

        return response()->json([
            'status' => 'success',
            'data' => $messages
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'related_booking_id' => 'nullable|exists:booking_requests,booking_id',
            'message_text' => 'required|string',
        ]);

        $message = Message::create($validated);

        return response()->json($message, 201);
    }

    public function show($id)
    {
        $message = Message::with(['sender', 'receiver', 'booking'])->find($id);
    
        if (!$message) {
            return response()->json([
                'status' => 'error',
                'message' => 'Message not found'
            ], 404);
        }
    
        return response()->json([
            'status' => 'success',
            'data' => $message
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $message = Message::findOrFail($id);

        $validated = $request->validate([
            'is_read' => 'boolean',
            'read_at' => 'nullable|date',
        ]);

        $message->update($validated);

        return response()->json($message);
    }

    public function destroy($id)
    {
        $message = Message::findOrFail($id);
        $message->delete();

        return response()->json(null, 204);
    }
}
