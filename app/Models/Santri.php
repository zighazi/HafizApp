<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    use HasFactory;

    protected $table = 'santri';

    protected $fillable = [
        'nis',
        'nama',
        'jenis_kelamin',   // 'L' atau 'P'
        'kelas_kode',      // contoh: X.E1
        'strata_nama',     // contoh: Tamhidi/Takmili/dll (boleh null)
        'angkatan_tahun',  // contoh: 2025
    ];

    protected $casts = [
        'angkatan_tahun' => 'integer',
    ];
}