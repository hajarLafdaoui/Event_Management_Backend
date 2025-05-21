<?php
namespace App\Http\Controllers;

use App\Models\EventFeedback;
use App\Models\Event;
use App\Models\GuestList;
use Illuminate\Http\Request;

class EventFeedbackController extends Controller
{
    // Show feedback form
    public function create($event_id, $guest_id)
    {
        $event = Event::findOrFail($event_id);
        $guest = GuestList::findOrFail($guest_id);

        // Only allow feedback if event has passed
        $canGiveFeedback = now()->greaterThan($event->start_datetime);

        return view('feedback.create', compact('event', 'guest', 'canGiveFeedback'));
    }

    // Store feedback
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,event_id',
            'guest_id' => 'required|exists:guest_lists,id',
            'rating' => 'required|integer|min:1|max:5',
            'feedback_text' => 'nullable|string',
        ]);

        EventFeedback::create([
            'event_id' => $request->event_id,
            'guest_id' => $request->guest_id,
            'rating' => $request->rating,
            'feedback_text' => $request->feedback_text,
            'submitted_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Thank you for your feedback!');
    }

    public function eventFeedbacks($event_id)
    {
        $feedbacks = EventFeedback::where('event_id', $event_id)->with('guest')->get();
        return response()->json($feedbacks);
    }
}