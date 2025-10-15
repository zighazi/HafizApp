<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Strata extends Model
{
    protected $table = 'strata';
    protected $fillable = ['stream','nama_strata'];
    public $timestamps = false;

    // relasi
    public function santris()     { return $this->hasMany(Santri::class, 'strata_id'); }
    public function enrollments() { return $this->hasMany(Enrollment::class, 'strata_id'); }

    // accessor seragam: $strata->nama
    public function getNamaAttribute(): string
    {
        return (string) ($this->nama_strata ?: $this->stream);
    }
}