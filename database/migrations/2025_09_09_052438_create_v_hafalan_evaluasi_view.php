<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::unprepared(<<<SQL
CREATE OR REPLACE VIEW v_hafalan_evaluasi AS
SELECT
  h.id              AS hafalan_id,
  h.tanggal_setor,
  s.id              AS santri_id,
  s.nis,
  s.nama            AS santri_nama,
  k.nama            AS kelas_nama,
  k.jenis           AS kelas_jenis,
  st.nama           AS strata_nama,
  st.target_baris,
  st.frekuensi,
  sr.nomor          AS surah_nomor,
  sr.nama_id        AS surah_nama,
  h.ayat_mulai,
  h.ayat_selesai,
  (h.ayat_selesai - h.ayat_mulai + 1) AS baris,
  ((h.ayat_selesai - h.ayat_mulai + 1) >= st.target_baris) AS status_tuntas
FROM hafalans h
JOIN santri   s  ON s.id  = h.santri_id
JOIN kelas    k  ON k.id  = s.kelas_id
JOIN strata   st ON st.id = s.strata_id
JOIN surahs   sr ON sr.id = h.surah_id;
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS v_hafalan_evaluasi;');
    }
};
