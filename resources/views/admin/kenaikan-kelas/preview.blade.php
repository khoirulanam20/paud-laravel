<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Pratinjau Kenaikan ({{ ucfirst($mode) }})</h2>
        </div>
    </x-slot>
    <div class="app-page-shell">
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>@endif
        @if(count($warnings))<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($warnings as $w)<li>{{ $w }}</li>@endforeach</ul></div>@endif

        <form method="POST" action="{{ route('admin.kenaikan-kelas.store') }}">
            @csrf
            <input type="hidden" name="mode" value="{{ $mode }}">
            <input type="hidden" name="tanggal_acuan" value="{{ $tanggalAcuan->format('Y-m-d') }}">
            <input type="hidden" name="tanggal_efektif" value="{{ $tanggalEfektif->format('Y-m-d') }}">

            <div class="card overflow-hidden mb-6">
                <div class="card-pad">
                    <p class="section-subtitle">Tanggal acuan: <b>{{ $tanggalAcuan->format('d M Y') }}</b> · Tanggal efektif: <b>{{ $tanggalEfektif->format('d M Y') }}</b> · {{ $items->count() }} siswa diusulkan</p>
                    <div class="toolbar-actions mt-4">
                        <a href="{{ route('admin.kenaikan-kelas.index') }}" class="btn-secondary">Kembali</a>
                        <button type="submit" class="btn-primary" @if($items->isEmpty()) disabled @endif>Konfirmasi &amp; Terapkan</button>
                    </div>
                </div>
            </div>

            @php $grouped = $items->groupBy(fn ($i) => $i['kelas_asal']->name); $n = 0; @endphp
            @foreach($grouped as $namaAsal => $rows)
            <div class="card overflow-hidden mb-6">
                <div class="card-pad page-toolbar border-b" style="border-color:rgba(0,0,0,0.06);">
                    <div><h3 class="section-title">{{ $namaAsal }}</h3><p class="section-subtitle">{{ $rows->count() }} siswa</p></div>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr><th class="w-10"><input type="checkbox" title="Pilih semua" @change="$el.closest('table').querySelectorAll('[data-proses]').forEach(c => c.checked = $el.checked)" x-data></th><th>Siswa</th><th>Usia (bln)</th><th>Aksi</th><th>Kelas Tujuan</th><th>Catatan</th></tr></thead>
                        <tbody>
                            @foreach($rows as $row)
                            @php $idx = $n++; @endphp
                            <tr>
                                <td><input type="hidden" name="items[{{ $idx }}][proses]" value="0"><input type="checkbox" data-proses name="items[{{ $idx }}][proses]" value="1"></td>
                                <td class="text-sm font-semibold">{{ $row['anak']->name }}</td>
                                <td class="text-sm">{{ $row['usia_bulan'] ?? '-' }}</td>
                                <td>
                                    <input type="hidden" name="items[{{ $idx }}][anak_id]" value="{{ $row['anak']->id }}">
                                    <select name="items[{{ $idx }}][aksi]" class="input-field" x-data @change="$el.closest('tr').querySelector('[data-tujuan]').toggleAttribute('disabled', !['naik','pindah'].includes($el.value))">
                                        @foreach(['naik' => 'Naik', 'tinggal' => 'Tinggal', 'lulus' => 'Lulus', 'pindah' => 'Pindah'] as $v => $l)
                                        <option value="{{ $v }}" @selected($row['aksi'] === $v)>{{ $l }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="items[{{ $idx }}][kelas_tujuan_id]" data-tujuan class="input-field" @disabled(!in_array($row['aksi'], ['naik', 'pindah'], true))>
                                        <option value="">—</option>
                                        @foreach($kelasTujuan as $k)<option value="{{ $k->id }}" @selected($row['kelas_tujuan']?->id === $k->id)>{{ $k->name }} (T{{ $k->tingkat }})</option>@endforeach
                                    </select>
                                </td>
                                <td><input type="text" name="items[{{ $idx }}][catatan]" value="{{ $row['catatan'] }}" class="input-field" placeholder="Opsional"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </form>
    </div>
</x-app-layout>
