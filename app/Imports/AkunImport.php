<?php

namespace App\Imports;

use App\Models\Akun;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AkunImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;

    public int $ignored = 0;

    /** @var list<array{row: int, status: string, label: string, message: string}> */
    public array $rows = [];

    public function __construct(
        protected int $sekolahId,
        protected bool $dryRun = true,
        protected bool $ignoreDuplicates = false,
    ) {}

    public function collection(Collection $rows): void
    {
        $seen = [];
        $existing = [];
        foreach (Akun::where('sekolah_id', $this->sekolahId)->get(['kode', 'snp', 'komponen']) as $akun) {
            $existing[$this->key($akun->kode, $akun->snp, $akun->komponen)] = true;
        }

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $data = $this->normalize($row);

            if ($data['kode'] === '' && $data['nama'] === '') {
                continue;
            }

            $error = $this->validate($data);
            $key = $this->key($data['kode'], $data['snp'], $data['komponen']);
            $label = trim($data['kode'].' — '.$data['nama'], ' —');

            if ($error) {
                $this->rows[] = ['row' => $rowNumber, 'status' => 'invalid', 'label' => $label, 'message' => $error];

                continue;
            }

            if (isset($seen[$key])) {
                $this->rows[] = [
                    'row' => $rowNumber,
                    'status' => 'duplicate',
                    'label' => $label,
                    'message' => 'Duplikat di file (baris '.$seen[$key].').',
                ];

                continue;
            }

            $seen[$key] = $rowNumber;

            if (isset($existing[$key])) {
                $this->rows[] = [
                    'row' => $rowNumber,
                    'status' => 'duplicate',
                    'label' => $label,
                    'message' => 'Sudah ada di database (kode + kelompok + subkelompok).',
                ];

                continue;
            }

            $this->rows[] = ['row' => $rowNumber, 'status' => 'ok', 'label' => $label, 'message' => 'Siap diimport.'];

            if ($this->dryRun) {
                continue;
            }

            Akun::create([
                'sekolah_id' => $this->sekolahId,
                'tipe' => 'rkas',
                'kode' => $data['kode'],
                'nama' => $data['nama'],
                'jenis' => $data['jenis'],
                'snp' => $data['snp'],
                'komponen' => $data['komponen'],
                'uraian' => $data['uraian'],
                'saldo_normal' => $data['saldo_normal'],
                'kategori_arus_kas' => $data['kategori_arus_kas'],
                'is_aktif' => true,
            ]);
            $existing[$key] = true;
            $this->imported++;
        }

        if (! $this->dryRun && $this->ignoreDuplicates) {
            $this->ignored = count(array_filter($this->rows, fn ($r) => $r['status'] === 'duplicate'));
        }
    }

    public function validCount(): int
    {
        return count(array_filter($this->rows, fn ($r) => $r['status'] === 'ok'));
    }

    public function duplicateCount(): int
    {
        return count(array_filter($this->rows, fn ($r) => $r['status'] === 'duplicate'));
    }

    public function invalidCount(): int
    {
        return count(array_filter($this->rows, fn ($r) => $r['status'] === 'invalid'));
    }

    /** @return array{kode: string, nama: string, jenis: string, snp: ?string, komponen: ?string, uraian: ?string, saldo_normal: string, kategori_arus_kas: string} */
    private function normalize(Collection $row): array
    {
        $jenis = strtolower($this->cell($row, ['jenis']));
        $saldo = strtolower($this->cell($row, ['saldo_normal', 'saldo']));
        $snp = $this->cell($row, ['kelompok', 'snp']);
        $komponen = $this->cell($row, ['subkelompok', 'komponen']);

        return [
            'kode' => $this->cell($row, ['kode_akun', 'kode']),
            'nama' => $this->cell($row, ['nama_akun', 'nama']),
            'jenis' => $jenis,
            'snp' => $snp !== '' ? $snp : null,
            'komponen' => $komponen !== '' ? $komponen : null,
            'uraian' => ($u = $this->cell($row, ['uraian'])) !== '' ? $u : null,
            'saldo_normal' => in_array($saldo, ['debit', 'kredit'], true) ? $saldo : $this->defaultSaldo($jenis),
            'kategori_arus_kas' => $this->defaultArus($jenis),
        ];
    }

    /** @param array<string, mixed> $data */
    private function validate(array $data): ?string
    {
        if ($data['kode'] === '' || $data['nama'] === '') {
            return 'Kode dan nama wajib diisi.';
        }
        if (mb_strlen($data['kode']) > 20) {
            return 'Kode maksimal 20 karakter.';
        }
        if (mb_strlen($data['nama']) > 200) {
            return 'Nama maksimal 200 karakter.';
        }
        if (! in_array($data['jenis'], ['aset', 'liabilitas', 'ekuitas', 'pendapatan', 'beban'], true)) {
            return 'Jenis harus aset, liabilitas, ekuitas, pendapatan, atau beban.';
        }

        return null;
    }

    private function cell(Collection $row, array $keys): string
    {
        foreach ($keys as $key) {
            $value = $row->get($key);
            if ($value !== null && trim((string) $value) !== '') {
                return trim((string) $value);
            }
        }

        return '';
    }

    private function key(string $kode, ?string $snp, ?string $komponen): string
    {
        return mb_strtolower(trim($kode)).'|'.mb_strtolower(trim((string) $snp)).'|'.mb_strtolower(trim((string) $komponen));
    }

    private function defaultSaldo(string $jenis): string
    {
        return in_array($jenis, ['pendapatan', 'liabilitas', 'ekuitas'], true) ? 'kredit' : 'debit';
    }

    private function defaultArus(string $jenis): string
    {
        return $jenis === 'ekuitas' ? 'pendanaan' : 'operasi';
    }
}
