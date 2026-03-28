@props([
    'events' => [],
    'year' => null,
    'month' => null,
])

@php
    $year = $year ?? (int) now()->year;
    $month = $month ?? (int) now()->month;
@endphp

@vite(['resources/js/kegiatan-calendar.js'])

@php
    // JSON_HEX_* + JSON_HEX_TAG: aman di dalam <script>, hindari pemutus </script>
    $flags = JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
    $eventsJson = json_encode($events ?? [], $flags);
    if ($eventsJson === false) {
        $eventsJson = '[]';
    }
@endphp
<div
    id="kegiatan-calendar-mount"
    class="jurnal-fc rounded-xl border overflow-hidden bg-white"
    style="border-color:rgba(0,0,0,0.08);"
    data-year="{{ $year }}"
    data-month="{{ $month }}"
>
    <script type="application/json" id="kegiatan-calendar-json">{!! $eventsJson !!}</script>
</div>
