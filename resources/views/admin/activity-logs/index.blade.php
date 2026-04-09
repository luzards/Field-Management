@extends('admin.layouts.app')
@section('title', 'Activity Logs')

@section('content')
<div class="page-header">
    <div>
        <h1>Activity Logs</h1>
        <div class="breadcrumb">Monitoring &bull; Activity Logs</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="/admin/activity-logs" class="flex-row">
            <select name="action" class="form-control" style="max-width:200px;">
                <option value="">All Actions</option>
                <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Login</option>
                <option value="logout" {{ request('action') === 'logout' ? 'selected' : '' }}>Logout</option>
                <option value="check_in" {{ request('action') === 'check_in' ? 'selected' : '' }}>Check-in</option>
                <option value="schedule_create" {{ request('action') === 'schedule_create' ? 'selected' : '' }}>Schedule Create</option>
                <option value="schedule_update" {{ request('action') === 'schedule_update' ? 'selected' : '' }}>Schedule Update</option>
                <option value="profile_update" {{ request('action') === 'profile_update' ? 'selected' : '' }}>Profile Update</option>
            </select>
            <button type="submit" class="btn btn-sm btn-outline">Filter</button>
        </form>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>User</th><th>Action</th><th>Description</th><th>IP</th><th>Time</th></tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>{{ $log->user->name ?? 'Unknown' }}</td>
                    <td><span class="badge badge-info">{{ $log->action }}</span></td>
                    <td class="text-sm">{{ $log->description }}</td>
                    <td class="text-muted text-sm">{{ $log->ip_address }}</td>
                    <td class="text-muted text-sm">{{ $log->created_at->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-muted" style="text-align:center;padding:24px;">No activity logs</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $logs->appends(request()->query())->links('pagination::simple-default') }}</div>
</div>
@endsection
