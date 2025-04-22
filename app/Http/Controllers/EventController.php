<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventTemplate;
use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenAI\Laravel\Facades\OpenAI;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $user = Auth::user();
        
        $query = Event::with(['eventType', 'template']);
        
        if ($user->isClient()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isVendor()) {
            // Vendor might only see events they're associated with
            $query->whereHas('vendors', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        // Admin can see all events
        
        $events = $query->get();
        
        return response()->json($events);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'event_type_id' => 'required|exists:event_types,event_type_id',
            'template_id' => 'nullable|exists:event_templates,template_id',
            'event_name' => 'required|string|max:255',
            'event_description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'location' => 'required|string|max:255',
            'venue_name' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'budget' => 'nullable|numeric',
            'theme' => 'nullable|string|max:100',
            'notes' => 'nullable|string'
        ]);

        $validated['user_id'] = $user->id;
        $validated['status'] = 'draft';

        // If using a template, fill in missing fields
        if ($request->template_id) {
            $template = EventTemplate::find($request->template_id);
            $validated['event_description'] = $validated['event_description'] ?? $template->template_description;
            $validated['budget'] = $validated['budget'] ?? $template->default_budget;
        }

        $event = Event::create($validated);

        return response()->json($event, 201);
    }

    public function show(Event $event)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($user->isClient() && $event->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        return response()->json($event->load(['eventType', 'template', 'user']));
    }

    public function update(Request $request, Event $event)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($user->isClient() && $event->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'event_type_id' => 'required|exists:event_types,event_type_id',
            'template_id' => 'nullable|exists:event_templates,template_id',
            'event_name' => 'required|string|max:255',
            'event_description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'location' => 'required|string|max:255',
            'venue_name' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'budget' => 'nullable|numeric',
            'theme' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,planned,in_progress,completed,cancelled'
        ]);

        $event->update($validated);

        return response()->json($event);
    }

    public function destroy(Event $event)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($user->isClient() && $event->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $event->delete();
        return response()->json(null, 204);
    }

    public function generateFromTemplate(Request $request, EventTemplate $template)
    {
        $user = Auth::user();
        
        $event = Event::create([
            'user_id' => $user->id,
            'event_type_id' => $template->event_type_id,
            'template_id' => $template->template_id,
            'event_name' => $template->template_name,
            'event_description' => $template->template_description,
            'start_datetime' => now()->addWeek(),
            'end_datetime' => now()->addWeek()->addDay(),
            'location' => 'To be determined',
            'budget' => $template->default_budget,
            'status' => 'draft'
        ]);

        return response()->json($event, 201);
    }

    public function generateWithAI(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'event_type_id' => 'required|exists:event_types,event_type_id',
            'prompt' => 'required|string'
        ]);

        $eventType = EventType::findOrFail($request->event_type_id);
        
        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $request->prompt]
            ],
            'temperature' => 0.7
        ]);

        $aiContent = $response->choices[0]->message->content;
        
        // Parse AI response - this would be more sophisticated in production
        $eventData = $this->parseAIEventResponse($aiContent);

        $event = Event::create([
            'user_id' => $user->id,
            'event_type_id' => $eventType->event_type_id,
            'event_name' => $eventData['name'] ?? 'AI Generated Event',
            'event_description' => $aiContent,
            'start_datetime' => $eventData['start_datetime'] ?? now()->addWeek(),
            'end_datetime' => $eventData['end_datetime'] ?? now()->addWeek()->addDay(),
            'location' => $eventData['location'] ?? 'To be determined',
            'budget' => $eventData['budget'] ?? 5000.00,
            'status' => 'draft'
        ]);

        return response()->json($event, 201);
    }

    protected function parseAIEventResponse($content)
    {
        // This is a simplified parser - you'd want to implement more robust parsing
        $data = [];
        
        if (preg_match('/Event Name: (.*)/i', $content, $matches)) {
            $data['name'] = trim($matches[1]);
        }
        
        if (preg_match('/Budget: (\$\s*[\d,]+)/i', $content, $matches)) {
            $data['budget'] = (float) str_replace(['$', ','], '', $matches[1]);
        }
        
        if (preg_match('/Location: (.*)/i', $content, $matches)) {
            $data['location'] = trim($matches[1]);
        }
        
        return $data;
    }
}