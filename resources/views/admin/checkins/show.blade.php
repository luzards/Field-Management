@extends('admin.layouts.app')
@section('title', 'Check-in Detail')

@section('content')
<div class="page-header">
    <div>
        <h1>Check-in Detail</h1>
        <div class="breadcrumb"><a href="/admin/check-ins">Check-ins</a> &bull; #{{ $checkIn->id }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header"><h3>📋 Check-in Info</h3></div>
        <table>
            <tr><td class="text-muted" style="width:140px;">AM</td><td><strong>{{ $checkIn->user->name }}</strong></td></tr>
            <tr><td class="text-muted">Store</td><td>{{ $checkIn->store->name }}</td></tr>
            <tr><td class="text-muted">Store Address</td><td>{{ $checkIn->store->address }}</td></tr>
            <tr><td class="text-muted">Checked In At</td><td>{{ $checkIn->checked_in_at->format('d M Y H:i:s') }}</td></tr>
            <tr>
                <td class="text-muted">Status</td>
                <td>
                    @if($checkIn->is_verified)
                        <span class="badge badge-success">✓ Verified (within 10m)</span>
                    @else
                        <span class="badge badge-danger">✗ Not Verified</span>
                    @endif
                </td>
            </tr>
            <tr><td class="text-muted">Distance</td><td><strong>{{ $checkIn->distance_from_store }}m</strong> from store</td></tr>
            <tr><td class="text-muted">AM GPS</td><td>{{ $checkIn->latitude }}, {{ $checkIn->longitude }}</td></tr>
            <tr><td class="text-muted">Store GPS</td><td>{{ $checkIn->store->latitude }}, {{ $checkIn->store->longitude }}</td></tr>
        </table>
    </div>

    <div class="card">
        <div class="card-header"><h3>📷 Photo Proof</h3></div>
        <img src="{{ url('storage/' . $checkIn->photo_path) }}" alt="Check-in photo"
            style="width:100%;border-radius:8px;max-height:400px;object-fit:cover;">
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>📍 Location Map</h3></div>
    <div id="map" style="height:400px;border-radius:8px;"></div>
</div>
@endsection

@section('scripts')
<script>
    var storeLat = {{ $checkIn->store->latitude }};
    var storeLng = {{ $checkIn->store->longitude }};
    var checkinLat = {{ $checkIn->latitude }};
    var checkinLng = {{ $checkIn->longitude }};

    var map = L.map('map').setView([storeLat, storeLng], 18);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // Store marker (blue)
    var storeIcon = L.divIcon({
        className: 'custom-icon',
        html: '<div style="background:#3b82f6;color:white;padding:6px 10px;border-radius:8px;font-size:12px;font-weight:600;white-space:nowrap;">🏪 Store</div>',
        iconSize: [80, 30], iconAnchor: [40, 15]
    });
    L.marker([storeLat, storeLng], {icon: storeIcon}).addTo(map);

    // Check-in marker
    var checkinIcon = L.divIcon({
        className: 'custom-icon',
        html: '<div style="background:{{ $checkIn->is_verified ? "#22c55e" : "#ef4444" }};color:white;padding:6px 10px;border-radius:8px;font-size:12px;font-weight:600;white-space:nowrap;">📍 AM ({{ $checkIn->distance_from_store }}m)</div>',
        iconSize: [120, 30], iconAnchor: [60, 15]
    });
    L.marker([checkinLat, checkinLng], {icon: checkinIcon}).addTo(map);

    // 10m radius circle
    L.circle([storeLat, storeLng], {
        radius: 10,
        color: '{{ $checkIn->is_verified ? "#22c55e" : "#ef4444" }}',
        fillOpacity: 0.15,
        weight: 2
    }).addTo(map);

    // Line between points
    L.polyline([[storeLat, storeLng], [checkinLat, checkinLng]], {
        color: '#6366f1', dashArray: '8 4', weight: 2
    }).addTo(map);

    // Fit bounds
    map.fitBounds([[storeLat, storeLng], [checkinLat, checkinLng]], {padding: [50, 50], maxZoom: 19});
</script>
@endsection
