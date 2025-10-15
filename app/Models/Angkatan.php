<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Angkatan extends Model
{
    protected $table = 'angkatans'; // sesuai punyamu
    protected $fillable = ['tahun', 'label'];
    public $timestamps = false;

    // relasi
    public function santris()    { return $this->hasMany(Santri::class); }
    public function enrollments(){ return $this->hasMany(Enrollment::class); }

    // accessor seragam: $angkatan->nama
    public function getNamaAttribute(): string
    {
        return (string) ($this->label ?: $this->tahun);
    }

    // label gabungan
    public function getTahunLabelAttribute(): string
    {
        return $this->label ? "{$this->tahun} - {$this->label}" : (string) $this->tahun;
    }
}