@extends('admin.layouts.app')
@section('title', 'Create Schedule')

@section('content')
<div class="page-header">
    <div>
        <h1>Create Schedule</h1>
        <div class="breadcrumb"><a href="/admin/schedules">Schedules</a> &bull; Create New</div>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error) {{ $error }}<br> @endforeach
        </div>
    @endif

    <form method="POST" action="/admin/schedules">
        @csrf
        <div class="form-group">
            <label class="form-label">Area Manager *</label>
            <select name="user_id" class="form-control" required>
                <option value="">Select AM</option>
                @foreach($ams as $am)
                    <option value="{{ $am->id }}" {{ old('user_id') == $am->id ? 'selected' : '' }}>{{ $am->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Store *</label>
            <select name="store_id" class="form-control" required>
                <option value="">Select Store</option>
                @foreach($stores as $store)
                    <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Date *</label>
            <input type="date" name="scheduled_date" class="form-control" value="{{ old('scheduled_date') }}" required>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Start Time *</label>
                <input type="time" name="start_time" class="form-control" value="{{ old('start_time', '09:00') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">End Time *</label>
                <input type="time" name="end_time" class="form-control" value="{{ old('end_time', '11:00') }}" required>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
        </div>
        <div class="flex-row">
            <button type="submit" class="btn btn-primary">Create Schedule</button>
            <a href="/admin/schedules" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
