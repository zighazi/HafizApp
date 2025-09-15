<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Bersihkan dulu kalau ada sisa
        DB::unprepared('DROP TRIGGER IF EXISTS santri_before_insert;');
        DB::unprepared('DROP TRIGGER IF EXISTS santri_before_update;');

        // TRIGGER INSERT (tanpa DELIMITER, gunakan VARCHAR agar kompatibel)
        DB::unprepared(<<<'SQL'
CREATE TRIGGER santri_before_insert
BEFORE INSERT ON santri
FOR EACH ROW
BEGIN
  DECLARE v_kelas_jenis  VARCHAR(8);
  DECLARE v_strata_jenis VARCHAR(8);

  SELECT jenis        INTO v_kelas_jenis  FROM kelas  WHERE id = NEW.kelas_id;
  SELECT jenis_kelas  INTO v_strata_jenis FROM strata WHERE id = NEW.strata_id;

  IF v_kelas_jenis IS NULL OR v_strata_jenis IS NULL OR v_kelas_jenis <> v_strata_jenis THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Strata tidak sesuai dengan jenis kelas santri';
  END IF;
END
SQL);

        // TRIGGER UPDATE
        DB::unprepared(<<<'SQL'
CREATE TRIGGER santri_before_update
BEFORE UPDATE ON santri
FOR EACH ROW
BEGIN
  DECLARE v_kelas_jenis  VARCHAR(8);
  DECLARE v_strata_jenis VARCHAR(8);

  SELECT jenis        INTO v_kelas_jenis  FROM kelas  WHERE id = NEW.kelas_id;
  SELECT jenis_kelas  INTO v_strata_jenis FROM strata WHERE id = NEW.strata_id;

  IF v_kelas_jenis IS NULL OR v_strata_jenis IS NULL OR v_kelas_jenis <> v_strata_jenis THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Strata tidak sesuai dengan jenis kelas santri';
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
