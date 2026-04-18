<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 leading-tight">Kegiatan Rutin</h2>
                <p class="text-sm text-gray-500 mt-1">{{ auth()->user()->hasRole('Pengajar') ? 'Pilih kegiatan untuk memperbarui pencapaian siswa.' : 'Kelola data kegiatan rutin untuk sekolah Anda.' }}</p>
            </div>
            
            <div class="flex flex-wrap gap-2">
                @if(!auth()->user()->hasRole('Pengajar'))
                <a href="{{ route((auth()->user()->hasRole('Admin Sekolah') ? 'admin.' : 'pengajar.').'master-kegiatan-rutin.create') }}" class="btn-primary py-2 px-4 rounded-xl font-bold shadow-lg shadow-[#1A6B6B]/20 inline-flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Kegiatan
                </a>
                <a href="{{ route((auth()->user()->hasRole('Admin Sekolah') ? 'admin.' : 'pengajar.').'kegiatan-rutin.index') }}" class="btn-secondary py-2 px-4 rounded-xl font-bold shadow-sm inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7l3 3 3-3" /></svg>
                    Input Rutin Harian
                </a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl flex items-center gap-3 animate-fade-in shadow-sm">
                <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <p class="text-sm font-bold">{{ session('success') }}</p>
            </div>
        @endif

        <div class="card overflow-hidden border-none shadow-sm shadow-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">Nama Kegiatan</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">Aspek</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">Matrikulasi</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">Peserta (Kelas)</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-gray-400 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($masters as $master)
                            <tr class="hover:bg-gray-50/30 transition">
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $master->nama_kegiatan }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $master->aspek }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $master->matrikulasi ? $master->matrikulasi->indicator : '-' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                    @foreach($master->kelas as $k)
                                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">{{ $k->name }}</span>
                                    @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route((auth()->user()->hasRole('Admin Sekolah') ? 'admin.' : 'pengajar.').'master-kegiatan-rutin.show', $master) }}" class="h-8 w-8 rounded-lg bg-gray-50 flex items-center justify-center hover:bg-blue-500 hover:text-white transition group border border-gray-100 shadow-sm text-blue-600" title="Detail Capaian Siswa">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </a>
                                        @if(!auth()->user()->hasRole('Pengajar'))
                                        <a href="{{ route((auth()->user()->hasRole('Admin Sekolah') ? 'admin.' : 'pengajar.').'master-kegiatan-rutin.edit', $master) }}" class="h-8 w-8 rounded-lg bg-gray-50 flex items-center justify-center hover:bg-yellow-500 hover:text-white transition group border border-gray-100 shadow-sm text-yellow-600" title="Edit Kegiatan">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </a>
                                        <form action="{{ route((auth()->user()->hasRole('Admin Sekolah') ? 'admin.' : 'pengajar.').'master-kegiatan-rutin.destroy', $master) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="h-8 w-8 rounded-lg bg-gray-50 flex items-center justify-center hover:bg-red-500 hover:text-white transition group border border-gray-100 shadow-sm text-red-600" title="Hapus Kegiatan">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic text-sm">
                                    Belum ada data master kegiatan rutin.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
