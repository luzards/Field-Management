<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== ALL USERS ===\n";
$users = DB::table('users')->get(['id', 'name', 'email', 'role']);
foreach ($users as $u) {
    echo "  id={$u->id}  name={$u->name}  email={$u->email}  role={$u->role}\n";
}

echo "\n=== SCHEDULES BY USER (this week 2026-04-20 to 2026-04-26) ===\n";
$schedules = DB::table('schedules')
    ->whereBetween('scheduled_date', ['2026-04-20', '2026-04-26'])
    ->orderBy('user_id')
    ->orderBy('scheduled_date')
    ->get(['id', 'user_id', 'scheduled_date', 'status']);

foreach ($schedules as $s) {
    echo "  id={$s->id}  user_id={$s->user_id}  date={$s->scheduled_date}  status={$s->status}\n";
}
