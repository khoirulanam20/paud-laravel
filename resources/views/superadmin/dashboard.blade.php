<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3" data-tour="page-header">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background:#1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </div>
            <h2 class="font-bold text-xl" style="color:#2C2C2C;">Dashboard Superadmin</h2>
        </div>
    </x-slot>

    <div class="pt-6 pb-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5" data-tour="superadmin-dashboard-stats">
            <div class="stat-card">
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:#9E9790;">Total Lembaga</p>
                <p class="text-3xl font-bold" style="color:#2C2C2C;">{{ $totalLembaga }}</p>
                <a href="{{ route('superadmin.lembaga.index') }}" class="text-xs font-medium mt-1 inline-block" style="color:#1A6B6B;">Kelola &rarr;</a>
            </div>
            <div class="stat-card">
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:#9E9790;">Total Sekolah</p>
                <p class="text-3xl font-bold" style="color:#2C2C2C;">{{ $totalSekolah }}</p>
                <a href="{{ route('superadmin.sekolah.index') }}" class="text-xs font-medium mt-1 inline-block" style="color:#1A6B6B;">Lihat &rarr;</a>
            </div>
            <div class="stat-card">
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:#9E9790;">Total Pengguna</p>
                <p class="text-3xl font-bold" style="color:#2C2C2C;">{{ $totalUsers }}</p>
            </div>
            <div class="stat-card">
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:#9E9790;">AI Terkonfigurasi</p>
                <p class="text-3xl font-bold" style="color:#2C2C2C;">{{ $totalAiConfigured }}</p>
                <a href="{{ route('superadmin.ai-setting.index') }}" class="text-xs font-medium mt-1 inline-block" style="color:#1A6B6B;">Pengaturan &rarr;</a>
            </div>
        </div>

        <div class="card">
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title">Lembaga Terbaru</h3>
            </div>
            <div class="divide-y" style="divide-color:rgba(0,0,0,0.05);">
                @forelse($recentLembagas as $l)
                    <div class="px-6 py-4 flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-sm" style="color:#2C2C2C;">{{ $l->name }}</p>
                            <p class="text-xs" style="color:#9E9790;">{{ $l->address ?? '-' }}</p>
                        </div>
                        <span class="text-xs" style="color:#9E9790;">{{ $l->created_at?->format('d M Y') }}</span>
                    </div>
                @empty
                    <p class="px-6 py-8 text-center text-sm" style="color:#9E9790;">Belum ada lembaga.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
