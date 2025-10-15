<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $fillable = ['grade','kode','nama_kelas','stream','is_special','next_kelas_id'];
    public $timestamps = false;

    // relasi
    public function next()        { return $this->belongsTo(Kelas::class, 'next_kelas_id'); }
    public function santris()     { return $this->hasMany(Santri::class, 'kelas_id'); }
    public function enrollments() { return $this->hasMany(Enrollment::class, 'kelas_id'); }

    // accessor seragam: $kelas->nama
    public function getNamaAttribute(): string
    {
        return (string) ($this->nama_kelas ?: ($this->kode ?? ''));
    }

    // kombinasi singkat untuk tampilan
    public function getDisplayAttribute(): string
    {
        $bagian = array_filter([
            $this->grade,
            $this->nama,    // dari accessor
            $this->stream,
        ], fn($v) => filled($v));
        return implode(' ', $bagian);
    }
}