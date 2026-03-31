@extends('layouts.admin')
@section('title', 'Create Tracking Link')
@section('page-title', 'Create Tracking Link')

@section('content')
<div style="max-width:560px;">
    <div class="card">
        <div class="card-title mb-1">Create New Tracking Link</div>
        <div class="card-subtitle mb-4">A unique tracking code will be generated automatically.</div>
        <form method="POST" action="{{ route('admin.tracking.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Publisher</label>
                <select name="user_id" class="form-control" required>
                    <option value="">Select publisher...</option>
                    @foreach($publishers as $pub)
                        <option value="{{ $pub->id }}">{{ $pub->name }} — {{ $pub->email }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Link Name (Optional)</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Main Campaign">
            </div>
            <div class="form-group">
                <label class="form-label">Destination URL</label>
                <input type="url" name="original_url" class="form-control" placeholder="https://example.com/download" required>
            </div>
            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary">Create Link</button>
                <a href="{{ route('admin.tracking.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
