<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7l3 3 3-3" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Pendaftaran Siswa Baru</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto"
         x-data="{ showRejectModal: false, rejectRoute: '', rejectData: {}, openReject(r, d){ this.rejectRoute=r; this.rejectData=d; this.showRejectModal=true; } }">

        @if(session('success'))<div class="alert-success mb-5"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>@endif

        {{-- PENDING --}}
        <div class="card overflow-hidden mb-6">
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06); background: linear-gradient(to right, #FFF9C4, #FFFBF0);">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">⏳</span>
                    <div>
                        <h3 class="section-title">Menunggu Persetujuan</h3>
                        <p class="section-subtitle">{{ $pending->count() }} pendaftar baru membutuhkan tindakan</p>
                    </div>
                    @if($pending->count() > 0)
                    <span class="ml-auto inline-flex items-center justify-center h-7 px-3 rounded-full text-xs font-bold text-white" style="background:#FF8C42;">{{ $pending->count() }}</span>
                    @endif
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Nama Anak</th><th>Orang Tua</th><th>Email</th><th>Tgl. Lahir</th><th>Catatan Ortu</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($pending as $a)
                        <tr>
                            <td><span class="font-semibold" style="color:#2C2C2C;">{{ $a->name }}</span></td>
                            <td>{{ $a->parent_name ?? $a->user->name }}</td>
                            <td class="text-sm" style="color:#9E9790;">{{ $a->user->email }}</td>
                            <td>{{ $a->dob ? \Carbon\Carbon::parse($a->dob)->format('d M Y') : '-' }}</td>
                            <td class="max-w-xs text-sm italic" style="color:#9E9790;">{{ $a->catatan_ortu ?? '-' }}</td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('admin.pendaftaran.approve', $a) }}">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg text-white" style="background:#1A6B6B;">✅ Setujui</button>
                                    </form>
                                    <button @click="openReject('{{ route('admin.pendaftaran.reject', $a) }}', {{ json_encode(['name' => $a->name]) }})" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="color:#C0392B;background:#FAD7D2;">❌ Tolak</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="py-10 text-center" style="color:#9E9790;">🎉 Tidak ada pendaftaran yang menunggu persetujuan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- APPROVED --}}
        <div class="card overflow-hidden mb-6">
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title">✅ Telah Disetujui</h3>
                <p class="section-subtitle">Siswa aktif di sekolah ini</p>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Nama Anak</th><th>Orang Tua</th><th>Email</th><th>Tgl. Lahir</th><th>Disetujui Pada</th></tr></thead>
                    <tbody>
                        @forelse($approved as $a)
                        <tr>
                            <td><span class="font-semibold" style="color:#2C2C2C;">{{ $a->name }}</span></td>
                            <td>{{ $a->parent_name ?? $a->user->name }}</td>
                            <td class="text-sm" style="color:#9E9790;">{{ $a->user->email }}</td>
                            <td>{{ $a->dob ? \Carbon\Carbon::parse($a->dob)->format('d M Y') : '-' }}</td>
                            <td class="text-sm" style="color:#9E9790;">{{ $a->updated_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="py-4 md:py-8 text-center" style="color:#9E9790;">Belum ada yang disetujui.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($approved->hasPages())<div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $approved->links() }}</div>@endif
        </div>

        {{-- REJECTED --}}
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title">❌ Ditolak</h3>
                <p class="section-subtitle">Pendaftaran yang tidak disetujui</p>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Nama Anak</th><th>Orang Tua</th><th>Alasan Penolakan</th></tr></thead>
                    <tbody>
                        @forelse($rejected as $a)
                        <tr>
                            <td><span class="font-semibold" style="color:#2C2C2C;">{{ $a->name }}</span></td>
                            <td>{{ $a->parent_name ?? $a->user->name }}</td>
                            <td class="text-sm italic" style="color:#9E9790;">{{ $a->catatan_admin ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="py-4 md:py-8 text-center" style="color:#9E9790;">Tidak ada penolakan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($rejected->hasPages())<div class="px-6 py-4 border-t" style="border-color:rgba(0,0,0,0.06);">{{ $rejected->links() }}</div>@endif
        </div>

        {{-- REJECT MODAL --}}
        <div x-show="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showRejectModal" x-transition class="modal-box max-w-md" @click.away="showRejectModal=false">
                <form :action="rejectRoute" method="POST">
                    @csrf
                    <div class="modal-header"><h3 class="section-title">Tolak Pendaftaran</h3><p class="section-subtitle mt-1">Penolakan untuk: <strong x-text="rejectData.name"></strong></p></div>
                    <div class="modal-body space-y-4">
                        <div><label class="input-label">Alasan Penolakan (opsional, akan dilihat admin)</label><textarea name="catatan_admin" rows="3" class="input-field" placeholder="Contoh: Kuota kelas sudah penuh..."></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showRejectModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-danger">Tolak Pendaftaran</button></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
