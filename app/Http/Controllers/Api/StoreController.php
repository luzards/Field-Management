<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $stores = Store::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'address', 'latitude', 'longitude', 'contact_phone', 'contact_name']);

        return response()->json([
            'success' => true,
            'data' => $stores,
        ]);
    }
}
