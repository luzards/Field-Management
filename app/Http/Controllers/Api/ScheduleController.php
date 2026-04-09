<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Schedule::with(['store', 'creator', 'checkIn'])
            ->forUser($user->id);

        // Filter by specific date
        if ($request->has('date')) {
            $query->forDate($request->date);
        }

        // Filter by week (pass start date of week)
        if ($request->has('week')) {
            $startOfWeek = Carbon::parse($request->week)->startOfWeek();
            $endOfWeek = $startOfWeek->copy()->endOfWeek();
            $query->forWeek($startOfWeek, $endOfWeek);
        }

        // Default: today's schedules if no filter
        if (! $request->has('date') && ! $request->has('week')) {
            $query->forDate(Carbon::today());
        }

        $schedules = $query->orderBy('scheduled_date')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $schedules->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'store' => [
                        'id' => $schedule->store->id,
                        'name' => $schedule->store->name,
                        'address' => $schedule->store->address,
                        'latitude' => $schedule->store->latitude,
                        'longitude' => $schedule->store->longitude,
                    ],
                    'scheduled_date' => $schedule->scheduled_date->format('Y-m-d'),
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'notes' => $schedule->notes,
                    'status' => $schedule->status,
                    'created_by' => [
                        'id' => $schedule->creator->id,
                        'name' => $schedule->creator->name,
                    ],
                    'check_in' => $schedule->checkIn ? [
                        'id' => $schedule->checkIn->id,
                        'checked_in_at' => $schedule->checkIn->checked_in_at,
                        'is_verified' => $schedule->checkIn->is_verified,
                        'distance_from_store' => $schedule->checkIn->distance_from_store,
                    ] : null,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = $request->user();

        $schedule = Schedule::create([
            'user_id' => $user->id,
            'store_id' => $request->store_id,
            'scheduled_date' => $request->scheduled_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'notes' => $request->notes,
            'status' => 'pending',
            'created_by' => $user->id,
        ]);

        $schedule->load(['store', 'creator']);

        ActivityLog::log($user->id, 'schedule_create', "Created schedule #{$schedule->id}", $request->ip());

        return response()->json([
            'success' => true,
            'message' => 'Schedule created successfully',
            'data' => $schedule,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $schedule = Schedule::where('user_id', $user->id)->findOrFail($id);

        if ($schedule->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot modify a completed schedule',
            ], 422);
        }

        $request->validate([
            'store_id' => 'sometimes|exists:stores,id',
            'scheduled_date' => 'sometimes|date',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',
            'notes' => 'nullable|string|max:500',
            'status' => 'sometimes|in:pending,cancelled',
        ]);

        $schedule->fill($request->only([
            'store_id', 'scheduled_date', 'start_time', 'end_time', 'notes', 'status',
        ]));
        $schedule->save();
        $schedule->load(['store', 'creator']);

        ActivityLog::log($user->id, 'schedule_update', "Updated schedule #{$schedule->id}", $request->ip());

        return response()->json([
            'success' => true,
            'message' => 'Schedule updated successfully',
            'data' => $schedule,
        ]);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $schedule = Schedule::with(['store', 'creator', 'checkIn'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $schedule,
        ]);
    }
}
