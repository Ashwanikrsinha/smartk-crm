@extends('layouts.dashboard')
@section('content')
    <header class="d-flex justify-content-between align-items-center mb-4">
        <h5>New Lead Source</h5>
        <a href="{{ route('lead-sources.index') }}" class="btn btn-sm btn-secondary">Back</a>
    </header>

    <form action="{{ route('lead-sources.store') }}" method="POST" class="bg-white rounded shadow-sm p-4"
        style="max-width: 500px;">
        @csrf

        <div class="mb-3">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                placeholder="e.g. Digital Campaign, Referral, Cold Calling" required>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="feather icon-save me-1"></i> Save
            </button>
            <a href="{{ route('lead-sources.index') }}" class="btn btn-secondary">Cancel</a>
        </div>

    </form>
@endsection
