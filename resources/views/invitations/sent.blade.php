<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="container">
    <h1>Send Invitations for {{ $event->event_name }}</h1>
    
    <form method="POST" action="{{ route('invitations.send', $event->id) }}">
        @csrf
        
        <div class="mb-3">
            <label class="form-label">Select Guests</label>
            @foreach($guests as $guest)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="guest_ids[]" 
                           value="{{ $guest->id }}" id="guest_{{ $guest->id }}">
                    <label class="form-check-label" for="guest_{{ $guest->id }}">
                        {{ $guest->first_name }} {{ $guest->last_name }} - {{ $guest->email }}
                    </label>
                </div>
            @endforeach
        </div>
        
        <div class="mb-3">
            <label for="template_id" class="form-label">Select Template (optional)</label>
            <select class="form-select" name="template_id" id="template_id">
                <option value="">Default Template</option>
                @foreach($templates as $template)
                    <option value="{{ $template->id }}">{{ $template->template_name }}</option>
                @endforeach
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Send Invitations</button>
    </form>
</div>
</body>
</html>