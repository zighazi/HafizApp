<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('santris', function (Blueprint $t) {
            if (!Schema::hasColumn('santris','angkatan_id')) $t->foreignId('angkatan_id')->nullable()->constrained('angkatans')->nullOnDelete();
            if (!Schema::hasColumn('santris','kelas_id'))    $t->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            if (!Schema::hasColumn('santris','strata_id'))   $t->foreignId('strata_id')->nullable()->constrained('strata')->nullOnDelete();
            if (!Schema::hasColumn('santris','graduated_at'))$t->timestamp('graduated_at')->nullable();
        });
    }
    public function down(): void {
        Schema::table('santris', function (Blueprint $t) {
            $t->dropConstrainedForeignId('angkatan_id');
            $t->dropConstrainedForeignId('kelas_id');
            $t->dropConstrainedForeignId('strata_id');
            $t->dropColumn('graduated_at');
        });
    }
};