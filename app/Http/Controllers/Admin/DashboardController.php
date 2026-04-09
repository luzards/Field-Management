<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CheckIn;
use App\Models\Schedule;
use App\Models\SopChecklist;
use App\Models\User;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $stats = [
            'total_ams' => User::where('role', 'am')->count(),
            'active_ams' => User::where('role', 'am')->where('is_active', true)->count(),
            'total_stores' => Store::where('is_active', true)->count(),
            'today_schedules' => Schedule::where('scheduled_date', $today)->count(),
            'today_completed' => Schedule::where('scheduled_date', $today)->where('status', 'completed')->count(),
            'today_checkins' => CheckIn::whereDate('checked_in_at', $today)->count(),
            'today_verified' => CheckIn::whereDate('checked_in_at', $today)->where('is_verified', true)->count(),
            'weekly_checkins' => CheckIn::whereBetween('checked_in_at', [
                $today->copy()->startOfWeek(), $today->copy()->endOfWeek()
            ])->count(),
        ];

        // Completion rate
        $stats['completion_rate'] = $stats['today_schedules'] > 0
            ? round(($stats['today_completed'] / $stats['today_schedules']) * 100, 1)
            : 0;

        // Recent check-ins
        $recentCheckIns = CheckIn::with(['user', 'store'])
            ->orderBy('checked_in_at', 'desc')
            ->limit(10)
            ->get();

        // Today's active schedules
        $todaySchedules = Schedule::with(['user', 'store', 'checkIn'])
            ->where('scheduled_date', $today)
            ->orderBy('start_time')
            ->get();

        // SOP stats
        $sopStats = [
            'total_audits' => SopChecklist::count(),
            'avg_score' => round(SopChecklist::avg('overall_value') ?? 0, 1),
            'today_audits' => SopChecklist::whereDate('created_at', $today)->count(),
        ];

        return view('admin.dashboard', compact('stats', 'recentCheckIns', 'todaySchedules', 'sopStats'));
    }
}
