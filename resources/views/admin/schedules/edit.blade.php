@extends('admin.layouts.app')
@section('title', 'Edit Schedule')

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Schedule</h1>
        <div class="breadcrumb"><a href="/admin/schedules">Schedules</a> &bull; Edit #{{ $schedule->id }}</div>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error) {{ $error }}<br> @endforeach
        </div>
    @endif

    <form method="POST" action="/admin/schedules/{{ $schedule->id }}">
        @csrf @method('PUT')
        <div class="form-group">
            <label class="form-label">Area Manager *</label>
            <select name="user_id" class="form-control" required>
                @foreach($ams as $am)
                    <option value="{{ $am->id }}" {{ $schedule->user_id == $am->id ? 'selected' : '' }}>{{ $am->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Store *</label>
            <select name="store_id" class="form-control" required>
                @foreach($stores as $store)
                    <option value="{{ $store->id }}" {{ $schedule->store_id == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Date *</label>
            <input type="date" name="scheduled_date" class="form-control"
                value="{{ $schedule->scheduled_date->format('Y-m-d') }}" required>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Start Time *</label>
                <input type="time" name="start_time" class="form-control"
                    value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">End Time *</label>
                <input type="time" name="end_time" class="form-control"
                    value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}" required>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Status *</label>
            <select name="status" class="form-control" required>
                <option value="pending" {{ $schedule->status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ $schedule->status === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="missed" {{ $schedule->status === 'missed' ? 'selected' : '' }}>Missed</option>
                <option value="cancelled" {{ $schedule->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control">{{ $schedule->notes }}</textarea>
        </div>
        <div class="flex-row">
            <button type="submit" class="btn btn-primary">Update Schedule</button>
            <a href="/admin/schedules" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
