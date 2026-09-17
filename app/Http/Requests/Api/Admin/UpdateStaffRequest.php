<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * FormRequest untuk mengupdate data staf yang sudah ada.
 * Password bersifat opsional — hanya diupdate jika dikirim.
 */
class UpdateStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Ambil ID staf dari URL parameter agar validasi unique email bisa mengabaikan dirinya sendiri
        $staffId = $this->route('staff');

        return [
            'name'      => ['required', 'string', 'min:2', 'max:100'],
            'email'     => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($staffId),
            ],
            // Password nullable saat update — hanya diproses jika dikirim
            'password'  => ['nullable', 'string', 'min:8'],
            'role'      => ['required', Rule::in(['kasir', 'kds', 'inventory'])],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Nama staf wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique'   => 'Email sudah digunakan oleh akun lain.',
            'password.min'   => 'Password minimal 8 karakter.',
            'role.in'        => 'Role harus salah satu dari: kasir, kds, inventory.',
        ];
    }
}
