<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BusinessSettingRequest;
use App\Models\AuditLog;
use App\Models\BusinessSetting;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BusinessSettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings', ['settings' => BusinessSetting::where('singleton_key', 1)->first() ?? new BusinessSetting]);
    }

    public function update(BusinessSettingRequest $request)
    {
        $data = $request->validated();
        DB::transaction(function () use ($request, $data) {
            // Serialize creation of the singleton without inventing contact or bank data.
            if (! Product::where('singleton_key', 1)->lockForUpdate()->first()) {
                throw ValidationException::withMessages(['business_name' => 'Produk belum tersedia. Jalankan seeder produk terlebih dahulu.']);
            }
            $settings = BusinessSetting::where('singleton_key', 1)->lockForUpdate()->first() ?? new BusinessSetting;
            $settings->fill($data)->save();
            $log = new AuditLog;
            $log->forceFill([
                'user_id' => $request->user()->id, 'event' => 'settings.updated',
                'description' => 'Informasi usaha diperbarui oleh pemilik.',
            ])->save();
        }, 3);

        return to_route('admin.settings.edit')->with('status', 'Pengaturan usaha berhasil disimpan.');
    }
}
