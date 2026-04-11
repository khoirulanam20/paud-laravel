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
     * @param  string  $score            Achievement scale (BB / MB / BSH / BSB)
     * @return array<string>             Exactly 3 suggestion strings
     */
    public function generateFeedbackSuggestions(
        string $anakName,
        string $kegiatanTitle,
        string $matrikulasiLabel,
        string $score
    ): array {
        $scoreLabel = match ($score) {
            'BB'  => 'Belum Berkembang (BB)',
            'MB'  => 'Mulai Berkembang (MB)',
            'BSH' => 'Berkembang Sesuai Harapan (BSH)',
            'BSB' => 'Berkembang Sangat Baik (BSB)',
            default => $score,
        };

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
}
