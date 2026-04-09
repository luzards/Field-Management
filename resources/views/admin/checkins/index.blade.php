@extends('admin.layouts.app')
@section('title', 'Check-ins')

@section('content')
<div class="page-header">
    <div>
        <h1>Check-ins</h1>
        <div class="breadcrumb">Monitoring &bull; Check-ins</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="/admin/check-ins" class="flex-row">
            <input type="date" name="date" class="form-control" style="max-width:180px;" value="{{ request('date') }}">
            <select name="verified" class="form-control" style="max-width:160px;">
                <option value="">All Status</option>
                <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Verified</option>
                <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>Unverified</option>
            </select>
            <button type="submit" class="btn btn-sm btn-outline">Filter</button>
        </form>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>AM</th><th>Store</th><th>Distance</th><th>Verified</th><th>Photo</th><th>Time</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($checkIns as $checkIn)
                <tr>
                    <td>{{ $checkIn->user->name }}</td>
                    <td>{{ $checkIn->store->name }}</td>
                    <td>{{ $checkIn->distance_from_store }}m</td>
                    <td>
                        @if($checkIn->is_verified)
                            <span class="badge badge-success">✓ Verified</span>
                        @else
                            <span class="badge badge-danger">✗ Failed ({{ $checkIn->distance_from_store }}m)</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ url('storage/' . $checkIn->photo_path) }}" target="_blank" class="btn btn-sm btn-outline">📷 View</a>
                    </td>
                    <td class="text-sm text-muted">{{ $checkIn->checked_in_at->format('d M Y H:i') }}</td>
                    <td>
                        <a href="/admin/check-ins/{{ $checkIn->id }}" class="btn btn-sm btn-outline">Details</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-muted" style="text-align:center;padding:24px;">No check-ins found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $checkIns->appends(request()->query())->links('pagination::simple-default') }}</div>
</div>
@endsection
