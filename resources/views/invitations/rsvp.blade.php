{{-- resources/views/invitations/rsvp.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>RSVP for {{ $invitation->event->event_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>RSVP for {{ $invitation->event->event_name }}</h2>
                </div>
                <div class="card-body">
                    <p><strong>Date:</strong> {{ $invitation->event->start_datetime->format('F j, Y') }}</p>
                    <p><strong>Location:</strong> {{ $invitation->event->location }}</p>
                    
                    @if($invitation->rsvp_status === 'pending')
                        <form method="POST" action="{{ route('rsvp.submit', $invitation->token) }}">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label">Will you attend?</label>
                                <div class="d-grid gap-2">
                                    <button type="submit" name="status" value="accepted" class="btn btn-success">Accept</button>
                                    <button type="submit" name="status" value="declined" class="btn btn-danger">Decline</button>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notes" class="form-label">Additional Notes (optional)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <p>Thank you for your response!</p>
                            <p>Your status: <strong>{{ ucfirst($invitation->rsvp_status) }}</strong></p>
                            @if($invitation->response_notes)
                                <p>Your notes: {{ $invitation->response_notes }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>