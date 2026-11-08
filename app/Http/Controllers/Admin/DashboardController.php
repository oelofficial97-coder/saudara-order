<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Product;
use App\Models\ProductionBatch;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $settings = BusinessSetting::with('activeBatch.product')->where('singleton_key', 1)->first();
        $activeBatch = $settings?->activeBatch;

        return view('admin.dashboard', [
            'productCount' => Product::where('is_active', true)->count(),
            'activeBatch' => $activeBatch,
            'remaining' => $activeBatch ? max(0, $activeBatch->capacity_pouches - $activeBatch->allocated_pouches) : null,
            'recentBatches' => ProductionBatch::latest('id')->limit(5)->get(),
            'settingsReady' => $settings !== null,
        ]);
    }
}
