<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('parent_santri', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');  // users.id (orangtua)
            $table->string('nis');                  // santri.nis
            $table->timestamps();

            $table->unique(['user_id','nis']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            // Jika tabel santri pakai PK 'nis' tanpa FK, biarkan tanpa FK constraint:
            // $table->foreign('nis')->references('nis')->on('santri')->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('parent_santri');
    }
};