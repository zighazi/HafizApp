<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surahs', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('nomor')->unique();
            $table->string('nama_id', 100);     // Al-Fatihah, Al-Baqarah, dst.
            $table->unsignedSmallInteger('jumlah_ayat');
            $table->enum('kategori', ['Makkiyah','Madaniyah'])->nullable(); // opsional
            $table->timestamps();

            $table->index('nama_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surahs');
    }
};
