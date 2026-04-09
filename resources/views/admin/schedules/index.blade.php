@extends('admin.layouts.app')
@section('title', 'Schedules')

@section('content')
<div class="page-header">
    <div>
        <h1>Schedules</h1>
        <div class="breadcrumb">Management &bull; Schedules</div>
    </div>
    <a href="/admin/schedules/create" class="btn btn-primary">+ New Schedule</a>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="/admin/schedules" class="flex-row">
            <select name="user_id" class="form-control" style="max-width:200px;">
                <option value="">All AMs</option>
                @foreach($ams as $am)
                    <option value="{{ $am->id }}" {{ request('user_id') == $am->id ? 'selected' : '' }}>{{ $am->name }}</option>
                @endforeach
            </select>
            <input type="date" name="date" class="form-control" style="max-width:180px;" value="{{ request('date') }}">
            <select name="status" class="form-control" style="max-width:150px;">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="missed" {{ request('status') === 'missed' ? 'selected' : '' }}>Missed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button type="submit" class="btn btn-sm btn-outline">Filter</button>
        </form>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>Date</th><th>AM</th><th>Store</th><th>Time</th><th>Status</th><th>Check-in</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($schedules as $schedule)
                <tr>
                    <td>{{ $schedule->scheduled_date->format('d M Y') }}</td>
                    <td>{{ $schedule->user->name }}</td>
                    <td>{{ $schedule->store->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                    <td>
                        @if($schedule->status === 'completed')
                            <span class="badge badge-success">Completed</span>
                        @elseif($schedule->status === 'missed')
                            <span class="badge badge-danger">Missed</span>
                        @elseif($schedule->status === 'cancelled')
                            <span class="badge badge-warning">Cancelled</span>
                        @else
                            <span class="badge badge-info">Pending</span>
                        @endif
                    </td>
                    <td>
                        @if($schedule->checkIn)
                            <span class="badge badge-{{ $schedule->checkIn->is_verified ? 'success' : 'danger' }}">
                                {{ $schedule->checkIn->is_verified ? '✓' : '✗' }} {{ $schedule->checkIn->distance_from_store }}m
                            </span>
                        @else
                            <span class="text-muted text-sm">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex-row">
                            <a href="/admin/schedules/{{ $schedule->id }}/edit" class="btn btn-sm btn-outline">Edit</a>
                            <form method="POST" action="/admin/schedules/{{ $schedule->id }}" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-muted" style="text-align:center;padding:24px;">No schedules found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $schedules->appends(request()->query())->links('pagination::simple-default') }}</div>
</div>
@endsection
