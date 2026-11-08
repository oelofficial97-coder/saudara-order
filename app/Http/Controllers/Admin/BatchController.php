<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BatchRequest;
use App\Models\BusinessSetting;
use App\Models\Product;
use App\Models\ProductionBatch;
use App\Services\BatchService;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index()
    {
        return view('admin.batches.index', [
            'batches' => ProductionBatch::latest('id')->paginate(12),
            'activeBatchId' => BusinessSetting::where('singleton_key', 1)->value('active_batch_id'),
            'settingsReady' => BusinessSetting::where('singleton_key', 1)->exists(),
        ]);
    }

    public function create()
    {
        return view('admin.batches.form', ['batch' => new ProductionBatch, 'product' => Product::where('is_active', true)->first()]);
    }

    public function store(BatchRequest $request, BatchService $service)
    {
        $service->create($request->user(), $request->validated());

        return to_route('admin.batches.index')->with('status', 'Batch berhasil dibuat. Penerimaan masih tertutup.');
    }

    public function edit(ProductionBatch $batch)
    {
        return view('admin.batches.form', ['batch' => $batch, 'product' => $batch->product]);
    }

    public function update(BatchRequest $request, ProductionBatch $batch, BatchService $service)
    {
        $service->update($request->user(), $batch, $request->validated());

        return to_route('admin.batches.index')->with('status', 'Perubahan batch berhasil disimpan.');
    }

    public function open(Request $request, ProductionBatch $batch, BatchService $service)
    {
        $service->setOpen($request->user(), $batch, true);

        return to_route('admin.batches.index')->with('status', 'Penerimaan batch dibuka.');
    }

    public function close(Request $request, ProductionBatch $batch, BatchService $service)
    {
        $service->setOpen($request->user(), $batch, false);

        return to_route('admin.batches.index')->with('status', 'Penerimaan batch ditutup.');
    }
}
