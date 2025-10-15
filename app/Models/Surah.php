<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Surah extends Model
{
    protected $table = 'surahs';
    protected $fillable = ['nomor','nama_id','jumlah_ayat','kategori'];

    public function hafalans()
    {
        return $this->hasMany(Hafalan::class, 'surah_id');
    }

    // seragam: $surah->nama
    public function getNamaAttribute(): string
    {
        return (string) ($this->nama_id ?? '');
    }
}