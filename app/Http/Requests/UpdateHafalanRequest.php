<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\DB;

class UpdateHafalanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'santri_id'      => ['sometimes','required','exists:santri,id'],
            'surah_id'       => ['sometimes','required','exists:surahs,id'],
            'tanggal_setor'  => ['sometimes','required','date'],
            'ayat_mulai'     => ['sometimes','required','integer','min:1'],
            'ayat_selesai'   => ['sometimes','required','integer','gte:ayat_mulai'],
            'metode'         => ['sometimes','required','in:setoran,murajaah,ziyadah'],
            'penilai_guru'   => ['nullable','string','max:100'],
            'catatan'        => ['nullable','string'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) return;

                $surahId = $this->input('surah_id');
                $mulai   = $this->input('ayat_mulai');
                $selesai = $this->input('ayat_selesai');

                if (!$surahId || !$mulai || !$selesai) return;

                $jumlahAyat = DB::table('surahs')->where('id',$surahId)->value('jumlah_ayat');
                if (!$jumlahAyat) return;

                if ((int)$selesai > (int)$jumlahAyat) {
                    $validator->errors()->add('ayat_selesai', 'ayat_selesai melebihi jumlah ayat surah ('.$jumlahAyat.').');
                }
            }
        ];
    }
}