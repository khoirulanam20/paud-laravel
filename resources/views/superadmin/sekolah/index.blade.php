<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3" data-tour="page-header">
            <h2 class="font-bold text-xl" style="color:#2C2C2C;">Monitoring Sekolah</h2>
        </div>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <form method="GET" class="mb-6 flex flex-wrap gap-3 items-end">
            <div>
                <label class="input-label">Filter Lembaga</label>
                <select name="lembaga_id" class="input-field" onchange="this.form.submit()">
                    <option value="">Semua lembaga</option>
                    @foreach($lembagas as $l)
                        <option value="{{ $l->id }}" @selected(request('lembaga_id') == $l->id)>{{ $l->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
        <div class="card overflow-hidden">
            <table class="data-table">
                <thead><tr><th>Sekolah</th><th>Lembaga</th><th>Alamat</th><th>Telepon</th></tr></thead>
                <tbody>
                    @forelse($sekolahs as $s)
                    <tr>
                        <td class="font-semibold">{{ $s->name }}</td>
                        <td>{{ $s->lembaga?->name ?? '-' }}</td>
                        <td>{{ $s->address ?? '-' }}</td>
                        <td>{{ $s->phone ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-8 text-center" style="color:#9E9790;">Tidak ada data sekolah.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 border-t">
                <x-per-page-selector :paginator="$sekolahs" />
                {{ $sekolahs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
