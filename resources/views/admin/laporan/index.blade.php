<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Laporan Keuangan</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
        <p class="section-subtitle mb-6">Laporan berbasis jurnal umum (PSAK). Pilih jenis laporan dan filter periode bulanan atau tahunan kalender.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.laporan-keuangan.neraca') }}" class="card p-6 hover:shadow-md transition block">
                <h3 class="section-title text-base mb-2">Neraca</h3>
                <p class="text-sm" style="color:#9E9790;">Posisi keuangan: aset, liabilitas, dan ekuitas per tanggal.</p>
            </a>
            <a href="{{ route('admin.laporan-keuangan.laba-rugi') }}" class="card p-6 hover:shadow-md transition block">
                <h3 class="section-title text-base mb-2">Laba Rugi</h3>
                <p class="text-sm" style="color:#9E9790;">Pendapatan dan beban untuk periode terpilih.</p>
            </a>
            <a href="{{ route('admin.laporan-keuangan.buku-besar') }}" class="card p-6 hover:shadow-md transition block">
                <h3 class="section-title text-base mb-2">Buku Besar</h3>
                <p class="text-sm" style="color:#9E9790;">General ledger per akun dengan saldo berjalan.</p>
            </a>
        </div>
    </div>
</x-app-layout>
