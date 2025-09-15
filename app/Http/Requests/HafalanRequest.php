<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HafalanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // atur ke policy bila sudah pakai auth/role
    }

    public function rules(): array
    {
        return [
            'santri_id'     => ['required','exists:santri,id'],
            'tanggal_setor' => ['required','date'],
            'surah_id'   => ['required','exists:surahs,id'],
            'ayat_mulai'    => ['required','integer','min:1'],
            'ayat_selesai'  => ['required','integer','gte:ayat_mulai'],
            'status'        => ['required','in:tuntas,belum'],
            // optional field lain, sesuaikan skema:
            // 'catatan'    => ['nullable','string','max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'santri_id.required' => 'Santri wajib dipilih.',
            'santri_id.exists'   => 'Santri tidak valid.',
            'tanggal_setor.*'    => 'Tanggal setor wajib dan harus bertipe tanggal.',
            'surah_id.*'      => 'Surah wajib dan harus nomor surah yang valid.',
            'ayat_mulai.*'       => 'Ayat mulai harus angka >= 1.',
            'ayat_selesai.gte'   => 'Ayat selesai tidak boleh lebih kecil dari ayat mulai.',
            'status.in'          => 'Status harus tuntas atau belum.',
        ];
    }
}