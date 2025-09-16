<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hafalans', function (Blueprint $table) {
            $table->id();

            // FK ke tabel santri (nama tabel kamu: 'santri')
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();

            // FK ke tabel surahs
            $table->foreignId('surah_id')->constrained('surahs')->cascadeOnDelete();

            $table->unsignedSmallInteger('ayat_awal');
            $table->unsignedSmallInteger('ayat_akhir');
            $table->date('tanggal');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hafalans');
    }
};