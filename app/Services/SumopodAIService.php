<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SumopodAIService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl = 'https://ai.sumopod.com/v1';

    public function __construct(string $apiKey, string $model = 'gpt-4o-mini')
    {
        $this->apiKey = $apiKey;
        $this->model  = $model;
    }

    /**
     * Generate 3 feedback suggestions for a student achievement entry.
     *
     * @param  string  $anakName         Student's name
     * @param  string  $kegiatanTitle    Activity title
     * @param  string  $matrikulasiLabel Matriculation indicator label (aspek: indicator)
     * @param  string  $scoreLabel       Human-readable scale label (e.g. "Mulai Berkembang (MB)")
     * @return array<string>             Exactly 3 suggestion strings
     */
    public function generateFeedbackSuggestions(
        string $anakName,
        string $kegiatanTitle,
        string $matrikulasiLabel,
        string $scoreLabel
    ): array {
        $prompt = <<<PROMPT
Kamu adalah guru PAUD / TK yang profesional dan penuh kasih sayang.
Berikan TEPAT 3 saran umpan balik positif dan konstruktif dalam Bahasa Indonesia untuk dicatat dalam laporan perkembangan siswa.

Konteks:
- Nama Siswa : {$anakName}
- Judul Kegiatan : {$kegiatanTitle}
- Aspek / Indikator Matrikulasi : {$matrikulasiLabel}
- Skala Capaian : {$scoreLabel}

Instruksi:
- Setiap saran harus singkat (maks 2 kalimat), positif, spesifik, dan sesuai dengan usia anak PAUD / TK.
- Gunakan bahasa yang hangat dan mendorong.
- Jangan mengulang saran yang sama.
- Jawab HANYA dengan 3 saran, masing-masing diawali dengan nomor (1. 2. 3.) tanpa penjelasan tambahan.

Format jawaban:
1. [Saran pertama]
2. [Saran kedua]
3. [Saran ketiga]
PROMPT;

        $response = Http::withToken($this->apiKey)
            ->timeout(30)
            ->post("{$this->baseUrl}/chat/completions", [
                'model'    => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens'  => 512,
                'temperature' => 0.8,
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException(
                'Sumopod AI API error: ' . $response->status() . ' ' . $response->body()
            );
        }

        $content = $response->json('choices.0.message.content', '');

        return $this->parseSuggestions($content);
    }

    /**
     * Parse the numbered list from the AI response into an array of 3 strings.
     */
    protected function parseSuggestions(string $content): array
    {
        $lines = preg_split('/\r?\n/', trim($content));
        $suggestions = [];

        foreach ($lines as $line) {
            $line = trim($line);
            // Match lines starting with 1. 2. 3. (with possible bold markers)
            if (preg_match('/^\d+\.\s*\*{0,2}(.+)\*{0,2}$/', $line, $matches)) {
                $suggestion = trim($matches[1]);
                if ($suggestion !== '') {
                    $suggestions[] = $suggestion;
                }
            }
        }

        // Fallback: split by numbered pattern if no matches
        if (empty($suggestions)) {
            preg_match_all('/\d+\.\s*(.+?)(?=\d+\.|$)/s', $content, $matches);
            $suggestions = array_map('trim', $matches[1] ?? []);
        }

        // Ensure we always return exactly 3
        $suggestions = array_values(array_filter($suggestions));
        while (count($suggestions) < 3) {
            $suggestions[] = 'Terus semangat dan pertahankan perkembangan yang baik ini!';
        }

        return array_slice($suggestions, 0, 3);
    }

    /**
     * Generate a monthly monitoring summary for a student's matriculation achievements.
     *
     * @param  array<string, mixed>  $stats  Aggregated data from MonevDataAggregator
     */
    public function generateMonevSummary(
        string $anakName,
        string $kelasName,
        string $periodeLabel,
        array $stats
    ): string {
        $statsJson = json_encode($stats, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $aspekNames = implode(', ', array_keys($stats['per_aspek'] ?? []));
        $aspekInstruction = $aspekNames !== ''
            ? <<<ASPEK

Setelah section REKOMENDASI, tambahkan ringkasan naratif per aspek capaian dengan format persis:
[ASPEK:{nama_aspek}]
- (poin ringkasan 1)
- (poin ringkasan 2)

WAJIB buat section [ASPEK:...] untuk setiap aspek berikut (nama harus sama persis): {$aspekNames}
Setiap aspek 2-4 poin bullet, bahasa hangat seperti guru PAUD, jelaskan makna perkembangan (bukan hanya angka).
ASPEK
            : '';

        $prompt = <<<PROMPT
Kamu adalah guru PAUD / TK yang profesional. Buat ringkasan monitoring & evaluasi (monev) perkembangan siswa berdasarkan data pencapaian matrikulasi selama satu bulan.

Konteks Siswa:
- Nama: {$anakName}
- Kelas: {$kelasName}
- Periode: {$periodeLabel}

Data Pencapaian Matrikulasi (agregat):
{$statsJson}

Instruksi:
- Tulis dalam Bahasa Indonesia, profesional namun hangat.
- WAJIB gunakan format section marker persis seperti ini (4 section):
[GAMBARAN_UMUM]
- (poin pertama gambaran umum)
- (poin kedua)
- (poin ketiga)

[KEKUATAN]
- (poin kekuatan 1)
- (poin kekuatan 2)

[PERHATIAN]
- (poin area perhatian 1)
- (poin area perhatian 2)

[REKOMENDASI]
- (poin rekomendasi 1)
- (poin rekomendasi 2)

- Setiap section WAJIB berupa daftar poin (bullet), bukan paragraf. Tiap baris diawali "- ".
- Setiap section 3-5 poin singkat (1-2 kalimat per poin).
- Gunakan data statistik sebagai dasar, jangan hanya menyebut angka mentah.
- Jika total entri 0, jelaskan bahwa belum ada data dan berikan 2-3 saran pemantauan umum dalam bentuk poin.
{$aspekInstruction}
PROMPT;

        $response = Http::withToken($this->apiKey)
            ->timeout(60)
            ->post("{$this->baseUrl}/chat/completions", [
                'model'    => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens'  => 2048,
                'temperature' => 0.7,
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException(
                'Sumopod AI API error: HTTP ' . $response->status()
            );
        }

        $content = trim((string) $response->json('choices.0.message.content', ''));

        if ($content === '') {
            throw new \RuntimeException('Sumopod AI mengembalikan respons kosong.');
        }

        return $content;
    }
}
