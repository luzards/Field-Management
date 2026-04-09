<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CheckIn;
use App\Models\Schedule;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $user = $request->user();

        // Verify the schedule belongs to this user
        $schedule = Schedule::with('store')
            ->where('user_id', $user->id)
            ->where('id', $request->schedule_id)
            ->firstOrFail();

        // Check if already checked in
        if ($schedule->checkIn) {
            return response()->json([
                'success' => false,
                'message' => 'You have already checked in for this schedule',
            ], 422);
        }

        // Calculate distance from store using Haversine formula
        $distance = CheckIn::calculateDistance(
            $request->latitude,
            $request->longitude,
            $schedule->store->latitude,
            $schedule->store->longitude
        );

        // Verify if within 10 meter radius
        $isVerified = $distance <= 10;

        // Store the photo
        $photoPath = $request->file('photo')->store('check-ins', 'public');

        // Create check-in record
        $checkIn = CheckIn::create([
            'schedule_id' => $schedule->id,
            'user_id' => $user->id,
            'store_id' => $schedule->store_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo_path' => $photoPath,
            'is_verified' => $isVerified,
            'distance_from_store' => round($distance, 2),
            'checked_in_at' => now(),
        ]);

        // Update schedule status
        $schedule->update(['status' => 'completed']);

        ActivityLog::log(
            $user->id,
            'check_in',
            "Checked in at {$schedule->store->name}. Distance: {$checkIn->distance_from_store}m. Verified: " . ($isVerified ? 'Yes' : 'No'),
            $request->ip()
        );

        return response()->json([
            'success' => true,
            'message' => $isVerified
                ? 'Check-in successful! Location verified.'
                : 'Check-in recorded, but you are ' . round($distance, 1) . 'm away from the store (max 10m).',
            'data' => [
                'id' => $checkIn->id,
                'is_verified' => $isVerified,
                'distance_from_store' => $checkIn->distance_from_store,
                'checked_in_at' => $checkIn->checked_in_at,
                'store_name' => $schedule->store->name,
                'photo_url' => url('storage/' . $photoPath),
            ],
        ], 201);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $checkIns = CheckIn::with(['schedule', 'store'])
            ->where('user_id', $user->id)
            ->orderBy('checked_in_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $checkIns->through(function ($checkIn) {
                return [
                    'id' => $checkIn->id,
                    'store' => [
                        'id' => $checkIn->store->id,
                        'name' => $checkIn->store->name,
                        'address' => $checkIn->store->address,
                    ],
                    'latitude' => $checkIn->latitude,
                    'longitude' => $checkIn->longitude,
                    'photo_url' => url('storage/' . $checkIn->photo_path),
                    'is_verified' => $checkIn->is_verified,
                    'distance_from_store' => $checkIn->distance_from_store,
                    'checked_in_at' => $checkIn->checked_in_at,
                ];
            }),
        ]);
    }
}
