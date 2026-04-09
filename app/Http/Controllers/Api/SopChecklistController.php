<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SopChecklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SopChecklistController extends Controller
{
    /**
     * List SOP checklists, optionally filtered by store_id or check_in_id.
     */
    public function index(Request $request)
    {
        $query = SopChecklist::with(['user', 'store', 'checkIn'])
            ->where('user_id', $request->user()->id);

        if ($request->has('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        if ($request->has('check_in_id')) {
            $query->where('check_in_id', $request->check_in_id);
        }

        $checklists = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $checklists,
        ]);
    }

    /**
     * Show a single SOP checklist.
     */
    public function show($id)
    {
        $checklist = SopChecklist::with(['user', 'store', 'checkIn'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $checklist,
        ]);
    }

    /**
     * Store a new SOP checklist with optional photo uploads.
     */
    public function store(Request $request)
    {
        $request->validate([
            'check_in_id' => 'required|exists:check_ins,id',
            'store_id' => 'required|exists:stores,id',
            'items' => 'required|json',
            'comments' => 'nullable|string|max:1000',
            'overall_value' => 'required|integer|min:1|max:10',
            'photos.*' => 'nullable|image|max:5120', // 5MB max per photo
        ]);

        $photoPaths = [];

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('sop-photos', 'public');
                $photoPaths[] = $path;
            }
        }

        $checklist = SopChecklist::create([
            'check_in_id' => $request->check_in_id,
            'user_id' => $request->user()->id,
            'store_id' => $request->store_id,
            'items' => json_decode($request->items, true),
            'photos' => $photoPaths,
            'comments' => $request->comments,
            'overall_value' => $request->overall_value,
        ]);

        $checklist->load(['user', 'store', 'checkIn']);

        return response()->json([
            'success' => true,
            'message' => 'SOP Checklist submitted successfully.',
            'data' => $checklist,
        ], 201);
    }
}
