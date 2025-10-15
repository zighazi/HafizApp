<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHafalanRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Atur ke policy kalau sudah pakai role/permission
        return true;
    }

    public function rules(): array
    {
        return [
            'santri_id'     => ['required', 'integer', 'exists:santri,id'],
            'surah_id'      => ['required', 'integer', 'exists:surahs,id'],
            'ayat_mulai'    => ['required', 'integer', 'min:1'],
            'ayat_selesai'  => ['required', 'integer', 'gte:ayat_mulai'],
            'tanggal_setor' => ['required', 'date'],
            'metode'        => ['nullable', 'string', 'max:100'],
            'penilai_guru'  => ['nullable', 'string', 'max:100'],
            'catatan'       => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'santri_id.required' => 'Nama santri wajib dipilih.',
            'surah_id.required'  => 'Surah wajib dipilih.',
            'ayat_mulai.min'     => 'Ayat mulai minimal 1.',
            'ayat_selesai.gte'   => 'Ayat akhir tidak boleh lebih kecil dari ayat mulai.',
        ];
    }
}