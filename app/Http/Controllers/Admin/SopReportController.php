<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SopChecklist;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SopReportController extends Controller
{
    /**
     * List all stores with their average SOP scores and audit counts.
     */
    public function index(Request $request)
    {
        $stores = Store::where('is_active', true)
            ->withCount('sopChecklists')
            ->withAvg('sopChecklists', 'overall_value')
            ->orderBy('name')
            ->get();

        return view('admin.sop-reports.index', compact('stores'));
    }

    /**
     * Show detailed SOP checklists for a specific store.
     */
    public function show($id)
    {
        $store = Store::findOrFail($id);

        $checklists = SopChecklist::with(['user', 'checkIn'])
            ->where('store_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $avgScore = SopChecklist::where('store_id', $id)->avg('overall_value');
        $totalAudits = SopChecklist::where('store_id', $id)->count();

        // Score distribution
        $distribution = SopChecklist::where('store_id', $id)
            ->select(DB::raw('overall_value, COUNT(*) as count'))
            ->groupBy('overall_value')
            ->pluck('count', 'overall_value')
            ->toArray();

        return view('admin.sop-reports.show', compact('store', 'checklists', 'avgScore', 'totalAudits', 'distribution'));
    }
}
