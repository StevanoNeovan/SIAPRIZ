<?php
// app/Http/Requests/UploadPenjualanRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadPenjualanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only Administrator can upload
        return auth()->user()->isAdministrator();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:csv,xlsx,xls',
                'max:10240', // Max 10MB
            ],
            'id_marketplace' => [
                'nullable',
                'exists:marketplace,id_marketplace',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'file.required' => 'File CSV/Excel wajib diupload.',
            'file.file' => 'File yang diupload tidak valid.',
            'file.mimes' => 'File harus berformat CSV, XLSX, atau XLS.',
            'file.max' => 'Ukuran file maksimal 10MB.',
            'id_marketplace.exists' => 'Marketplace yang dipilih tidak valid.',
        ];
    }
}