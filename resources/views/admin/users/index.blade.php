@extends('admin.layouts.app')
@section('title', 'Area Managers')

@section('content')
<div class="page-header">
    <div>
        <h1>Area Managers</h1>
        <div class="breadcrumb">Management &bull; Area Managers</div>
    </div>
    <a href="/admin/users/create" class="btn btn-primary">+ New AM</a>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="/admin/users" class="flex-row">
            <input type="text" name="search" class="form-control" style="max-width:300px;"
                placeholder="Search name, email, phone..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-sm btn-outline">Search</button>
        </form>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Schedules</th>
                    <th>Check-ins</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td class="text-muted">{{ $user->email }}</td>
                    <td>{{ $user->phone ?? '-' }}</td>
                    <td>{{ $user->schedules_count }}</td>
                    <td>{{ $user->check_ins_count }}</td>
                    <td>
                        @if($user->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex-row">
                            <a href="/admin/users/{{ $user->id }}" class="btn btn-sm btn-outline">View</a>
                            <a href="/admin/users/{{ $user->id }}/edit" class="btn btn-sm btn-outline">Edit</a>
                            <form method="POST" action="/admin/users/{{ $user->id }}" onsubmit="return confirm('Delete this AM?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:24px;" class="text-muted">No Area Managers found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $users->appends(request()->query())->links('pagination::simple-default') }}</div>
</div>
@endsection
