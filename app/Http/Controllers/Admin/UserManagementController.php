<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'am');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->withCount(['schedules', 'checkIns'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'am',
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => true,
        ]);

        ActivityLog::log(auth()->id(), 'am_create', "Created AM: {$user->name} ({$user->email})", $request->ip());

        return redirect('/admin/users')->with('success', 'Area Manager created successfully.');
    }

    public function edit($id)
    {
        $user = User::where('role', 'am')->findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::where('role', 'am')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $user->fill($request->only(['name', 'email', 'phone', 'address']));
        $user->is_active = $request->boolean('is_active');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        ActivityLog::log(auth()->id(), 'am_update', "Updated AM: {$user->name}", $request->ip());

        return redirect('/admin/users')->with('success', 'Area Manager updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::where('role', 'am')->findOrFail($id);
        $name = $user->name;
        $user->delete();

        ActivityLog::log(auth()->id(), 'am_delete', "Deleted AM: {$name}", request()->ip());

        return redirect('/admin/users')->with('success', 'Area Manager deleted successfully.');
    }

    public function show($id)
    {
        $user = User::where('role', 'am')
            ->withCount(['schedules', 'checkIns'])
            ->findOrFail($id);

        $recentCheckIns = $user->checkIns()
            ->with('store')
            ->orderBy('checked_in_at', 'desc')
            ->limit(20)
            ->get();

        $recentSchedules = $user->schedules()
            ->with('store')
            ->orderBy('scheduled_date', 'desc')
            ->limit(20)
            ->get();

        return view('admin.users.show', compact('user', 'recentCheckIns', 'recentSchedules'));
    }
}
