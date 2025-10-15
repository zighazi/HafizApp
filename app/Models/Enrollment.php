<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'santri_id','tahun_ajaran','angkatan_id',
        'kelas_id','strata_id','promoted_at',
    ];

    protected $casts = [
        'promoted_at' => 'datetime',
    ];

    // relasi
    public function santri()   { return $this->belongsTo(Santri::class); }
    public function kelas()    { return $this->belongsTo(Kelas::class); }
    public function strata()   { return $this->belongsTo(Strata::class); }
    public function angkatan() { return $this->belongsTo(Angkatan::class); }
}