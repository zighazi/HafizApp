<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kelas', function (Blueprint $t) {
            $t->id();
            $t->enum('grade', ['X','XI','XII']);
            $t->string('kode');             // "X.E1", "XI.F3", ...
            $t->string('nama_kelas');       // label tampilan (boleh sama dgn kode)
            $t->enum('stream', ['TAHFIZH','REGULER']);
            $t->boolean('is_special')->default(false); // penanda kelas khusus (E1, F1)
            $t->foreignId('next_kelas_id')->nullable()->constrained('kelas')->nullOnDelete(); // target promosi
            $t->timestamps();
            $t->unique(['grade','kode']);
        });
    }
    public function down(): void { Schema::dropIfExists('kelas'); }
};