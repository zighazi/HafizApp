<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('hafalans', function (Blueprint $table) {
            // cegah duplikat persis
            $table->unique(
                ['santri_id','surah_id','tanggal_setor','ayat_mulai','ayat_selesai'],
                'hafalans_unique_exact'
            );
        });
    }
    public function down(): void
    {
        Schema::table('hafalans', function (Blueprint $table) {
            $table->dropUnique('hafalans_unique_exact');
        });
    }
};