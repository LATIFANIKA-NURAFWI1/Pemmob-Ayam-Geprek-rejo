<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * FormRequest untuk mengupdate jadwal shift yang sudah ada.
 */
class UpdateShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'    => ['required', 'integer', 'exists:users,id'],
            'shift_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],
            'position'   => ['required', Rule::in(['kasir', 'inventory', 'dapur'])],
            'notes'      => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'    => 'Staf wajib dipilih.',
            'user_id.exists'      => 'Staf tidak ditemukan.',
            'shift_date.required' => 'Tanggal shift wajib diisi.',
            'start_time.required' => 'Jam mulai wajib diisi.',
            'end_time.required'   => 'Jam selesai wajib diisi.',
            'end_time.after'      => 'Jam selesai harus setelah jam mulai.',
            'position.in'         => 'Posisi harus salah satu dari: kasir, inventory, dapur.',
        ];
    }
}
