<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::unprepared(<<<SQL
CREATE OR REPLACE VIEW v_santri_targets AS
SELECT
  s.id            AS santri_id,
  s.nis,
  s.nama          AS santri_nama,
  k.nama          AS kelas_nama,
  k.jenis         AS kelas_jenis,
  st.nama         AS strata_nama,
  st.target_baris,
  st.frekuensi
FROM santri s
JOIN kelas  k  ON k.id  = s.kelas_id
JOIN strata st ON st.id = s.strata_id;
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS v_santri_targets;');
    }
};
