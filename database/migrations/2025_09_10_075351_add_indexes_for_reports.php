<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Cek apakah index dengan nama tertentu sudah ada di table */
    private function indexExists(string $table, string $indexName): bool
    {
        $db = DB::getDatabaseName();
        $rows = DB::select(
            'SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ? LIMIT 1',
            [$db, $table, $indexName]
        );
        return !empty($rows);
    }

    /** Drop index kalau ada (untuk down() yang aman) */
    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if ($this->indexExists($table, $indexName)) {
            // MySQL/MariaDB allow DROP INDEX index_name ON table_name
            DB::statement("DROP INDEX `$indexName` ON `$table`");
        }
    }

    public function up(): void
    {
        // ========== HAFALANS ==========
        Schema::table('hafalans', function (Blueprint $t) {});

        // tanggal_setor
        if (Schema::hasColumn('hafalans', 'tanggal_setor') && !$this->indexExists('hafalans', 'hafalans_tgl_idx')) {
            Schema::table('hafalans', function (Blueprint $t) {
                $t->index('tanggal_setor', 'hafalans_tgl_idx');
            });
        }

        // santri_id + tanggal_setor
        if (
            Schema::hasColumn('hafalans', 'santri_id')
            && Schema::hasColumn('hafalans', 'tanggal_setor')
            && !$this->indexExists('hafalans', 'hafalans_santri_tgl_idx')
        ) {
            Schema::table('hafalans', function (Blueprint $t) {
                $t->index(['santri_id', 'tanggal_setor'], 'hafalans_santri_tgl_idx');
            });
        }

        // index untuk kolom surah (pilih yang ada saja)
        $surahCols = ['surah_nomor', 'surah_id', 'nomor_surah'];
        $surahColToUse = null;
        foreach ($surahCols as $col) {
            if (Schema::hasColumn('hafalans', $col)) {
                $surahColToUse = $col;
                break;
            }
        }
        if ($surahColToUse && !$this->indexExists('hafalans', 'hafalans_surah_idx')) {
            Schema::table('hafalans', function (Blueprint $t) use ($surahColToUse) {
                $t->index($surahColToUse, 'hafalans_surah_idx');
            });
        }

        // ========== SANTRI ==========
        if (Schema::hasTable('santri')) {
            if (Schema::hasColumn('santri', 'kelas_id') && !$this->indexExists('santri', 'santri_kelas_idx')) {
                Schema::table('santri', function (Blueprint $t) {
                    $t->index('kelas_id', 'santri_kelas_idx');
                });
            }
            if (Schema::hasColumn('santri', 'angkatan_id') && !$this->indexExists('santri', 'santri_angkatan_idx')) {
                Schema::table('santri', function (Blueprint $t) {
                    $t->index('angkatan_id', 'santri_angkatan_idx');
                });
            }
            if (Schema::hasColumn('santri', 'strata_id') && !$this->indexExists('santri', 'santri_strata_idx')) {
                Schema::table('santri', function (Blueprint $t) {
                    $t->index('strata_id', 'santri_strata_idx');
                });
            }
        }

        // ========== KELAS ==========
        if (Schema::hasTable('kelas')) {
            if (Schema::hasColumn('kelas', 'nama') && !$this->indexExists('kelas', 'kelas_nama_idx')) {
                Schema::table('kelas', function (Blueprint $t) {
                    $t->index('nama', 'kelas_nama_idx');
                });
            }
            if (Schema::hasColumn('kelas', 'jenis') && !$this->indexExists('kelas', 'kelas_jenis_idx')) {
                Schema::table('kelas', function (Blueprint $t) {
                    $t->index('jenis', 'kelas_jenis_idx');
                });
            }
        }
    }

    public function down(): void
    {
        // HAFALANS
        $this->dropIndexIfExists('hafalans', 'hafalans_tgl_idx');
        $this->dropIndexIfExists('hafalans', 'hafalans_santri_tgl_idx');
        $this->dropIndexIfExists('hafalans', 'hafalans_surah_idx');

        // SANTRI
        $this->dropIndexIfExists('santri', 'santri_kelas_idx');
        $this->dropIndexIfExists('santri', 'santri_angkatan_idx');
        $this->dropIndexIfExists('santri', 'santri_strata_idx');

        // KELAS
        $this->dropIndexIfExists('kelas', 'kelas_nama_idx');
        $this->dropIndexIfExists('kelas', 'kelas_jenis_idx');
    }
};