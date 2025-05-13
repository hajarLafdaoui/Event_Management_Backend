<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    // Show a list of all email templates
    public function index()
    {
        $templates = EmailTemplate::all();
        return response()->json($templates);
    }

    // Show a specific email template by ID
    public function show($id)
    {
        $template = EmailTemplate::find($id);

        if (!$template) {
            return response()->json(['message' => 'Email template not found'], 404);
        }

        return response()->json($template);
    }

    // Create a new email template
    public function store(Request $request)
    {
        $validated = $request->validate([
            'template_name' => 'required|string|max:255',
            'template_subject' => 'required|string|max:255',
            'template_body' => 'required|string',
            'is_system_template' => 'required|boolean',
            'created_by_admin_id' => 'required|exists:users,id',
        ]);

        $template = EmailTemplate::create($validated);

        return response()->json($template, 201);
    }

    // Update an existing email template
    public function update(Request $request, $id)
    {
        $template = EmailTemplate::find($id);

        if (!$template) {
            return response()->json(['message' => 'Email template not found'], 404);
        }

        $validated = $request->validate([
            'template_name' => 'required|string|max:255',
            'template_subject' => 'required|string|max:255',
            'template_body' => 'required|string',
            'is_system_template' => 'required|boolean',
            'created_by_admin_id' => 'required|exists:users,id',
        ]);

        $template->update($validated);

        return response()->json($template);
    }

    // Delete an email template
    public function destroy($id)
    {
        $template = EmailTemplate::find($id);

        if (!$template) {
            return response()->json(['message' => 'Email template not found'], 404);
        }

        $template->delete();

        return response()->json(['message' => 'Email template deleted successfully']);
    }
}
