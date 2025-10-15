<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya admin yang boleh pakai request ini
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        // Sesuaikan daftar role yang kamu gunakan di aplikasi
        return [
            'role' => 'required|string|in:admin,orangtua,guru,user',
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
        ];
    }
}