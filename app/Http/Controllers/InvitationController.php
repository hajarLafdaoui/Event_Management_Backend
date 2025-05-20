<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\GuestList;
use App\Models\Invitation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Notifications\EventInvitationNotification;

class InvitationController extends Controller
{
    // WEB: Show send invitations form
    public function showSendForm(Event $event)
    {
        $guests = $event->guests;
        $templates = EmailTemplate::all();

        return view('invitations.send', compact('event', 'guests', 'templates'));
    }

    // WEB: Send invitations
    public function send(Request $request, Event $event)
    {
        $request->validate([
            'guest_ids' => 'required|array',
            'guest_ids.*' => 'exists:guest_lists,id',
            'template_id' => 'nullable|exists:email_templates,id'
        ]);

        $this->createInvitations($event, $request->guest_ids, $request->template_id);

        return redirect()->back()
            ->with('success', 'Invitations sent successfully');
    }

    // API: Send invitations
    public function sendInvitations(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        $request->validate([
            'guest_ids' => 'required|array',
            'guest_ids.*' => 'exists:guest_lists,id',
            'template_id' => 'nullable|exists:email_templates,id'
        ]);

        $invitations = $this->createInvitations($event, $request->guest_ids, $request->template_id);

        return response()->json([
            'message' => 'Invitations sent successfully',
            'count' => count($invitations),
            'invitations' => $invitations
        ]);
    }

    // Shared invitation creation logic
    protected function createInvitations($event, $guestIds, $templateId = null)
    {
        $invitations = [];

        foreach (GuestList::whereIn('id', $guestIds)->get() as $guest) {
            $invitation = Invitation::create([
                'event_id' => $event->event_id,
                'guest_id' => $guest->id,
                'sent_via' => 'email',
                'token' => Str::random(60),
                'template_id' => $templateId,
                'sent_at' => now(),
            ]);

            $guest->notify(new EventInvitationNotification($event, $invitation));
            $invitations[] = $invitation;
        }

        return $invitations;
    }

    // WEB: Show RSVP form

    public function showRSVP($token)
    {
        $invitation = Invitation::with('event')->where('token', $token)->firstOrFail();
        return view('invitations.rsvp', compact('invitation'));
    }

    // API: Show invitation details
    public function showInvitation($token)
    {
        $invitation = Invitation::with(['event', 'guest'])
            ->where('token', $token)
            ->firstOrFail();

        return response()->json([
            'invitation' => $invitation,
            'event' => $invitation->event,
            'guest' => $invitation->guest
        ]);
    }

    // WEB: Process RSVP response
    public function processRSVP(Request $request, $token)
    {
        $request->validate([
            'status' => 'required|in:accepted,declined',
            'notes' => 'nullable|string'
        ]);

        $this->updateRSVP($token, $request->status, $request->notes);

        return redirect()->route('rsvp.show', $token)
            ->with('status', 'Thank you for your response!');
    }

    // API: Process RSVP response
    public function processApiRSVP(Request $request, $token)
    {
        $request->validate([
            'status' => 'required|in:accepted,declined',
            'notes' => 'nullable|string'
        ]);

        $invitation = $this->updateRSVP($token, $request->status, $request->notes);

        return response()->json([
            'message' => 'RSVP recorded successfully',
            'status' => $invitation->rsvp_status
        ]);
    }

    // Shared RSVP update logic
    protected function updateRSVP($token, $status, $notes = null)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        $invitation->update([
            'rsvp_status' => $status,
            'response_notes' => $notes,
            'responded_at' => now()
        ]);

        return $invitation;
    }
}