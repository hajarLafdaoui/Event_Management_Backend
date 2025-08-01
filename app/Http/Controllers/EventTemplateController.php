<?php

namespace App\Http\Controllers;

use App\Models\EventTemplate;
use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenAI\Laravel\Facades\OpenAI;

class EventTemplateController extends Controller
{
    public function index()
    {
        $templates = EventTemplate::with('eventType')->get();
        return response()->json($templates);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_type_id' => 'required|exists:event_types,event_type_id',
            'template_name' => 'required|string|max:255',
            'template_description' => 'nullable|string',
            'default_budget' => 'nullable|numeric',
            'default_event_name' => 'nullable|string|max:255',
            'default_event_description' => 'nullable|string',
            'default_start_datetime' => 'nullable|date',
            'default_end_datetime' => 'nullable|date|after:default_start_datetime',
            'default_location' => 'nullable|string|max:255',
            'default_venue_name' => 'nullable|string|max:255',
            'default_address' => 'nullable|string|max:255',
            'default_city' => 'nullable|string|max:255',
            'default_state' => 'nullable|string|max:255',
            'default_country' => 'nullable|string|max:255',
            'default_postal_code' => 'nullable|string|max:20',
            'default_theme' => 'nullable|string|max:255',
            'default_notes' => 'nullable|string',
            'is_system_template' => 'boolean'
        ]);

        $validated['created_by_admin_id'] = Auth::id();

        $template = EventTemplate::create($validated);

        return response()->json($template, 201);
    }

    public function show(EventTemplate $template)
    {
        return response()->json($template->load('eventType'));
    }

    public function update(Request $request, EventTemplate $template)
    {
        $validated = $request->validate([
            'event_type_id' => 'required|exists:event_types,event_type_id',
            'template_name' => 'required|string|max:255',
            'template_description' => 'nullable|string',
            'default_budget' => 'nullable|numeric',
            'default_event_name' => 'nullable|string|max:255',
            'default_event_description' => 'nullable|string',
            'default_start_datetime' => 'nullable|date',
            'default_end_datetime' => 'nullable|date|after:default_start_datetime',
            'default_location' => 'nullable|string|max:255',
            'default_venue_name' => 'nullable|string|max:255',
            'default_address' => 'nullable|string|max:255',
            'default_city' => 'nullable|string|max:255',
            'default_state' => 'nullable|string|max:255',
            'default_country' => 'nullable|string|max:255',
            'default_postal_code' => 'nullable|string|max:20',
            'default_theme' => 'nullable|string|max:255',
            'default_notes' => 'nullable|string',
            'is_system_template' => 'boolean'
        ]);

        $template->update($validated);

        return response()->json($template);
    }

    public function destroy(EventTemplate $template)
    {
        $template->delete();
        return response()->json(['message' => 'Event template deleted successfully.'], 200);
    }

    public function generateWithAI(Request $request)
    {
        $request->validate([
            'event_type_id' => 'required|exists:event_types,event_type_id',
            'prompt' => 'nullable|string'
        ]);

        $eventType = EventType::findOrFail($request->event_type_id);

        // Generate with OpenAI
        $prompt = $request->prompt ?? "Create a comprehensive event template for {$eventType->type_name} including suggested budget, activities, and timeline.";

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7
        ]);

        $aiContent = $response->choices[0]->message->content;

        // Parse AI response (this would be more sophisticated in production)
        $budget = $this->extractBudgetFromAIResponse($aiContent);

        $template = EventTemplate::create([
            'event_type_id' => $eventType->event_type_id,
            'template_name' => 'AI Generated - ' . $eventType->type_name,
            'template_description' => $aiContent,
            'default_budget' => $budget,
            'created_by_admin_id' => Auth::id(),
            'is_system_template' => true
            // Optionally, parse and fill other default fields from AI if needed
        ]);

        return response()->json($template, 201);
    }

    protected function extractBudgetFromAIResponse($content)
    {
        // Simple regex to find budget in the content
        if (preg_match('/\$\s*[\d,]+(\.\d{2})?/', $content, $matches)) {
            return (float) str_replace(['$', ','], '', $matches[0]);
        }
        return 5000.00; // Default if not found
    }
}