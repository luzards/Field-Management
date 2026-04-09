@extends('admin.layouts.app')
@section('title', 'Create Store')

@section('content')
<div class="page-header">
    <div>
        <h1>Create Store</h1>
        <div class="breadcrumb"><a href="/admin/stores">Stores</a> &bull; Create New</div>
    </div>
</div>

<div class="card" style="max-width: 700px;">
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error) {{ $error }}<br> @endforeach
        </div>
    @endif

    <form method="POST" action="/admin/stores">
        @csrf
        <div class="form-group">
            <label class="form-label">Store Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="form-group">
            <label class="form-label">Address *</label>
            <textarea name="address" class="form-control" required>{{ old('address') }}</textarea>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Latitude *</label>
                <input type="number" name="latitude" id="lat" class="form-control" step="0.00000001"
                    value="{{ old('latitude') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Longitude *</label>
                <input type="number" name="longitude" id="lng" class="form-control" step="0.00000001"
                    value="{{ old('longitude') }}" required>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">📍 Click on map to set location</label>
            <div id="map" class="map-container"></div>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Contact Name</label>
                <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Contact Phone</label>
                <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone') }}">
            </div>
        </div>
        <div class="flex-row">
            <button type="submit" class="btn btn-primary">Create Store</button>
            <a href="/admin/stores" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    var map = L.map('map').setView([-6.2088, 106.8456], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker;
    map.on('click', function(e) {
        if (marker) map.removeLayer(marker);
        marker = L.marker(e.latlng).addTo(map);
        document.getElementById('lat').value = e.latlng.lat.toFixed(8);
        document.getElementById('lng').value = e.latlng.lng.toFixed(8);
    });

    // If values exist, show marker
    var latVal = document.getElementById('lat').value;
    var lngVal = document.getElementById('lng').value;
    if (latVal && lngVal) {
        marker = L.marker([parseFloat(latVal), parseFloat(lngVal)]).addTo(map);
        map.setView([parseFloat(latVal), parseFloat(lngVal)], 15);
    }
</script>
@endsection
