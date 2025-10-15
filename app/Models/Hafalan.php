<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Hafalan extends Model
{
    protected $table = 'hafalans';
    protected $fillable = [
        'santri_id','surah_id','ayat_mulai','ayat_selesai',
        'tanggal_setor','metode','penilai_guru','catatan',
    ];

    protected $casts = [
        'tanggal_setor' => 'date',
    ];

    // relasi
    public function santri() { return $this->belongsTo(Santri::class, 'santri_id'); }
    public function surah()  { return $this->belongsTo(Surah::class,  'surah_id'); }

    // scope rekap per bulan (pakai 'tanggal_setor', fallback created_at)
    public function scopeRekapPerBulan(Builder $q, int $limit = 6): Builder
    {
        $col = $this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), 'tanggal_setor')
            ? 'tanggal_setor' : 'created_at';

        return $q->selectRaw("DATE_FORMAT($col, '%Y-%m') as bulan, COUNT(*) as total")
                 ->groupBy('bulan')->orderBy('bulan','desc')->limit($limit);
    }
}