<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Carbon\Carbon;

// Simulate EXACT JSON the API sends (John Doe = user_id=2, week 2026-04-20)
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

$data = $schedules->map(function ($schedule) {
    return [
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
        'check_in' => $schedule->checkIn ? [
            'id'                  => $schedule->checkIn->id,
            'checked_in_at'       => $schedule->checkIn->checked_in_at,
            'is_verified'         => $schedule->checkIn->is_verified,
            'distance_from_store' => $schedule->checkIn->distance_from_store,
        ] : null,
    ];
});

$json = json_encode(['success' => true, 'data' => $data], JSON_PRETTY_PRINT);
echo "=== EXACT API JSON RESPONSE ===\n";
echo $json . "\n";

echo "\n=== KEY FIELD VERIFICATION ===\n";
foreach ($data as $item) {
    echo "id={$item['id']}  scheduled_date=\"{$item['scheduled_date']}\"  type=" . gettype($item['scheduled_date']) . "\n";
}
