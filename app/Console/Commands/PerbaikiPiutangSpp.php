<?php

namespace App\Console\Commands;

use App\Models\AkuntansiSetting;
use App\Models\PembayaranBulanan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PerbaikiPiutangSpp extends Command
{
    protected $signature = 'akuntansi:perbaiki-piutang-spp {--apply : Simpan perubahan (default: dry-run)}';

    protected $description = 'Pindahkan kredit jurnal pelunasan SPP dari pendapatan ke piutang bila tagihannya sudah diakui sebagai piutang';

    public function handle(): int
    {
        $apply = $this->option('apply');
        $jumlah = 0;

        $pelunasan = DB::table('jurnals')
            ->where('sourceable_type', PembayaranBulanan::class)
            ->where('deskripsi', 'like', 'Auto: Pembayaran%')
            ->whereExists(fn ($q) => $q->selectRaw(1)->from('jurnals as t')
                ->whereColumn('t.sourceable_id', 'jurnals.sourceable_id')
                ->where('t.sourceable_type', PembayaranBulanan::class)
                ->where('t.deskripsi', 'like', 'Auto: Tagihan%'))
            ->get();

        foreach ($pelunasan as $j) {
            $setting = AkuntansiSetting::forSekolah($j->sekolah_id);
            if (! $setting->akun_piutang_id || ! $setting->akun_pendapatan_id) {
                continue;
            }

            $lines = DB::table('jurnal_lines')
                ->where('jurnal_id', $j->id)
                ->where('akun_id', $setting->akun_pendapatan_id)
                ->where('kredit', '>', 0);

            if ($lines->count() === 0) {
                continue;
            }

            $jumlah++;
            $this->line("Jurnal #{$j->id} ({$j->deskripsi}): kredit pendapatan → piutang");
            if ($apply) {
                $lines->update(['akun_id' => $setting->akun_piutang_id]);
            }
        }

        $this->info($apply ? "{$jumlah} jurnal diperbaiki." : "{$jumlah} jurnal akan diperbaiki. Jalankan dengan --apply.");

        return self::SUCCESS;
    }
}
