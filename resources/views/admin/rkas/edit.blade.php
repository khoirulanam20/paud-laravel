<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.rkas.index', ['tahun_ajaran' => $rka->tahun_ajaran]) }}" class="text-sm" style="color:#1A6B6B;">&larr; Kembali</a>
                <h2 class="font-bold text-xl" style="color:#2C2C2C;">{{ $rka->label }}</h2>
                <span class="badge {{ $rka->isFinal() ? 'badge-green' : 'badge-blue' }}">{{ ucfirst($rka->status) }}</span>
            </div>
            <div class="flex gap-2 flex-wrap">
                @if(!$rka->isFinal())
                    <button type="button" @click="showFinalizeModal = true" class="btn-secondary text-sm">Finalkan</button>
                @else
                    <form action="{{ route('admin.rkas.reopen', $rka) }}" method="POST">@csrf<button class="btn-secondary text-sm">Buka Kembali</button></form>
                @endif
                <form action="{{ route('admin.rkas.sync', $rka) }}" method="POST">@csrf<button class="btn-secondary text-sm">Sync Realisasi</button></form>
            </div>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-full mx-auto" x-data="{ showFinalizeModal: false }">
        @if(session('success'))<div class="alert-success mb-5">{{ session('success') }}</div>@endif
        <div class="card p-4 mb-5 text-sm" style="color:#9E9790;">Periode: {{ $rka->tanggal_mulai->format('d M Y') }} – {{ $rka->tanggal_akhir->format('d M Y') }}</div>

        <form action="{{ route('admin.rkas.update', $rka) }}" method="POST">
            @csrf @method('PUT')
            @foreach(['belanja' => 'Belanja', 'pendapatan' => 'Pendapatan'] as $jenis => $label)
                @php $items = $jenis === 'belanja' ? $akunBelanja : $akunPendapatan; @endphp
                @if($items->count() === 0) @continue @endif
                <div class="card overflow-hidden mb-6">
                    <div class="px-6 py-3 border-b font-bold" style="border-color:rgba(0,0,0,0.06);background:#FAF6F0;">{{ $label }}</div>
                    <div class="overflow-x-auto">
                        <table class="data-table text-sm">
                            <thead>
                                <tr>
                                    <th class="w-8"></th>
                                    <th>Kode</th>
                                    <th>Uraian</th>
                                    @foreach($sumberDanas as $sd)<th class="text-right whitespace-nowrap">{{ $sd->kode }}</th>@endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $akun)
                                    @php $line = $selected->get($akun->id); $anggarans = $line?->anggarans->keyBy('sumber_dana_id') ?? collect(); @endphp
                                    <tr>
                                        <td><input type="checkbox" name="lines[{{ $akun->id }}][enabled]" value="1" @checked($line !== null) {{ $rka->isFinal() ? 'disabled' : '' }}></td>
                                        <td class="font-mono whitespace-nowrap">{{ $akun->kode }}</td>
                                        <td class="max-w-xs truncate" title="{{ $akun->uraian ?? $akun->nama }}">{{ Str::limit($akun->uraian ?? $akun->nama, 50) }}</td>
                                        @foreach($sumberDanas as $sd)
                                            <td class="text-right">
                                                <input type="number" name="lines[{{ $akun->id }}][anggaran][{{ $sd->id }}]" value="{{ $anggarans[$sd->id]->nominal ?? 0 }}" min="0" step="1000" class="input-field text-xs w-28 text-right" {{ $rka->isFinal() ? 'disabled' : '' }}>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(method_exists($items, 'links'))
                        <div class="px-6 py-3 border-t" style="border-color:rgba(0,0,0,0.06);">
                            <x-per-page-selector :paginator="$items" param="{{ $jenis }}_per_page" />
                            {{ $items->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            @endforeach
            @if(!$rka->isFinal())<div class="flex justify-end"><button type="submit" class="btn-primary">Simpan Anggaran</button></div>@endif
        </form>

        <x-confirm-modal
            show="showFinalizeModal"
            :action="route('admin.rkas.finalize', $rka)"
            title="Finalkan RKAS?"
            message="Anggaran akan dikunci dan tidak bisa diubah lagi. Realisasi tetap bisa disync."
            submit="Ya, Finalkan"
            submit-class="btn-primary"
            icon="warning"
        />
    </div>
</x-app-layout>
