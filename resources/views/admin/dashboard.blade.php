@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1>Dashboard</h1>
        <div class="breadcrumb">📊 Overview &bull; {{ now()->format('l, d M Y') }}</div>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div>
            <span class="stat-value">{{ $stats['active_ams'] }}</span>
            <span class="stat-label">Active Area Managers</span>
        </div>
        <div class="stat-icon blue">👥</div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value">{{ $stats['total_stores'] }}</span>
            <span class="stat-label">Total Stores</span>
        </div>
        <div class="stat-icon green">🏪</div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value">{{ $stats['today_checkins'] }} / {{ $stats['today_schedules'] }}</span>
            <span class="stat-label">Today's Check-ins / Schedules</span>
        </div>
        <div class="stat-icon yellow">📍</div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value">{{ $stats['completion_rate'] }}%</span>
            <span class="stat-label">Today's Completion Rate</span>
        </div>
        <div class="stat-icon {{ $stats['completion_rate'] >= 70 ? 'green' : 'red' }}">📈</div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value">{{ $stats['today_verified'] }}</span>
            <span class="stat-label">Verified Check-ins Today</span>
        </div>
        <div class="stat-icon green">✅</div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value">{{ $stats['weekly_checkins'] }}</span>
            <span class="stat-label">This Week's Check-ins</span>
        </div>
        <div class="stat-icon blue">📅</div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value">{{ $sopStats['total_audits'] }}</span>
            <span class="stat-label">Total SOP Audits</span>
        </div>
        <div class="stat-icon green">✅</div>
    </div>
    <div class="stat-card">
        <div>
            <span class="stat-value" style="color:{{ $sopStats['avg_score'] >= 7 ? 'var(--success)' : ($sopStats['avg_score'] >= 5 ? 'var(--warning)' : 'var(--danger)') }}">{{ $sopStats['avg_score'] }}/10</span>
            <span class="stat-label">Avg SOP Score</span>
        </div>
        <div class="stat-icon {{ $sopStats['avg_score'] >= 7 ? 'green' : ($sopStats['avg_score'] >= 5 ? 'yellow' : 'red') }}">⭐</div>
    </div>
</div>

<div class="grid-2">
    <!-- Today's Schedules -->
    <div class="card">
        <div class="card-header">
            <h3>📅 Today's Schedules</h3>
            <a href="/admin/schedules?date={{ now()->format('Y-m-d') }}" class="btn btn-sm btn-outline">View All</a>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>AM</th>
                        <th>Store</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todaySchedules as $schedule)
                    <tr>
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
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-muted text-sm" style="text-align:center;padding:24px;">No schedules for today</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Check-ins -->
    <div class="card">
        <div class="card-header">
            <h3>📍 Recent Check-ins</h3>
            <a href="/admin/check-ins" class="btn btn-sm btn-outline">View All</a>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>AM</th>
                        <th>Store</th>
                        <th>Verified</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentCheckIns as $checkIn)
                    <tr>
                        <td>{{ $checkIn->user->name }}</td>
                        <td>{{ $checkIn->store->name }}</td>
                        <td>
                            @if($checkIn->is_verified)
                                <span class="badge badge-success">✓ {{ $checkIn->distance_from_store }}m</span>
                            @else
                                <span class="badge badge-danger">✗ {{ $checkIn->distance_from_store }}m</span>
                            @endif
                        </td>
                        <td class="text-sm text-muted">{{ $checkIn->checked_in_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-muted text-sm" style="text-align:center;padding:24px;">No check-ins yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
