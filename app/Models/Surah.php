<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Surah extends Model
{
    protected $table = 'surahs'; // sesuaikan jika tabelmu bernama 'surah'
    public $timestamps = false;

    protected $fillable = [
        'nomor',        // 1..114
        'nama',         // Al-Fatihah, dst.
        'jumlah_ayat',  // 7, 286, ...
    ];
}