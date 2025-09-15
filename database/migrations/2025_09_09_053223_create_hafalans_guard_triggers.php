<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS hafalans_before_insert;');
        DB::unprepared('DROP TRIGGER IF EXISTS hafalans_before_update;');

        DB::unprepared(<<<'SQL'
CREATE TRIGGER hafalans_before_insert
BEFORE INSERT ON hafalans
FOR EACH ROW
BEGIN
  DECLARE v_jml SMALLINT;

  SELECT jumlah_ayat INTO v_jml FROM surahs WHERE id = NEW.surah_id;

  IF NEW.ayat_mulai < 1 OR NEW.ayat_selesai < NEW.ayat_mulai THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Rentang ayat tidak valid';
  END IF;

  IF v_jml IS NULL OR NEW.ayat_selesai > v_jml THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'ayat_selesai melebihi jumlah ayat surah';
  END IF;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE TRIGGER hafalans_before_update
BEFORE UPDATE ON hafalans
FOR EACH ROW
BEGIN
  DECLARE v_jml SMALLINT;

  SELECT jumlah_ayat INTO v_jml FROM surahs WHERE id = NEW.surah_id;

  IF NEW.ayat_mulai < 1 OR NEW.ayat_selesai < NEW.ayat_mulai THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Rentang ayat tidak valid';
  END IF;

  IF v_jml IS NULL OR NEW.ayat_selesai > v_jml THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'ayat_selesai melebihi jumlah ayat surah';
  END IF;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS hafalans_before_insert;');
        DB::unprepared('DROP TRIGGER IF EXISTS hafalans_before_update;');
    }
};
