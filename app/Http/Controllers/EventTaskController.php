<?php

namespace App\Http\Controllers;

use App\Models\EventTask;
use App\Models\Event;
use App\Models\TaskTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventTaskController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = EventTask::with(['event', 'user', 'template']);
        
        if ($user->isClient()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isVendor()) {
            $query->where('assigned_to', 'vendor')
                  ->where('assigned_vendor_id', $user->vendor->id ?? null);
        }
        
        if ($request->has('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,event_id',
            'template_id' => 'nullable|exists:task_templates,task_template_id',
            'task_name' => 'required|string|max:255',
            'task_description' => 'nullable|string',
            'assigned_to' => 'required|in:client,vendor,none',
            'due_date' => 'nullable|date',
            'due_datetime' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'sometimes|in:not_started,in_progress,completed,cancelled'
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = $validated['status'] ?? 'not_started';
        $validated['progress_percentage'] = 0;

        // If using a template, fill in missing fields
        if ($request->template_id) {
            $template = TaskTemplate::find($request->template_id);
            $validated['task_description'] = $validated['task_description'] ?? $template->task_description;
            $validated['priority'] = $validated['priority'] ?? $template->default_priority;
        }

        $task = EventTask::create($validated);

        return response()->json($task->load(['event', 'template']), 201);
    }

    public function show($id)
    {
         $task = EventTask::with(['event', 'user', 'template'])->findOrFail($id);
        return response()->json($task);
    }

    public function update(Request $request,  $id)
    {
        $task = EventTask::findOrFail($id);
        
        $validated = $request->validate([
            'task_name' => 'sometimes|string|max:255',
            'task_description' => 'nullable|string',
            'assigned_to' => 'sometimes|in:client,vendor,none',
            'due_date' => 'nullable|date',
            'due_datetime' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'priority' => 'sometimes|in:low,medium,high',
            'status' => 'sometimes|in:not_started,in_progress,completed,cancelled',
            'progress_percentage' => 'sometimes|integer|min:0|max:100'
        ]);

        $task->update($validated);

        return response()->json($task->load(['event', 'template']));
    }

    public function destroy($id)
    {
        $task = EventTask::findOrFail($id);
        $task->delete();
         return response()->json(['message' => 'event task deleted successfully.'], 200);
    }

    public function generateFromTemplate(Request $request, TaskTemplate $template)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,event_id'
        ]);

        $task = EventTask::create([
            'event_id' => $validated['event_id'],
            'user_id' => Auth::id(),
            'template_id' => $template->task_template_id,
            'task_name' => $template->task_name,
            'task_description' => $template->task_description,
            'assigned_to' => 'none',
            'due_date' => now()->addDays($template->default_days_before_event),
            'priority' => $template->default_priority,
            'status' => 'not_started',
            'progress_percentage' => 0
        ]);

        return response()->json($task, 201);
    }
}