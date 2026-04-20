<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

// Simulate the FULL API response JSON that the controller sends
// Logged in as John Doe (user_id=2), week=2026-04-20
$user_id = 2;
$weekParam = '2026-04-20';

$startOfWeek = Carbon::parse($weekParam)->startOfWeek(Carbon::MONDAY);
$endOfWeek   = $startOfWeek->copy()->endOfWeek(Carbon::SUNDAY);

$schedules = \App\Models\Schedule::with(['store', 'creator', 'checkIn'])
    ->where('user_id', $user_id)
    ->whereBetween('scheduled_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
    ->orderBy('scheduled_date')
    ->orderBy('start_time')
    ->get();

echo "=== Total schedules from DB: " . $schedules->count() . " ===\n\n";

foreach ($schedules as $schedule) {
    echo "--- Schedule id={$schedule->id} ---\n";
    try {
        $mapped = [
            'id'             => $schedule->id,
            'store'          => [
                'id'        => $schedule->store->id,
                'name'      => $schedule->store->name,
                'address'   => $schedule->store->address,
                'latitude'  => $schedule->store->latitude,
                'longitude' => $schedule->store->longitude,
            ],
            'scheduled_date' => $schedule->scheduled_date->format('Y-m-d'),
            'start_time'     => $schedule->start_time,
            'end_time'       => $schedule->end_time,
            'notes'          => $schedule->notes,
            'status'         => $schedule->status,
            'created_by'     => [
                'id'   => $schedule->creator->id,
                'name' => $schedule->creator->name,
            ],
            'check_in'       => $schedule->checkIn ? [
                'id'                 => $schedule->checkIn->id,
                'checked_in_at'      => $schedule->checkIn->checked_in_at,
                'is_verified'        => $schedule->checkIn->is_verified,
                'distance_from_store'=> $schedule->checkIn->distance_from_store,
            ] : null,
        ];
        echo "  ✅ OK: scheduled_date={$mapped['scheduled_date']} store={$mapped['store']['name']}\n";
        echo "     start_time={$mapped['start_time']} end_time={$mapped['end_time']}\n";
        echo "     created_by_id={$mapped['created_by']['id']} store_id={$mapped['store']['id']}\n";
    } catch (\Throwable $e) {
        echo "  ❌ ERROR building JSON: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Checking the forUser scope more carefully ===\n";
$scopedId = \App\Models\Schedule::forUser($user_id)
    ->whereBetween('scheduled_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
    ->pluck('id');
echo "IDs returned by forUser scope: " . $scopedId->implode(', ') . "\n";
