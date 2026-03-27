<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Dashboard Admin Sekolah') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Summary Cards -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-slate-200">
                    <div class="p-6 text-slate-900">
                        <h3 class="text-sm font-medium text-slate-500 uppercase tracking-wider">Total Siswa</h3>
                        <p class="mt-2 text-3xl font-semibold">120</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-slate-200">
                    <div class="p-6 text-slate-900">
                        <h3 class="text-sm font-medium text-slate-500 uppercase tracking-wider">Total Pengajar</h3>
                        <p class="mt-2 text-3xl font-semibold">15</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-slate-200">
                    <div class="p-6 text-slate-900">
                        <h3 class="text-sm font-medium text-slate-500 uppercase tracking-wider">Sarana</h3>
                        <p class="mt-2 text-3xl font-semibold">45</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-slate-200">
                    <div class="p-6 text-slate-900">
                        <h3 class="text-sm font-medium text-slate-500 uppercase tracking-wider">Saldo Cashflow</h3>
                        <p class="mt-2 text-xl font-semibold">Rp 15.000.000</p>
                    </div>
                </div>
            </div>

            <!-- Actions Panel -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-slate-200">
                <div class="p-6 text-slate-900">
                    <h3 class="text-lg font-medium mb-4">Manajemen Sekolah</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('admin.anak.index') }}" class="p-4 border border-slate-200 rounded-lg hover:border-slate-900 transition flex flex-col items-center justify-center text-center">
                            <span class="font-medium">Data Anak & Ortu</span>
                        </a>
                        <a href="{{ route('admin.pengajar.index') }}" class="p-4 border border-slate-200 rounded-lg hover:border-slate-900 transition flex flex-col items-center justify-center text-center">
                            <span class="font-medium">Data Pengajar</span>
                        </a>
                        <a href="{{ route('admin.sarana.index') }}" class="p-4 border border-slate-200 rounded-lg hover:border-slate-900 transition flex flex-col items-center justify-center text-center">
                            <span class="font-medium">Sarana Prasarana</span>
                        </a>
                        <a href="#" class="p-4 border border-slate-200 rounded-lg hover:border-slate-900 transition flex flex-col items-center justify-center text-center">
                            <span class="font-medium">Menu Makanan</span>
                        </a>
                        <a href="{{ route('admin.kegiatan.index') }}" class="p-4 border border-slate-200 rounded-lg hover:border-slate-900 transition flex flex-col items-center justify-center text-center">
                            <span class="font-medium">Jadwal Kegiatan</span>
                        </a>
                        <a href="{{ route('admin.cashflow.index') }}" class="p-4 border border-slate-200 rounded-lg hover:border-slate-900 transition flex flex-col items-center justify-center text-center">
                            <span class="font-medium">Modul Keuangan</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
