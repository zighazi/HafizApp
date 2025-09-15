<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('angkatans', function (Blueprint $t) {
            $t->id();
            $t->string('tahun', 9);          // "2023", "2025"
            $t->string('label')->nullable(); // "Angkatan 2025"
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('angkatans'); }
};