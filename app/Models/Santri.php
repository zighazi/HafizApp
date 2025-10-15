<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Santri extends Model
{
    protected $table = 'santri'; // sesuai punyamu (singular)
    protected $fillable = ['nis','nama','jenis_kelamin','kelas_id','strata_id','angkatan_id'];

    // relasi langsung (FK di tabel santri)
    public function kelas()    { return $this->belongsTo(Kelas::class, 'kelas_id'); }
    public function strata()   { return $this->belongsTo(Strata::class, 'strata_id'); }
    public function angkatan() { return $this->belongsTo(Angkatan::class, 'angkatan_id'); }

    // relasi riwayat penempatan & hafalan
    public function enrollments() { return $this->hasMany(Enrollment::class); }
    public function hafalans()    { return $this->hasMany(Hafalan::class, 'santri_id'); }

    // enrollment aktif (kalau ada lebih dari satu, ambil yang terbaru)
    public function activeEnrollment()
    {
        return $this->hasOne(Enrollment::class)->latestOfMany('tahun_ajaran');
    }

    // helper tampilan
    public function getDisplayKelasAttribute(): ?string
    {
        // prioritas: FK langsung → enrollment aktif → null
        return $this->kelas->nama
            ?? $this->activeEnrollment?->kelas?->nama
            ?? null;
    }

    public function getDisplayStrataAttribute(): ?string
    {
        return $this->strata->nama
            ?? $this->activeEnrollment?->strata?->nama
            ?? null;
    }

    // scope pencarian
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        $term = trim((string) $term);
        if ($term === '') return $q;

        return $q->where(function (Builder $w) use ($term) {
            $w->where('nis','like',"%{$term}%")
              ->orWhere('nama','like',"%{$term}%");
        });
    }
}