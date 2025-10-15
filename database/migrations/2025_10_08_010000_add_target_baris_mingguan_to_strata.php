<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan nama tabel sesuai: 'strata'
        Schema::table('strata', function (Blueprint $table) {
            if (!Schema::hasColumn('strata', 'target_baris_mingguan')) {
                $table->unsignedInteger('target_baris_mingguan')->default(100)->after('nama');
            }
        });
    }

    public function down(): void
    {
        Schema::table('strata', function (Blueprint $table) {
            if (Schema::hasColumn('strata', 'target_baris_mingguan')) {
                $table->dropColumn('target_baris_mingguan');
            }
        });
    }
};