<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::unprepared(<<<SQL
CREATE OR REPLACE VIEW v_rekap_kelas_harian_tahfizh AS
SELECT
  k.kelas_nama,
  k.kelas_jenis,
  k.tanggal_setor,
  COUNT(*) AS total_setoran,
  SUM(CASE WHEN k.status_tuntas=1 THEN 1 ELSE 0 END) AS total_tuntas
FROM v_hafalan_evaluasi k
WHERE k.kelas_jenis = 'tahfizh'
GROUP BY k.kelas_nama, k.kelas_jenis, k.tanggal_setor;
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS v_rekap_kelas_harian_tahfizh;');
    }
};