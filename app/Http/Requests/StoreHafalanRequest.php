<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\DB;

class StoreHafalanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'santri_id'      => ['required','exists:santri,id'],
            'surah_id'       => ['required','exists:surahs,id'],
            'tanggal_setor'  => ['required','date'],
            'ayat_mulai'     => ['required','integer','min:1'],
            'ayat_selesai'   => ['required','integer','gte:ayat_mulai'],
            'metode'         => ['required','in:setoran,murajaah,ziyadah'],
            'penilai_guru'   => ['nullable','string','max:100'],
            'catatan'        => ['nullable','string'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) return;

                $surahId = (int)$this->input('surah_id');
                $mulai   = (int)$this->input('ayat_mulai');
                $selesai = (int)$this->input('ayat_selesai');

                $jumlahAyat = DB::table('surahs')->where('id',$surahId)->value('jumlah_ayat');
                if (!$jumlahAyat) return;

                if ($selesai > $jumlahAyat) {
                    $validator->errors()->add('ayat_selesai', 'ayat_selesai melebihi jumlah ayat surah ('.$jumlahAyat.').');
                }
            }
        ];
    }
}