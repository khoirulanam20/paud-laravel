<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->unsignedTinyInteger('tingkat')->nullable()->after('description');
            $table->unsignedSmallInteger('usia_min_bulan')->nullable()->after('tingkat');
        });

        DB::statement("ALTER TABLE `anaks` MODIFY `status` ENUM('pending','approved','rejected','lulus') NOT NULL DEFAULT 'pending'");

        Schema::create('kenaikan_kelas_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->string('mode', 10); // semester|usia
            $table->date('tanggal_acuan');
            $table->date('tanggal_efektif');
            $table->string('tahun_ajaran_asal', 9);
            $table->string('tahun_ajaran_tujuan', 9);
            $table->unsignedInteger('jumlah_naik')->default(0);
            $table->unsignedInteger('jumlah_tinggal')->default(0);
            $table->unsignedInteger('jumlah_lulus')->default(0);
            $table->string('status', 10)->default('diterapkan'); // diterapkan|dibatalkan
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('dibatalkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dibatalkan_at')->nullable();
            $table->timestamps();
        });

        Schema::create('kenaikan_kelas_batch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('kenaikan_kelas_batches')->cascadeOnDelete();
            $table->foreignId('anak_id')->constrained('anaks')->cascadeOnDelete();
            $table->foreignId('kelas_asal_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->foreignId('kelas_tujuan_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->string('aksi', 10); // naik|tinggal|lulus|pindah
            $table->unsignedSmallInteger('usia_bulan')->nullable();
            $table->string('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('anak_kelas_riwayats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('anak_id')->constrained('anaks')->cascadeOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->string('kelas_nama');
            $table->string('tahun_ajaran', 9);
            $table->unsignedTinyInteger('semester');
            $table->date('mulai');
            $table->date('selesai')->nullable();
            $table->string('alasan', 10); // masuk|naik|pindah|koreksi
            $table->string('alasan_selesai', 10)->nullable(); // naik|pindah|lulus|keluar|koreksi
            $table->foreignId('batch_id')->nullable()->constrained('kenaikan_kelas_batches')->nullOnDelete();
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->string('catatan')->nullable();
            $table->timestamps();
            $table->index(['anak_id', 'mulai']);
            $table->index(['kelas_id', 'mulai', 'selesai']);
        });

        $this->backfill();
    }

    public function down(): void
    {
        Schema::dropIfExists('anak_kelas_riwayats');
        Schema::dropIfExists('kenaikan_kelas_batch_items');
        Schema::dropIfExists('kenaikan_kelas_batches');
        DB::statement("ALTER TABLE `anaks` MODIFY `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn(['tingkat', 'usia_min_bulan']);
        });
    }

    private function backfill(): void
    {
        $anaks = DB::table('anaks as a')
            ->leftJoin('kelas as k', 'k.id', '=', 'a.kelas_id')
            ->whereNotNull('a.kelas_id')
            ->select('a.id as anak_id', 'a.sekolah_id', 'a.kelas_id', 'a.created_at', 'k.name as kelas_nama')
            ->get();

        foreach ($anaks as $row) {
            $date = \Carbon\Carbon::parse($row->created_at)->startOfDay();
            $ta = \App\Support\TahunAjaran::fromDate($date);

            DB::table('anak_kelas_riwayats')->insert([
                'sekolah_id' => $row->sekolah_id,
                'anak_id' => $row->anak_id,
                'kelas_id' => $row->kelas_id,
                'kelas_nama' => $row->kelas_nama ?? '-',
                'tahun_ajaran' => $ta['tahun_ajaran'],
                'semester' => $ta['semester'],
                'mulai' => $date->toDateString(),
                'selesai' => null,
                'alasan' => 'masuk',
                'alasan_selesai' => null,
                'batch_id' => null,
                'dibuat_oleh' => null,
                'catatan' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
