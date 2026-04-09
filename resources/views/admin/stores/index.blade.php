@extends('admin.layouts.app')
@section('title', 'Stores')

@section('content')
<div class="page-header">
    <div>
        <h1>Stores</h1>
        <div class="breadcrumb">Management &bull; Stores</div>
    </div>
    <a href="/admin/stores/create" class="btn btn-primary">+ New Store</a>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="/admin/stores" class="flex-row">
            <input type="text" name="search" class="form-control" style="max-width:300px;"
                placeholder="Search store name or address..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-sm btn-outline">Search</button>
        </form>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>Name</th><th>Address</th><th>Contact</th><th>Schedules</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($stores as $store)
                <tr>
                    <td><strong>{{ $store->name }}</strong></td>
                    <td class="text-muted text-sm">{{ Str::limit($store->address, 40) }}</td>
                    <td>{{ $store->contact_name ?? '-' }} <br><span class="text-muted text-sm">{{ $store->contact_phone }}</span></td>
                    <td>{{ $store->schedules_count }}</td>
                    <td>
                        <span class="badge badge-{{ $store->is_active ? 'success' : 'danger' }}">
                            {{ $store->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <div class="flex-row">
                            <a href="/admin/stores/{{ $store->id }}/edit" class="btn btn-sm btn-outline">Edit</a>
                            <form method="POST" action="/admin/stores/{{ $store->id }}" onsubmit="return confirm('Delete this store?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-muted" style="text-align:center;padding:24px;">No stores found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $stores->appends(request()->query())->links('pagination::simple-default') }}</div>
</div>
@endsection
