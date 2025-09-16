<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{    protected $table = 'santri';
    public $timestamps = true;

    protected $fillable = ['nis','nama','jenis_kelamin','kelas','strata','angkatan'];

    public function hafalans()
    {
        return $this->hasMany(Hafalan::class, 'santri_id');
    }
}