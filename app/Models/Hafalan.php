<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hafalan extends Model
{
    protected $table = 'hafalans';

    protected $fillable = [
        'santri_id', 'surah_id', 'ayat_awal', 'ayat_akhir', 'tanggal',
    ];

    protected $casts = ['tanggal' => 'date'];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function surah()
    {
        return $this->belongsTo(Surah::class, 'surah_id');
    }
}