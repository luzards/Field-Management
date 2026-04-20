<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$schedules = DB::table('schedules')
    ->orderBy('scheduled_date')
    ->get(['id', 'user_id', 'store_id', 'scheduled_date', 'status']);

echo "=== SCHEDULES IN DATABASE ===\n";
echo str_pad('ID', 5) . str_pad('USER', 6) . str_pad('SCHEDULED_DATE', 16) . str_pad('STATUS', 12) . "\n";
echo str_repeat('-', 42) . "\n";

foreach ($schedules as $s) {
    echo str_pad($s->id, 5) . str_pad($s->user_id, 6) . str_pad($s->scheduled_date, 16) . str_pad($s->status, 12) . "\n";
}

echo "\n=== TOTAL: " . count($schedules) . " schedules ===\n";

// Also show today's date for reference
echo "=== SERVER TODAY (UTC): " . date('Y-m-d') . " ===\n";
echo "=== Server timezone: " . date_default_timezone_get() . " ===\n";
