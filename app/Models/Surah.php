<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Surah extends Model
{
    protected $table = 'surahs';
    public $timestamps = true;

    protected $fillable = ['nomor','nama_id','jumlah_ayat','kategori'];

    public function hafalans()
    {
        return $this->hasMany(Hafalan::class, 'surah_id');
    }
}