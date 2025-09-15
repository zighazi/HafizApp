<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hafalans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('surah_id')->constrained('surahs')->cascadeOnDelete()->cascadeOnUpdate();

            $table->date('tanggal_setor');
            $table->unsignedSmallInteger('ayat_mulai');
            $table->unsignedSmallInteger('ayat_selesai');

            $table->enum('metode', ['setoran','murajaah','ziyadah'])->default('setoran');
            $table->string('penilai_guru', 100)->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->index(['santri_id', 'tanggal_setor'], 'idx_haf_santri_tanggal');
            $table->index('surah_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hafalans');
    }
};
