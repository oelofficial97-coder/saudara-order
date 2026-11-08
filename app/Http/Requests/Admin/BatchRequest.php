<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-batches') ?? false;
    }

    public function rules(): array
    {
        return [
            'batch_name' => ['required', 'string', 'max:100'],
            'capacity_pouches' => ['required', 'integer', 'min:1', 'max:4294967295'],
            'estimated_ready_at' => ['nullable', 'date_format:Y-m-d\TH:i'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function attributes(): array
    {
        return ['batch_name' => 'nama batch', 'capacity_pouches' => 'kapasitas pouch', 'estimated_ready_at' => 'estimasi siap', 'notes' => 'catatan'];
    }

    public function messages(): array
    {
        return [
            'batch_name.required' => 'Nama batch wajib diisi.',
            'batch_name.string' => 'Nama batch harus berupa teks.',
            'batch_name.max' => 'Nama batch maksimal 100 karakter.',
            'capacity_pouches.required' => 'Kapasitas pouch wajib diisi.',
            'capacity_pouches.integer' => 'Kapasitas harus berupa jumlah pouch bulat.',
            'capacity_pouches.min' => 'Kapasitas minimal satu pouch.',
            'capacity_pouches.max' => 'Kapasitas melebihi batas penyimpanan.',
            'estimated_ready_at.date_format' => 'Estimasi siap harus berisi tanggal dan jam yang valid.',
            'notes.string' => 'Catatan harus berupa teks.',
            'notes.max' => 'Catatan maksimal 5.000 karakter.',
        ];
    }
}
