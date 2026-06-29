<?php

namespace App\Services;

use App\Models\SekolahAiPersona;
use App\Support\AiPersonaScope;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SumopodAIService
{
    protected string $apiKey;

    protected string $model;

    protected string $baseUrl;

    public function __construct(string $apiKey, string $model = 'gpt-4o-mini', ?string $baseUrl = null)
    {
        $this->apiKey = $apiKey;
        $this->model = $model;
        $this->baseUrl = rtrim($baseUrl ?? 'https://ai.sumopod.com/v1', '/');
    }

    /**
     * Generate 3 feedback suggestions for a student achievement entry.
     *
     * @param  string  $anakName  Student's name
     * @param  string  $kegiatanTitle  Activity title
     * @param  string  $matrikulasiLabel  Matriculation indicator label (aspek: indicator)
     * @param  string  $scoreLabel  Human-readable scale label (e.g. "Mulai Berkembang (MB)")
     * @return array<string> Exactly 3 suggestion strings
     */
    public function generateFeedbackSuggestions(
        string $anakName,
        string $kegiatanTitle,
        string $matrikulasiLabel,
        string $scoreLabel,
        ?string $personaPrefix = null
    ): array {
        $identity = $personaPrefix !== null && trim($personaPrefix) !== ''
            ? trim($personaPrefix)
            : 'Kamu adalah guru PAUD / TK yang profesional dan penuh kasih sayang.';

        $prompt = <<<PROMPT
{$identity}
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
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 512,
                'temperature' => 0.8,
            ]);

        if (! $response->successful()) {
            $this->throwApiError($response);
        }

        $content = $response->json('choices.0.message.content', '');

        return $this->parseSuggestions($content);
    }

    protected function throwApiError(Response $response): void
    {
        Log::warning('AI API request failed', [
            'status' => $response->status(),
            'body' => mb_substr($response->body(), 0, 2000),
            'base_url' => $this->baseUrl,
        ]);

        throw new \RuntimeException('AI API error: HTTP '.$response->status());
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
        array $stats,
        ?string $personaPrefix = null
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

        $identity = $personaPrefix !== null && trim($personaPrefix) !== ''
            ? trim($personaPrefix)
            : 'Kamu adalah guru PAUD / TK yang profesional.';

        $prompt = <<<PROMPT
{$identity} Buat ringkasan monitoring & evaluasi (monev) perkembangan siswa berdasarkan data pencapaian matrikulasi selama satu bulan.

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
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 2048,
                'temperature' => 0.7,
            ]);

        if (! $response->successful()) {
            $this->throwApiError($response);
        }

        $content = trim((string) $response->json('choices.0.message.content', ''));

        if ($content === '') {
            throw new \RuntimeException('AI mengembalikan respons kosong.');
        }

        return $content;
    }

    /**
     * Multi-turn chat completion.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    public function chatCompletion(array $messages, int $maxTokens = 1024): string
    {
        $response = Http::withToken($this->apiKey)
            ->timeout(60)
            ->post("{$this->baseUrl}/chat/completions", [
                'model' => $this->model,
                'messages' => $messages,
                'max_tokens' => $maxTokens,
                'temperature' => 0.7,
            ]);

        if (! $response->successful()) {
            $this->throwApiError($response);
        }

        $content = trim((string) $response->json('choices.0.message.content', ''));

        if ($content === '') {
            throw new \RuntimeException('AI mengembalikan respons kosong.');
        }

        return $content;
    }

    /**
     * Generate structured persona fields for a PAUD school AI function.
     *
     * @return array<string, mixed>
     */
    public function generatePersonaFields(string $sekolahName, string $scope, ?string $brief = null): array
    {
        $briefBlock = filled($brief)
            ? "\nDeskripsi sekolah dari admin:\n{$brief}\n"
            : '';

        $scopeContext = AiPersonaScope::generateContext($scope);
        $defaultRole = AiPersonaScope::defaultRoleTitle($scope);

        $prompt = <<<PROMPT
Buat persona AI untuk PAUD / daycare.

Nama sekolah: {$sekolahName}
Fungsi AI: {$scopeContext}
Judul peran default (referensi): {$defaultRole}
{$briefBlock}
Jawab HANYA dengan JSON valid (tanpa markdown, tanpa penjelasan) dengan key persis:
{
  "name": "nama persona",
  "role_title": "judul peran",
  "description": "deskripsi singkat persona",
  "gender": "perempuan atau laki_laki atau netral",
  "age": 28,
  "dialog_language": "Bahasa Indonesia",
  "personality_traits": "sifat kepribadian",
  "communication_style": "gaya komunikasi",
  "behavior_guidelines": "panduan perilaku AI",
  "background": "latar belakang persona"
}

Gunakan Bahasa Indonesia untuk teks. Field age harus angka 18-80.
Hindari sapaan kaku berulang seperti Bu/Ibu/Bapak/Ibu.
PROMPT;

        $content = $this->chatCompletion([
            ['role' => 'user', 'content' => $prompt],
        ], 1536);

        try {
            return $this->parsePersonaFields($content, $scope);
        } catch (\RuntimeException) {
            $retryPrompt = $prompt."\n\nPENTING: Balas HANYA JSON valid tanpa teks lain.";
            $retryContent = $this->chatCompletion([
                ['role' => 'user', 'content' => $retryPrompt],
            ], 1536);

            return $this->parsePersonaFields($retryContent, $scope);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function parsePersonaFields(string $content, string $scope): array
    {
        $defaults = [
            'name' => AiPersonaScope::defaultName($scope),
            'role_title' => AiPersonaScope::defaultRoleTitle($scope),
            'description' => '',
            'gender' => null,
            'age' => null,
            'dialog_language' => 'Bahasa Indonesia',
            'personality_traits' => '',
            'communication_style' => '',
            'behavior_guidelines' => '',
            'background' => '',
        ];

        $json = trim($content);
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/i', $json, $matches)) {
            $json = trim($matches[1]);
        }

        $decoded = json_decode($json, true);
        if (! is_array($decoded)) {
            if (preg_match('/\{[\s\S]*\}/', $content, $objectMatch)) {
                $decoded = json_decode($objectMatch[0], true);
            }
        }

        if (! is_array($decoded)) {
            throw new \RuntimeException('AI tidak mengembalikan JSON persona yang valid.');
        }

        $limits = [
            'name' => 120,
            'role_title' => 120,
            'description' => 2000,
            'dialog_language' => 60,
            'personality_traits' => 2000,
            'communication_style' => 2000,
            'behavior_guidelines' => 2000,
            'background' => 2000,
        ];

        $result = [];
        foreach ($defaults as $key => $default) {
            if ($key === 'gender') {
                $gender = strtolower(trim((string) ($decoded['gender'] ?? '')));
                $result['gender'] = in_array($gender, [
                    SekolahAiPersona::GENDER_PEREMPUAN,
                    SekolahAiPersona::GENDER_LAKI_LAKI,
                    SekolahAiPersona::GENDER_NETRAL,
                ], true) ? $gender : null;

                continue;
            }

            if ($key === 'age') {
                $age = $decoded['age'] ?? null;
                $age = is_numeric($age) ? (int) $age : null;
                $result['age'] = ($age !== null && $age >= 18 && $age <= 80) ? $age : null;

                continue;
            }

            $value = trim((string) ($decoded[$key] ?? $default));
            $result[$key] = mb_substr($value, 0, $limits[$key] ?? 2000);
        }

        if ($result['name'] === '') {
            $result['name'] = $defaults['name'];
        }

        return $result;
    }
}
