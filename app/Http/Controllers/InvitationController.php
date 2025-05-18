<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\GuestList;
use App\Models\SentEmail;
use App\Models\Invitation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\EventInvitation;
use App\Models\EmailTemplate;
use App\Mail\InvitationConfirmation;
use Illuminate\Support\Facades\Mail;

class InvitationController extends Controller
{
    // Send invitations to guests
    public function sendInvitations(Request $request, $eventId)
    {
        $request->validate([
            'guest_ids' => 'required|array',
            'guest_ids.*' => 'exists:guest_lists,id',
            'template_id' => 'nullable|exists:email_templates,id',
            'send_via' => 'required|in:email,sms,both'
        ]);

        $event = Event::findOrFail($eventId);
        $template = $request->template_id ? EmailTemplate::find($request->template_id) : null;
        $guests = GuestList::whereIn('id', $request->guest_ids)->get();

        $sentCount = 0;

        foreach ($guests as $guest) {
            if (!$guest->email && in_array($request->send_via, ['email', 'both'])) {
                continue;
            }

            $token = Str::random(32);
            
            // Create or update invitation
            $invitation = Invitation::updateOrCreate(
                ['guest_id' => $guest->id],
                [
                    'event_id' => $eventId,
                    'sent_via' => $request->send_via,
                    'sent_at' => now(),
                    'token' => $token,
                    'template_id' => $template ? $template->id : null,
                ]
            );

            // Send email invitation
            if (in_array($request->send_via, ['email', 'both']) && $guest->email) {
                try {
                    Mail::to($guest->email)->send(new EventInvitation($event, $guest, $invitation, $template));
                    $sentCount++;
                    
                    // Record sent email
                    $this->recordSentEmail($event, $guest, $template);
                } catch (\Exception $e) {
                    // Log error
                    continue;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Invitations sent to {$sentCount} guests"
        ]);
    }

    // Handle RSVP response
    public function handleRSVP(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();
        
        $request->validate([
            'response' => 'required|in:accepted,declined',
            'notes' => 'nullable|string',
            'plus_one_attending' => 'nullable|boolean'
        ]);

        $invitation->update([
            'rsvp_status' => $request->response,
            'responded_at' => now(),
            'response_notes' => $request->notes
        ]);

        // Send confirmation email
        if ($invitation->guest->email) {
            Mail::to($invitation->guest->email)->send(new InvitationConfirmation($invitation));
        }

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your response!'
        ]);
    }

    // Record sent email in database
    private function recordSentEmail($event, $guest, $template)
    {
        SentEmail::create([
            'template_id' => $template ? $template->id : null,
            'event_id' => $event->id,
            'sender_id' => auth()->id(),
            'recipient_email' => $guest->email,
            'subject' => $template ? $template->template_subject : "Invitation to {$event->event_name}",
            'body' => $template ? $template->template_body : '',
            'status' => 'sent'
        ]);
    }
}