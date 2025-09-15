<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Pastikan bersih dulu jika pernah ada
        DB::unprepared('DROP TRIGGER IF EXISTS santri_before_insert;');
        DB::unprepared('DROP TRIGGER IF EXISTS santri_before_update;');

        // NOTE: Tidak perlu DELIMITER di DB::unprepared()
        DB::unprepared(<<<'SQL'
CREATE TRIGGER santri_before_insert
BEFORE INSERT ON santri
FOR EACH ROW
BEGIN
  DECLARE v_kelas_jenis ENUM('tahfizh','reguler');
  DECLARE v_strata_jenis ENUM('tahfizh','reguler');

  SELECT jenis INTO v_kelas_jenis FROM kelas WHERE id = NEW.kelas_id;
  SELECT jenis_kelas INTO v_strata_jenis FROM strata WHERE id = NEW.strata_id;

  IF v_kelas_jenis IS NULL OR v_strata_jenis IS NULL OR v_kelas_jenis <> v_strata_jenis THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Strata tidak sesuai dengan jenis kelas santri';
  END IF;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE TRIGGER santri_before_update
BEFORE UPDATE ON santri
FOR EACH ROW
BEGIN
  DECLARE v_kelas_jenis ENUM('tahfizh','reguler');
  DECLARE v_strata_jenis ENUM('tahfizh','reguler');

  SELECT jenis INTO v_kelas_jenis FROM kelas WHERE id = NEW.kelas_id;
  SELECT jenis_kelas INTO v_strata_jenis FROM strata WHERE id = NEW.strata_id;

  IF v_kelas_jenis IS NULL OR v_strata_jenis IS NULL OR v_kelas_jenis <> v_strata_jenis THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Strata tidak sesuai dengan jenis kelas santri';
  END IF;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS santri_before_insert;');
        DB::unprepared('DROP TRIGGER IF EXISTS santri_before_update;');
    }
};
