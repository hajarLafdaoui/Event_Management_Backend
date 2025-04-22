<?php

namespace App\Http\Controllers;

use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('role:admin')->except(['index']);
    }

    public function index()
    {
        $eventTypes = EventType::where('is_active', true)->get();
        return response()->json($eventTypes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['created_by_admin_id'] = Auth::id();

        $eventType = EventType::create($validated);

        return response()->json($eventType, 201);
    }

    public function update(Request $request, EventType $eventType)
    {
        $validated = $request->validate([
            'type_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $eventType->update($validated);

        return response()->json($eventType);
    }

    public function destroy(EventType $eventType)
    {
        $eventType->delete();
        return response()->json(null, 204);
    }
}