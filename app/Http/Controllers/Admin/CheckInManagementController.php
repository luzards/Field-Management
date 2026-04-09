<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CheckIn;
use Illuminate\Http\Request;

class CheckInManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = CheckIn::with(['user', 'store', 'schedule']);

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('verified') && $request->verified !== '') {
            $query->where('is_verified', $request->verified);
        }

        if ($request->has('date') && $request->date) {
            $query->whereDate('checked_in_at', $request->date);
        }

        $checkIns = $query->orderBy('checked_in_at', 'desc')
            ->paginate(20);

        return view('admin.checkins.index', compact('checkIns'));
    }

    public function show($id)
    {
        $checkIn = CheckIn::with(['user', 'store', 'schedule'])
            ->findOrFail($id);

        return view('admin.checkins.show', compact('checkIn'));
    }
}
