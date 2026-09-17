<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * FormRequest untuk membuat staf baru.
 * Password WAJIB diisi saat create.
 */
class StoreStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya owner yang bisa membuat staf (dijaga middleware di route)
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'min:2', 'max:100'],
            'email'     => ['required', 'email', 'unique:users,email'],
            'password'  => ['required', 'string', 'min:8'],
            'role'      => ['required', Rule::in(['kasir', 'kds', 'inventory'])],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Nama staf wajib diisi.',
            'name.min'          => 'Nama minimal 2 karakter.',
            'email.required'    => 'Email wajib diisi.',
            'email.unique'      => 'Email sudah digunakan oleh akun lain.',
            'password.required' => 'Password wajib diisi saat membuat staf baru.',
            'password.min'      => 'Password minimal 8 karakter.',
            'role.in'           => 'Role harus salah satu dari: kasir, kds, inventory.',
        ];
    }
}
