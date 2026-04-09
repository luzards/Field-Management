<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Store::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $stores = $query->withCount('schedules')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.stores.index', compact('stores'));
    }

    public function create()
    {
        return view('admin.stores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'contact_phone' => 'nullable|string|max:20',
            'contact_name' => 'nullable|string|max:255',
        ]);

        $store = Store::create($request->only([
            'name', 'address', 'latitude', 'longitude', 'contact_phone', 'contact_name'
        ]));

        ActivityLog::log(auth()->id(), 'store_create', "Created store: {$store->name}", $request->ip());

        return redirect('/admin/stores')->with('success', 'Store created successfully.');
    }

    public function edit($id)
    {
        $store = Store::findOrFail($id);
        return view('admin.stores.edit', compact('store'));
    }

    public function update(Request $request, $id)
    {
        $store = Store::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'contact_phone' => 'nullable|string|max:20',
            'contact_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $store->fill($request->only([
            'name', 'address', 'latitude', 'longitude', 'contact_phone', 'contact_name'
        ]));
        $store->is_active = $request->boolean('is_active');
        $store->save();

        ActivityLog::log(auth()->id(), 'store_update', "Updated store: {$store->name}", $request->ip());

        return redirect('/admin/stores')->with('success', 'Store updated successfully.');
    }

    public function destroy($id)
    {
        $store = Store::findOrFail($id);
        $name = $store->name;
        $store->delete();

        ActivityLog::log(auth()->id(), 'store_delete', "Deleted store: {$name}", request()->ip());

        return redirect('/admin/stores')->with('success', 'Store deleted successfully.');
    }
}
