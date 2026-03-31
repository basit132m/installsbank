@extends('layouts.publisher')
@section('title', 'New Ticket')
@section('page-title', 'New Support Ticket')

@section('content')
<div style="max-width:600px;">
    <div class="card">
        <form method="POST" action="{{ route('publisher.support.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required maxlength="200">
            </div>
            <div class="form-group">
                <label class="form-label">Priority</label>
                <select name="priority" class="form-control">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Message</label>
                <textarea name="message" class="form-control" rows="6" required maxlength="2000" placeholder="Describe your issue..."></textarea>
            </div>
            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary">Submit Ticket</button>
                <a href="{{ route('publisher.support.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
