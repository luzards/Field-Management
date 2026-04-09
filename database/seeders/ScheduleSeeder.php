<?php

namespace Database\Seeders;

use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();
        $users = [2, 3, 4]; // AM user IDs
        $stores = [1, 2, 3, 4, 5]; // store IDs

        foreach ($users as $userId) {
            // Create schedules for this week
            for ($i = 0; $i < 5; $i++) {
                $date = $today->copy()->startOfWeek()->addDays($i);
                $storeId = $stores[array_rand($stores)];

                Schedule::create([
                    'user_id' => $userId,
                    'store_id' => $storeId,
                    'scheduled_date' => $date,
                    'start_time' => '09:00',
                    'end_time' => '11:00',
                    'notes' => 'Regular store visit',
                    'status' => $date->isPast() ? 'pending' : 'pending',
                    'created_by' => 1, // admin
                ]);

                // Afternoon schedule for some days
                if ($i % 2 === 0) {
                    $afternoonStore = $stores[array_rand($stores)];
                    Schedule::create([
                        'user_id' => $userId,
                        'store_id' => $afternoonStore,
                        'scheduled_date' => $date,
                        'start_time' => '14:00',
                        'end_time' => '16:00',
                        'notes' => 'Afternoon visit',
                        'status' => 'pending',
                        'created_by' => 1,
                    ]);
                }
            }
        }
    }
}
