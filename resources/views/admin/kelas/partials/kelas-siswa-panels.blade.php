<div class="card overflow-hidden">
    <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
        <h3 class="section-title">Siswa di kelas</h3>
        <p class="section-subtitle">Nama diurutkan A–Z</p>
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama Siswa</th>
                    <th>NIK</th>
                    <th>Jenis Kelamin</th>
                    <th>Tgl. Lahir</th>
                    <th>Nama Orang Tua</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelas->anaks as $anak)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <x-foto-profil :path="$anak->photo" :name="$anak->name" size="sm" />
                                <span class="font-semibold" style="color:#2C2C2C;">{{ $anak->name }}</span>
                            </div>
                        </td>
                        <td><span class="text-sm">{{ $anak->nik ?? '—' }}</span></td>
                        <td><span class="text-sm">{{ $anak->jenis_kelamin ?? '—' }}</span></td>
                        <td>
                            @if($anak->dob)
                                <span class="text-sm">{{ $anak->dob->format('d M Y') }}</span>
                                <span class="text-[10px] block font-bold text-[#1A6B6B]">{{ $anak->age }}</span>
                            @else
                                <span class="text-sm">—</span>
                            @endif
                        </td>
                        <td><span class="text-sm">{{ $anak->parent_name ?? '—' }}</span></td>
                        <td class="text-right">
                            <a href="{{ route('admin.anak.show', $anak) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg transition inline-block" style="color:#1A6B6B;background:#E8F5F5;">Profil siswa</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-10 md:py-14 text-center" style="color:#9E9790;">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="h-10 w-10 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                Belum ada siswa yang ditugaskan ke kelas ini. Atur kelas siswa di menu <span class="font-semibold" style="color:#5A5A5A;">Kelola Data Siswa</span>.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
