<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Tentukan apakah user berhak melakukan request ini.
     */
    public function authorize(): bool
    {
        return true; // semua user yang login boleh update profilnya
    }

    /**
     * Aturan validasi untuk update profil.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],
            // kalau nanti mau tambah field lain tinggal disini
            // 'phone' => ['nullable', 'string', 'max:20'],
            // 'avatar' => ['nullable', 'image', 'max:1024'],
        ];
    }
}