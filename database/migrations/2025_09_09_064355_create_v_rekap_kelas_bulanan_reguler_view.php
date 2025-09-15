<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Hapus view pekanan bila sebelumnya ada (opsional, aman jika belum pernah dibuat)
        DB::unprepared('DROP VIEW IF EXISTS v_rekap_kelas_mingguan_reguler;');

        DB::unprepared(<<<SQL
CREATE OR REPLACE VIEW v_rekap_kelas_bulanan_reguler AS
SELECT
  k.kelas_nama,
  k.kelas_jenis,
  DATE_FORMAT(k.tanggal_setor, '%Y-%m-01') AS bulan_mulai,
  LAST_DAY(k.tanggal_setor)                 AS bulan_selesai,
  DATE_FORMAT(k.tanggal_setor, '%Y-%m')     AS label_bulan,
  COUNT(*) AS total_setoran,
  SUM(CASE WHEN k.status_tuntas=1 THEN 1 ELSE 0 END) AS total_tuntas
FROM v_hafalan_evaluasi k
WHERE k.kelas_jenis = 'reguler'
GROUP BY
  k.kelas_nama, k.kelas_jenis,
  DATE_FORMAT(k.tanggal_setor, '%Y-%m-01'),
  LAST_DAY(k.tanggal_setor),
  DATE_FORMAT(k.tanggal_setor, '%Y-%m');
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS v_rekap_kelas_bulanan_reguler;');
    }
};