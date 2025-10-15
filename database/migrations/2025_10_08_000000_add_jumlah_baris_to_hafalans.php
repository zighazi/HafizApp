<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hafalans', function (Blueprint $table) {
            if (!Schema::hasColumn('hafalans', 'jumlah_baris')) {
                $table->unsignedInteger('jumlah_baris')->nullable()->after('ayat_selesai');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hafalans', function (Blueprint $table) {
            if (Schema::hasColumn('hafalans', 'jumlah_baris')) {
                $table->dropColumn('jumlah_baris');
            }
        });
    }
};
