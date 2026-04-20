<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Simulate the full API response for schedule id=36
$schedule = \App\Models\Schedule::with(['store', 'creator', 'checkIn'])->find(36);

if (!$schedule) {
    echo "Schedule 36 not found!\n";
    exit(1);
}

echo "=== Schedule id=36 raw data ===\n";
echo "scheduled_date (raw):        " . $schedule->getAttributes()['scheduled_date'] . "\n";
echo "scheduled_date (cast):       " . $schedule->scheduled_date . "\n";
echo "scheduled_date->format Y-m-d: " . $schedule->scheduled_date->format('Y-m-d') . "\n";
echo "store:                       " . ($schedule->store ? $schedule->store->name : 'NULL - MISSING STORE!') . "\n";
echo "creator:                     " . ($schedule->creator ? $schedule->creator->name : 'NULL - MISSING CREATOR!') . "\n";
echo "store_id:                    " . $schedule->store_id . "\n";
echo "created_by:                  " . $schedule->created_by . "\n";
