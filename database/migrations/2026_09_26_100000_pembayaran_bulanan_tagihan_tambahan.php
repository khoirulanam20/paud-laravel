<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran_bulanans', function (Blueprint $table) {
            $table->dropUnique('pembayaran_bulanan_unique');
            $table->boolean('is_tagihan_tambahan')->default(false)->after('periode_tahun');
            $table->string('diskon_keterangan')->nullable()->after('nilai_diskon');
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_bulanans', function (Blueprint $table) {
            $table->dropColumn(['is_tagihan_tambahan', 'diskon_keterangan']);
            $table->unique(
                ['anak_id', 'biaya_bulanan_sekolah_id', 'periode_bulan', 'periode_tahun'],
                'pembayaran_bulanan_unique'
            );
        });
    }
};
