@extends('layouts.admin')
@section('title', 'Ad Presets')
@section('page-title', 'Ad Button Presets')

@section('content')
<div class="grid-2" style="align-items:start;">
    <!-- Create Form -->
    <div class="card">
        <div class="card-title mb-4">Create New Preset</div>
        <form method="POST" action="{{ route('admin.ad-presets.store') }}">
            @csrf
            <div class="form-group"><label class="form-label">Preset Name</label><input type="text" name="name" class="form-control" required placeholder="e.g. Green Rounded"></div>
            <div class="form-group"><label class="form-label">Button Text</label><input type="text" name="button_text" class="form-control" required placeholder="Download Now" maxlength="50"></div>
            <div class="grid-2">
                <div class="form-group"><label class="form-label">Button Color</label><input type="color" name="button_color" class="form-control" value="#01BF63" style="height:42px;padding:4px;"></div>
                <div class="form-group"><label class="form-label">Text Color</label><input type="color" name="button_text_color" class="form-control" value="#ffffff" style="height:42px;padding:4px;"></div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Size</label>
                    <select name="button_size" class="form-control">
                        <option value="small">Small</option>
                        <option value="medium" selected>Medium</option>
                        <option value="large">Large</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Style</label>
                    <select name="button_style" class="form-control">
                        <option value="rounded" selected>Rounded</option>
                        <option value="square">Square</option>
                        <option value="pill">Pill</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Create Preset</button>
        </form>
    </div>

    <!-- Presets List -->
    <div class="card">
        <div class="card-title mb-4">Existing Presets</div>
        @forelse($presets as $preset)
        <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #f3f4f6;">
            <div>
                <a href="#" onclick="return false;" style="
                    display:inline-block;
                    background:{{ $preset->button_color }};
                    color:{{ $preset->button_text_color }};
                    padding:{{ ['small'=>'6px 14px','medium'=>'9px 20px','large'=>'11px 28px'][$preset->button_size] ?? '9px 20px' }};
                    border-radius:{{ ['rounded'=>'6px','square'=>'0','pill'=>'40px'][$preset->button_style] ?? '6px' }};
                    font-size:13px;font-weight:600;text-decoration:none;font-family:Arial,sans-serif;">
                    {{ $preset->button_text }}
                </a>
            </div>
            <div style="flex:1;">
                <div style="font-size:13px;font-weight:600;">{{ $preset->name }}</div>
                <div style="font-size:12px;color:#9ca3af;">{{ ucfirst($preset->button_size) }} · {{ ucfirst($preset->button_style) }}</div>
            </div>
            <span class="badge {{ $preset->is_active ? 'badge-success' : 'badge-danger' }}">{{ $preset->is_active ? 'Active' : 'Off' }}</span>
            <form method="POST" action="{{ route('admin.ad-presets.destroy', $preset) }}" onsubmit="return confirm('Delete preset?')">@csrf @method('DELETE')<button class="btn btn-ghost btn-sm" style="color:#ef4444;">Del</button></form>
        </div>
        @empty
        <div style="text-align:center;padding:24px;color:#9ca3af;">No presets yet</div>
        @endforelse
    </div>
</div>
@endsection
