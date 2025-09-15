<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ANGKATAN
        Schema::create('angkatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            // gunakan smallInteger(4) agar portable (daripada YEAR)
            $table->unsignedSmallInteger('tahun_mulai');
            $table->unsignedSmallInteger('tahun_selesai');
            $table->timestamps();
        });

        // KELAS
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->foreignId('angkatan_id')->constrained('angkatan')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('jenis', ['tahfizh', 'reguler']);
            $table->timestamps();

            $table->unique(['nama', 'angkatan_id'], 'uq_kelas_nama_angkatan');
            $table->index('angkatan_id', 'idx_kelas_angkatan');
        });

        // STRATA
        Schema::create('strata', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);
            $table->unsignedInteger('target_baris');
            $table->enum('frekuensi', ['harian', 'mingguan']);
            $table->enum('jenis_kelas', ['tahfizh', 'reguler']);
            $table->timestamps();

            $table->unique(['nama', 'jenis_kelas'], 'uq_strata_nama_jenis');
            // HAPUS -> $table->check('target_baris > 0'); // tidak didukung di Blueprint
        });

        // SANTRI
        Schema::create('santri', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 20)->unique();
            $table->string('nama', 200);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->foreignId('angkatan_id')->constrained('angkatan')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('strata_id')->constrained('strata')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();

            $table->index(['kelas_id', 'strata_id'], 'idx_santri_kelas_strata');
            $table->index('angkatan_id', 'idx_santri_angkatan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('santri');
        Schema::dropIfExists('strata');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('angkatan');
    }
};
