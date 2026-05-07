<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PredictionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'total_target'      => ['required', 'integer', 'min:1', 'max:99999'],
            'motor_baru'        => ['nullable', 'array'],
            'motor_baru.*'      => ['integer', 'exists:motor,id'],
            
            // Tambahkan rule ini agar Request mengizinkan input string periode
            'periode_penjualan' => ['nullable', 'string'],
            'periode_stok'      => ['nullable', 'string'],
            'periode_opini'     => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'total_target.required' => 'Total target unit wajib diisi.',
            'total_target.integer'  => 'Total target harus berupa angka bulat.',
            'total_target.min'      => 'Total target minimal 1 unit.',
            'motor_baru.*.exists'   => 'Motor yang dipilih tidak valid.',
        ];
    }
}