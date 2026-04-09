@extends('admin.layouts.app')
@section('title', 'Edit ' . $store->name)

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Store</h1>
        <div class="breadcrumb"><a href="/admin/stores">Stores</a> &bull; {{ $store->name }}</div>
    </div>
</div>

<div class="card" style="max-width: 700px;">
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error) {{ $error }}<br> @endforeach
        </div>
    @endif

    <form method="POST" action="/admin/stores/{{ $store->id }}">
        @csrf @method('PUT')
        <div class="form-group">
            <label class="form-label">Store Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $store->name) }}" required>
        </div>
        <div class="form-group">
            <label class="form-label">Address *</label>
            <textarea name="address" class="form-control" required>{{ old('address', $store->address) }}</textarea>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Latitude *</label>
                <input type="number" name="latitude" id="lat" class="form-control" step="0.00000001"
                    value="{{ old('latitude', $store->latitude) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Longitude *</label>
                <input type="number" name="longitude" id="lng" class="form-control" step="0.00000001"
                    value="{{ old('longitude', $store->longitude) }}" required>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">📍 Click on map to change location</label>
            <div id="map" class="map-container"></div>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Contact Name</label>
                <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name', $store->contact_name) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Contact Phone</label>
                <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $store->contact_phone) }}">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Status</label>
            <select name="is_active" class="form-control">
                <option value="1" {{ $store->is_active ? 'selected' : '' }}>Active</option>
                <option value="0" {{ !$store->is_active ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="flex-row">
            <button type="submit" class="btn btn-primary">Update Store</button>
            <a href="/admin/stores" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    var lat = {{ $store->latitude }};
    var lng = {{ $store->longitude }};
    var map = L.map('map').setView([lat, lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([lat, lng]).addTo(map);

    map.on('click', function(e) {
        if (marker) map.removeLayer(marker);
        marker = L.marker(e.latlng).addTo(map);
        document.getElementById('lat').value = e.latlng.lat.toFixed(8);
        document.getElementById('lng').value = e.latlng.lng.toFixed(8);
    });
</script>
@endsection
