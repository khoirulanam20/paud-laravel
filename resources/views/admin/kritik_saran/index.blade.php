<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
            </div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Kritik &amp; Saran</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title">Masukan orang tua &amp; wali</h3>
                <p class="section-subtitle">Hanya masukan yang ditujukan ke sekolah Anda.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pengirim</th>
                            <th>Sekolah</th>
                            <th>Kelas (anak)</th>
                            <th>Ringkasan</th>
                            <th class="text-right w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feedbacks as $fb)
                            @php
                                $kelasUnik = $fb->user?->anaks
                                    ? $fb->user->anaks->pluck('kelas.name')->filter()->unique()->values()
                                    : collect();
                                $kelasTeks = $kelasUnik->isNotEmpty()
                                    ? $kelasUnik->implode(', ')
                                    : ($fb->user?->anaks?->isNotEmpty() ? 'Anak belum masuk kelas' : '—');
                            @endphp
                            <tr>
                                <td class="whitespace-nowrap text-sm" style="color:#6B6560;">{{ $fb->created_at->format('d M Y, H:i') }}</td>
                                <td class="text-sm">
                                    <span class="font-semibold" style="color:#2C2C2C;">{{ $fb->user?->name ?? ($fb->nama_bapak ?: '—') }}</span>
                                    @if($fb->user?->email)
                                        <span class="block text-xs" style="color:#9E9790;">{{ $fb->user->email }}</span>
                                    @endif
                                </td>
                                <td class="text-sm max-w-[10rem]" style="color:#2C2C2C;">{{ $fb->sekolah?->name ?? '—' }}</td>
                                <td class="text-sm max-w-[12rem]" style="color:#6B6560;">{{ $kelasTeks }}</td>
                                <td class="max-w-md">
                                    <p class="text-sm line-clamp-2" style="color:#6B6560;">{{ \Illuminate\Support\Str::limit($fb->message, 120) }}</p>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.kritik-saran.show', $fb) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg inline-block" style="color:#1A6B6B;background:#D0E8E8;">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-14 text-center text-sm" style="color:#9E9790;">Belum ada kritik atau saran masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($feedbacks->hasPages())
                <div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $feedbacks->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
