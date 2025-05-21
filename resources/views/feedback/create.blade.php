@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

@if($canGiveFeedback)
    <form method="POST" action="{{ route('feedback.store') }}">
        @csrf
        <input type="hidden" name="event_id" value="{{ $event->event_id }}">
        <input type="hidden" name="guest_id" value="{{ $guest->id }}">
        <label>Rating (1-5):</label>
        <input type="number" name="rating" min="1" max="5" required>
        <label>Feedback:</label>
        <textarea name="feedback_text"></textarea>
        <button type="submit">Submit Feedback</button>
    </form>
@else
    <div class="alert alert-info">
        Feedback will be available after the event ends.
    </div>
@endif