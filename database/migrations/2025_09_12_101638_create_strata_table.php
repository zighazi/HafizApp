<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('strata', function (Blueprint $t) {
            $t->id();
            $t->enum('stream', ['TAHFIZH','REGULER']);
            $t->string('nama_strata');  // Tahsin/Reguler/Akselerasi/Tamhidi/Takmili
            $t->timestamps();
            $t->unique(['stream','nama_strata']);
        });
    }
    public function down(): void { Schema::dropIfExists('strata'); }
};