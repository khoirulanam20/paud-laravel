<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background:#1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div>
            <h2 class="font-bold text-xl" style="color:#2C2C2C;">Siswa Kelasku</h2>
        </div>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title">Daftar Siswa</h3>
                <p class="section-subtitle">Siswa yang terdaftar di kelas Anda</p>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>NIK</th>
                            <th>Jenis Kelamin</th>
                            <th>Tanggal Lahir</th>
                            <th>Nama Orang Tua</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($anaks as $anak)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-xl flex items-center justify-center font-bold text-sm text-white shrink-0" style="background:#1A6B6B;">{{ substr($anak->name, 0, 1) }}</div>
                                    <span class="font-semibold" style="color:#2C2C2C;">{{ $anak->name }}</span>
                                </div>
                            </td>
                            <td>{{ $anak->nik ?? '-' }}</td>
                            <td>{{ $anak->jenis_kelamin ?? '-' }}</td>
                            <td>{{ $anak->dob ? \Carbon\Carbon::parse($anak->dob)->format('d M Y') : '-' }}</td>
                            <td>{{ $anak->parent_name ?? '-' }}</td>
                            <td class="text-right">
                                <a href="{{ route('adminkelas.anak.show', $anak) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg transition" style="color: #1A6B6B; background: #E8F5F5;">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center" style="color:#9E9790;">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    Belum ada siswa di kelas ini.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($anaks->hasPages())<div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $anaks->links() }}</div>@endif
        </div>
    </div>
</x-app-layout>
