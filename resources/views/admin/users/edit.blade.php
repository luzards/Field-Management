@extends('admin.layouts.app')
@section('title', 'Edit ' . $user->name)

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Area Manager</h1>
        <div class="breadcrumb"><a href="/admin/users">Area Managers</a> &bull; {{ $user->name }}</div>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error) {{ $error }}<br> @endforeach
        </div>
    @endif

    <form method="POST" action="/admin/users/{{ $user->id }}">
        @csrf @method('PUT')
        <div class="form-group">
            <label class="form-label">Full Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>
        <div class="form-group">
            <label class="form-label">Email Address *</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>
        <div class="form-group">
            <label class="form-label">New Password <span class="text-muted text-sm">(leave blank to keep current)</span></label>
            <input type="password" name="password" class="form-control" minlength="8">
        </div>
        <div class="form-group">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>
        <div class="form-group">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
        </div>
        <div class="form-group">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control">{{ old('address', $user->address) }}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Status</label>
            <select name="is_active" class="form-control">
                <option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
                <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="flex-row">
            <button type="submit" class="btn btn-primary">Update Area Manager</button>
            <a href="/admin/users" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
