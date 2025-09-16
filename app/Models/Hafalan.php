<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hafalan extends Model
{
    protected $table = 'hafalans';

    protected $fillable = [
        'santri_id',
        'surah_id',
        'tanggal_setor',
        'ayat_mulai',
        'ayat_selesai',
        'metode',
        'penilai_guru',
        'catatan',
    ];

    protected $casts = [
        'tanggal_setor' => 'date',
    ];

    public function santri()
    {
        return $this->belongsTo(\App\Models\Santri::class, 'santri_id');
    }

    public function surah()
    {
        return $this->belongsTo(\App\Models\Surah::class, 'surah_id');
    }
}