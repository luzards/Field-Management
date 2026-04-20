<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Carbon\Carbon;

// Simulate what the Flutter app sends as the week parameter
// Flutter sends: DateFormat('yyyy-MM-dd').format(_weekStart)
// _weekStart is computed as: Monday of this week at midnight
// Today is 2026-04-20 (Monday), so weekStart = 2026-04-20

$weekParam = '2026-04-20';

$startOfWeek = Carbon::parse($weekParam)->startOfWeek(Carbon::MONDAY);
$endOfWeek   = $startOfWeek->copy()->endOfWeek(Carbon::SUNDAY);

echo "=== Week Query Simulation ===\n";
echo "week_param:   $weekParam\n";
echo "startOfWeek:  {$startOfWeek->toDateTimeString()}\n";
echo "endOfWeek:    {$endOfWeek->toDateTimeString()}\n";
echo "\n";

// Query what the DB actually returns
$schedules = DB::table('schedules')
    ->where('user_id', 2) // logged-in user
    ->whereBetween('scheduled_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
    ->orderBy('scheduled_date')
    ->get(['id', 'scheduled_date', 'status']);

echo "=== Schedules returned for this week (user_id=2) ===\n";
foreach ($schedules as $s) {
    echo "  id={$s->id}  scheduled_date={$s->scheduled_date}  status={$s->status}\n";
}
echo "Total: " . count($schedules) . "\n";
