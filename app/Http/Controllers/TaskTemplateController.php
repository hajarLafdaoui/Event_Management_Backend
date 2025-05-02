<?php

namespace App\Http\Controllers;

use App\Models\TaskTemplate;
use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenAI\Laravel\Facades\OpenAI;

class TaskTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('role:admin')->except(['index', 'show']);
    }

    public function index()
    {
        $templates = TaskTemplate::with(['eventType', 'createdBy'])->get();
        return response()->json($templates);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_type_id' => 'required|exists:event_types,event_type_id',
            'template_name' => 'required|string|max:255',
            'task_name' => 'required|string|max:255',
            'task_description' => 'nullable|string',
            'default_days_before_event' => 'nullable|integer',
            'default_priority' => 'nullable|in:low,medium,high',
            'default_duration_hours' => 'nullable|integer',
            'is_system_template' => 'boolean'
        ]);

        $validated['created_by_admin_id'] = Auth::id();

        $template = TaskTemplate::create($validated);

        return response()->json($template->load(['eventType', 'createdBy']), 201);
    }

    public function show(TaskTemplate $template)
    {
        return response()->json($template->load(['eventType', 'createdBy', 'eventTasks']));
    }

    public function update(Request $request, TaskTemplate $template)
    {
        $validated = $request->validate([
            'event_type_id' => 'required|exists:event_types,event_type_id',
            'template_name' => 'required|string|max:255',
            'task_name' => 'required|string|max:255',
            'task_description' => 'nullable|string',
            'default_days_before_event' => 'nullable|integer',
            'default_priority' => 'nullable|in:low,medium,high',
            'default_duration_hours' => 'nullable|integer',
            'is_system_template' => 'boolean'
        ]);

        $template->update($validated);

        return response()->json($template->load(['eventType', 'createdBy']));
    }

    public function destroy(TaskTemplate $template)
    {
        $template->delete();
        return response()->json(null, 204);
    }

    public function generateWithAI(Request $request)
    {
        $request->validate([
            'event_type_id' => 'required|exists:event_types,event_type_id',
            'prompt' => 'nullable|string'
        ]);

        $eventType = EventType::findOrFail($request->event_type_id);
        
        $prompt = $request->prompt ?? "Create a comprehensive task template for {$eventType->type_name} events including suggested timeline and priority.";
        
        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7
        ]);

        $aiContent = $response->choices[0]->message->content;
        
        // Parse AI response
        $defaults = $this->parseAITaskResponse($aiContent);

        $template = TaskTemplate::create([
            'event_type_id' => $eventType->event_type_id,
            'template_name' => 'AI Generated - ' . $eventType->type_name,
            'task_name' => $defaults['task_name'] ?? 'Generated Task',
            'task_description' => $aiContent,
            'default_days_before_event' => $defaults['days_before'] ?? 7,
            'default_priority' => $defaults['priority'] ?? 'medium',
            'default_duration_hours' => $defaults['duration'] ?? 2,
            'created_by_admin_id' => Auth::id(),
            'is_system_template' => true
        ]);

        return response()->json($template, 201);
    }

    protected function parseAITaskResponse($content)
    {
        $data = [];
        
        if (preg_match('/Task Name: (.*)/i', $content, $matches)) {
            $data['task_name'] = trim($matches[1]);
        }
        
        if (preg_match('/Days Before: (\d+)/i', $content, $matches)) {
            $data['days_before'] = (int)$matches[1];
        }
        
        if (preg_match('/Priority: (low|medium|high)/i', $content, $matches)) {
            $data['priority'] = strtolower($matches[1]);
        }
        
        if (preg_match('/Duration: (\d+) hours?/i', $content, $matches)) {
            $data['duration'] = (int)$matches[1];
        }
        
        return $data;
    }
}
