<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BusinessSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-batches') ?? false;
    }

    protected function prepareForValidation(): void
    {
        foreach (['bank_name', 'bank_account_number', 'bank_account_name'] as $field) {
            if ($this->exists($field) && is_string($this->input($field))) {
                $value = trim($this->input($field));
                $this->merge([$field => $value === '' ? null : $value]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'business_name' => ['required', 'string', 'max:100'],
            'whatsapp_number' => ['required', 'regex:/\A62[0-9]{8,13}\z/'],
            'pickup_address' => ['required', 'string', 'max:2000'],
            'pickup_maps_url' => ['nullable', 'url:https', 'max:500'],
            'bank_name' => ['nullable', 'string', 'max:50'],
            'bank_account_number' => ['nullable', 'string', 'max:50', 'regex:/\A[0-9]+\z/'],
            'bank_account_name' => ['nullable', 'string', 'max:100'],
        ];
    }
}
