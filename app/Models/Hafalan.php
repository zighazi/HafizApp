<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hafalan extends Model
{
    use HasFactory;

    protected $table = 'hafalans';

    protected $fillable = [
        'santri_id',
        'surah_id',
        'tanggal_setor',
        'ayat_mulai',
        'ayat_selesai',
        'status',
        'catatan',
    ];

    protected $dates = ['tanggal_setor'];

    /** Relasi ke Santri */
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    /** Relasi ke Surah */
    public function surah()
    {
        return $this->belongsTo(Surah::class, 'surah_id', 'id');
    }
}