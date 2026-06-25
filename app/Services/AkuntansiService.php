<?php

namespace App\Services;

use App\Models\Akun;
use App\Models\AkuntansiSetting;
use App\Models\Cashflow;
use App\Models\Jurnal;
use App\Models\JurnalLine;
use App\Models\PembayaranBulanan;
use Illuminate\Support\Facades\DB;

class AkuntansiService
{
    /**
     * Jurnal untuk cashflow manual.
     * Pemasukan: Debit Kas / Kredit akun_counter (dari setting)
     * Pengeluaran: Debit akun_counter / Kredit Kas
     */
    public function buatJurnalDariCashflow(Cashflow $cashflow): Jurnal
    {
        $setting = AkuntansiSetting::forSekolah($cashflow->sekolah_id);
        $kas = $cashflow->akun_id
            ? Akun::find($cashflow->akun_id)
            : $setting->akunKas;
        $counter = $cashflow->akun_lawan_id
            ? Akun::find($cashflow->akun_lawan_id)
            : ($cashflow->type === 'in' ? $setting->akunUntukIn : $setting->akunUntukOut);

        $jurnal = DB::transaction(function () use ($cashflow, $kas, $counter) {
            $jurnal = Jurnal::create([
                'sekolah_id' => $cashflow->sekolah_id,
                'no_jurnal' => $this->generateNoJurnal($cashflow->sekolah_id),
                'tanggal' => $cashflow->date,
                'deskripsi' => 'Auto: Cashflow '.$cashflow->type.' - '.($cashflow->description),
                'created_by' => auth()->id(),
                'source' => 'auto-cashflow',
            ]);

            $this->createLines($jurnal, [
                [$kas->id, $cashflow->type === 'in' ? $cashflow->amount : 0, $cashflow->type === 'in' ? 0 : $cashflow->amount],
                [$counter->id, $cashflow->type === 'in' ? 0 : $cashflow->amount, $cashflow->type === 'in' ? $cashflow->amount : 0],
            ]);

            return $jurnal;
        });

        $cashflow->update(['jurnal_id' => $jurnal->id]);

        return $jurnal;
    }

    /**
     * Accrual: jurnal saat generate tagihan.
     * Debit Piutang SPP / Kredit Pendapatan SPP
     */
    public function buatJurnalSaatGenerate(PembayaranBulanan $pembayaran, int $userId): Jurnal
    {
        $setting = AkuntansiSetting::forSekolah($pembayaran->sekolah_id);

        $jurnal = DB::transaction(function () use ($pembayaran, $setting, $userId) {
            $jurnal = Jurnal::create([
                'sekolah_id' => $pembayaran->sekolah_id,
                'no_jurnal' => $this->generateNoJurnal($pembayaran->sekolah_id),
                'tanggal' => now(),
                'deskripsi' => 'Auto: Tagihan '.$pembayaran->getPeriodeLabel().' - '.($pembayaran->anak->name ?? 'Siswa'),
                'created_by' => $userId,
                'source' => 'auto-pembayaran',
                'sourceable_type' => PembayaranBulanan::class,
                'sourceable_id' => $pembayaran->id,
            ]);

            $this->createLines($jurnal, [
                [$setting->akun_piutang_id, $pembayaran->total_bayar, 0],
                [$setting->akun_pendapatan_id, 0, $pembayaran->total_bayar],
            ]);

            return $jurnal;
        });

        $pembayaran->update(['jurnal_id' => $jurnal->id]);

        return $jurnal;
    }

    /**
     * Jurnal saat approve.
     * Cash:  Debit Kas / Kredit Pendapatan SPP
     * Accrual: Debit Kas / Kredit Piutang SPP
     */
    public function buatJurnalSaatApprove(PembayaranBulanan $pembayaran, int $userId): Jurnal
    {
        $setting = AkuntansiSetting::forSekolah($pembayaran->sekolah_id);

        $jurnal = DB::transaction(function () use ($pembayaran, $setting, $userId) {
            $deskripsi = 'Auto: Pembayaran '.$pembayaran->getPeriodeLabel().' - '.($pembayaran->anak->name ?? 'Siswa');

            if ($setting->isAccrual()) {
                // Debit Kas / Kredit Piutang SPP
                $lines = [
                    [$setting->akun_kas_id, $pembayaran->total_bayar, 0],
                    [$setting->akun_piutang_id, 0, $pembayaran->total_bayar],
                ];
            } else {
                // Cash: Debit Kas / Kredit Pendapatan SPP
                $lines = [
                    [$setting->akun_kas_id, $pembayaran->total_bayar, 0],
                    [$setting->akun_pendapatan_id, 0, $pembayaran->total_bayar],
                ];
            }

            $jurnal = Jurnal::create([
                'sekolah_id' => $pembayaran->sekolah_id,
                'no_jurnal' => $this->generateNoJurnal($pembayaran->sekolah_id),
                'tanggal' => now(),
                'deskripsi' => $deskripsi,
                'created_by' => $userId,
                'source' => 'auto-pembayaran',
                'sourceable_type' => PembayaranBulanan::class,
                'sourceable_id' => $pembayaran->id,
            ]);

            $this->createLines($jurnal, $lines);

            return $jurnal;
        });

        $pembayaran->update(['jurnal_id' => $jurnal->id]);

        // Sync ke cashflow
        Cashflow::create([
            'sekolah_id' => $pembayaran->sekolah_id,
            'akun_id' => $setting->akun_kas_id,
            'jurnal_id' => $jurnal->id,
            'type' => 'in',
            'amount' => $pembayaran->total_bayar,
            'description' => 'Pembayaran '.$pembayaran->getPeriodeLabel().' - '.($pembayaran->anak->name ?? 'Siswa'),
            'date' => now(),
        ]);

        return $jurnal;
    }

    public function hapusJurnal(Jurnal $jurnal): void
    {
        DB::transaction(function () use ($jurnal) {
            Cashflow::where('jurnal_id', $jurnal->id)->delete();
            PembayaranBulanan::where('jurnal_id', $jurnal->id)->update(['jurnal_id' => null]);
            $jurnal->lines()->delete();
            $jurnal->delete();
        });
    }

    public function validasiSaldo(array $lines): bool
    {
        $totalDebit = array_sum(array_column($lines, 'debit'));
        $totalKredit = array_sum(array_column($lines, 'kredit'));

        return bccomp((string) $totalDebit, (string) $totalKredit, 2) === 0
            && $totalDebit > 0;
    }

    public function generateNoJurnal(int $sekolahId): string
    {
        $prefix = 'JNL-'.now()->format('Ym').'-';
        $last = Jurnal::where('sekolah_id', $sekolahId)
            ->where('no_jurnal', 'like', $prefix.'%')
            ->orderBy('no_jurnal', 'desc')
            ->first();

        if ($last) {
            $num = (int) substr($last->no_jurnal, -4) + 1;
        } else {
            $num = 1;
        }

        return $prefix.str_pad((string) $num, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Saldo akun dari jurnal_lines.
     * Untuk saldo_normal=debit: SUM(debit) - SUM(kredit)
     * Untuk saldo_normal=kredit: SUM(kredit) - SUM(debit)
     */
    public function saldoAkun(int $akunId, ?string $sampaiTanggal = null): float
    {
        $akun = Akun::findOrFail($akunId);

        $query = JurnalLine::where('akun_id', $akunId)
            ->whereHas('jurnal', function ($q) use ($sampaiTanggal) {
                if ($sampaiTanggal) {
                    $q->where('tanggal', '<=', $sampaiTanggal);
                }
            });

        $totalDebit = (float) (clone $query)->sum('debit');
        $totalKredit = (float) (clone $query)->sum('kredit');

        if ($akun->saldo_normal === 'debit') {
            return $totalDebit - $totalKredit;
        }

        return $totalKredit - $totalDebit;
    }

    /**
     * Total saldo per jenis akun (aset/liabilitas/ekuitas/pendapatan/beban)
     */
    public function saldoPerJenis(string $jenis, int $sekolahId, ?string $sampaiTanggal = null): float
    {
        $total = 0;
        $akuns = Akun::where('sekolah_id', $sekolahId)
            ->where('jenis', $jenis)
            ->where('is_aktif', true)
            ->get();

        foreach ($akuns as $akun) {
            $total += $this->saldoAkun($akun->id, $sampaiTanggal);
        }

        return $total;
    }

    public function getSetting(int $sekolahId): AkuntansiSetting
    {
        return AkuntansiSetting::forSekolah($sekolahId);
    }

    /** @param array<array{int, float, float}> $lines [akun_id, debit, kredit] */
    private function createLines(Jurnal $jurnal, array $lines): void
    {
        foreach ($lines as [$akunId, $debit, $kredit]) {
            $jurnal->lines()->create([
                'akun_id' => $akunId,
                'debit' => $debit,
                'kredit' => $kredit,
            ]);
        }
    }
}
