@extends('admin.layouts.app')
@section('title', $user->name . ' - AM Detail')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $user->name }}</h1>
        <div class="breadcrumb"><a href="/admin/users">Area Managers</a> &bull; {{ $user->name }}</div>
    </div>
    <a href="/admin/users/{{ $user->id }}/edit" class="btn btn-outline">Edit</a>
</div>

<div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">
    <div class="stat-card">
        <div>
            <span class="stat-value">{{ $user->email }}</span>
            <span class="stat-label">Email</span>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value">{{ $user->phone ?? '-' }}</span>
            <span class="stat-label">Phone</span>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value">{{ $user->schedules_count }}</span>
            <span class="stat-label">Total Schedules</span>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value">{{ $user->check_ins_count }}</span>
            <span class="stat-label">Total Check-ins</span>
        </div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header"><h3>📅 Recent Schedules</h3></div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Date</th><th>Store</th><th>Time</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($recentSchedules as $schedule)
                    <tr>
                        <td>{{ $schedule->scheduled_date->format('d M Y') }}</td>
                        <td>{{ $schedule->store->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                        <td>
                            <span class="badge badge-{{ $schedule->status === 'completed' ? 'success' : ($schedule->status === 'missed' ? 'danger' : 'info') }}">
                                {{ ucfirst($schedule->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-muted" style="text-align:center;padding:16px;">No schedules</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>📍 Recent Check-ins</h3></div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Store</th><th>Verified</th><th>Distance</th><th>Time</th></tr></thead>
                <tbody>
                    @forelse($recentCheckIns as $checkIn)
                    <tr>
                        <td>{{ $checkIn->store->name }}</td>
                        <td>
                            @if($checkIn->is_verified)
                                <span class="badge badge-success">✓ Verified</span>
                            @else
                                <span class="badge badge-danger">✗ Unverified</span>
                            @endif
                        </td>
                        <td>{{ $checkIn->distance_from_store }}m</td>
                        <td class="text-sm text-muted">{{ $checkIn->checked_in_at->format('d M H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-muted" style="text-align:center;padding:16px;">No check-ins</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
