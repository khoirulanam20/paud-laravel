<?php

namespace App\Imports;

use App\Models\Matrikulasi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MatrikulasiImport implements ToCollection, WithHeadingRow
{
    public int $successCount = 0;

    /** @var array<int, string> */
    public array $failedRows = [];

    /** @var array<int, string> */
    public array $validRows = [];

    public function __construct(
        protected int $sekolahId,
        protected bool $dryRun = false,
    ) {}

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            if ($this->isEmptyRow($row)) {
                continue;
            }

            $data = $this->normalizeRow($row);

            $validator = Validator::make($data, [
                'aspek' => ['nullable', 'string', 'max:255'],
                'indikator' => ['required', 'string', 'max:255'],
                'deskripsi' => ['required', 'string'],
                'tujuan' => ['nullable', 'string'],
                'strategi' => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                $this->failedRows[$rowNumber] = implode(' ', $validator->errors()->all());

                continue;
            }

            $validated = $validator->validated();

            try {
                if ($this->dryRun) {
                    $this->validRows[$rowNumber] = $this->previewRow($validated);
                    $this->successCount++;

                    continue;
                }

                Matrikulasi::create([
                    'sekolah_id' => $this->sekolahId,
                    'aspek' => $validated['aspek'],
                    'indicator' => $validated['indikator'],
                    'description' => $validated['deskripsi'],
                    'tujuan' => $validated['tujuan'],
                    'strategi' => $validated['strategi'],
                ]);

                $this->successCount++;
            } catch (\Throwable $e) {
                $this->failedRows[$rowNumber] = $e->getMessage();
            }
        }
    }

    protected function isEmptyRow(Collection $row): bool
    {
        $indikator = trim((string) ($row['indikator'] ?? ''));
        $deskripsi = trim((string) ($row['deskripsi'] ?? ''));

        return $indikator === '' && $deskripsi === '';
    }

    protected function normalizeRow(Collection $row): array
    {
        $data = [];

        foreach (['aspek', 'indikator', 'deskripsi', 'tujuan', 'strategi'] as $key) {
            $value = $row[$key] ?? null;
            if ($value === null || $value === '') {
                $data[$key] = null;

                continue;
            }

            $data[$key] = is_string($value) ? trim($value) : $value;
        }

        return $data;
    }

    protected function previewRow(array $validated): string
    {
        $parts = [];

        if ($validated['aspek'] ?? null) {
            $parts[] = $validated['aspek'];
        }

        $parts[] = $validated['indikator'];

        return implode(' · ', $parts);
    }
}
