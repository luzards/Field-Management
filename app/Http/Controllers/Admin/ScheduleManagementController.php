<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Schedule;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedule::with(['user', 'store', 'checkIn']);

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('date') && $request->date) {
            $query->where('scheduled_date', $request->date);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $schedules = $query->orderBy('scheduled_date', 'desc')
            ->orderBy('start_time')
            ->paginate(20);

        $ams = User::where('role', 'am')->orderBy('name')->get();

        return view('admin.schedules.index', compact('schedules', 'ams'));
    }

    public function create()
    {
        $ams = User::where('role', 'am')->where('is_active', true)->orderBy('name')->get();
        $stores = Store::where('is_active', true)->orderBy('name')->get();
        return view('admin.schedules.create', compact('ams', 'stores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'store_id' => 'required|exists:stores,id',
            'scheduled_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string|max:500',
        ]);

        $schedule = Schedule::create([
            'user_id' => $request->user_id,
            'store_id' => $request->store_id,
            'scheduled_date' => $request->scheduled_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'notes' => $request->notes,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        ActivityLog::log(auth()->id(), 'schedule_create', "Created schedule #{$schedule->id} for user #{$request->user_id}", $request->ip());

        return redirect('/admin/schedules')->with('success', 'Schedule created successfully.');
    }

    public function edit($id)
    {
        $schedule = Schedule::findOrFail($id);
        $ams = User::where('role', 'am')->where('is_active', true)->orderBy('name')->get();
        $stores = Store::where('is_active', true)->orderBy('name')->get();
        return view('admin.schedules.edit', compact('schedule', 'ams', 'stores'));
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'store_id' => 'required|exists:stores,id',
            'scheduled_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string|max:500',
            'status' => 'required|in:pending,completed,missed,cancelled',
        ]);

        $schedule->fill($request->only([
            'user_id', 'store_id', 'scheduled_date', 'start_time', 'end_time', 'notes', 'status',
        ]));
        $schedule->save();

        ActivityLog::log(auth()->id(), 'schedule_update', "Updated schedule #{$schedule->id}", $request->ip());

        return redirect('/admin/schedules')->with('success', 'Schedule updated successfully.');
    }

    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        ActivityLog::log(auth()->id(), 'schedule_delete', "Deleted schedule #{$id}", request()->ip());

        return redirect('/admin/schedules')->with('success', 'Schedule deleted successfully.');
    }
}
