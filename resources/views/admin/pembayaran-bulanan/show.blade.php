<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3" data-tour="page-header">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-xl" style="color: #2C2C2C;">Detail Pembayaran</h2>
                <p class="text-sm" style="color:#9E9790;">{{ $pembayaran->anak->name }} - {{ $pembayaran->getPeriodeLabel() }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto"
         x-data="{ showHariHadirModal: false, showDiskonModal: false, showApproveModal: false, showRejectModal: false }" @tour-close-modals.window="showHariHadirModal=false; showDiskonModal=false; showApproveModal=false; showRejectModal=false">

        @if(session('success'))<div class="alert-success mb-5">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert-danger mb-5"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>@endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="section-title">Status</h3>
                        <span class="badge badge-{{ $pembayaran->status_badge }}">{{ $pembayaran->status_label }}</span>
                    </div>
                    @if($pembayaran->catatan_admin)
                        <div class="p-3 rounded-lg" style="background:#F9FAFB;">
                            <p class="text-xs font-semibold mb-1" style="color:#9E9790;">Catatan:</p>
                            <p class="text-sm">{{ $pembayaran->catatan_admin }}</p>
                        </div>
                    @endif
                </div>

                <div class="card p-6" data-tour="pembayaran-rincian">
                    <h3 class="section-title mb-4">Rincian Perhitungan</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between py-2 border-b" style="border-color:rgba(0,0,0,0.06);">
                            <span class="text-sm" style="color:#9E9790;">Biaya Harian</span>
                            <span class="font-semibold">{{ $pembayaran->getBiayaPerHariFormatted() }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b" style="border-color:rgba(0,0,0,0.06);">
                            <div class="flex items-center gap-2">
                                <span class="text-sm" style="color:#9E9790;">Hari Hadir</span>
                                <button @click="showHariHadirModal=true" class="text-xs px-2 py-0.5 rounded" style="color:#1A6B6B;background:#D0E8E8;">Edit</button>
                            </div>
                            <span class="font-semibold" style="color:#1A6B6B;">{{ $pembayaran->hari_hadir }} hari</span>
                        </div>
                        <div class="flex justify-between py-2 border-b" style="border-color:rgba(0,0,0,0.06);">
                            <span class="text-sm" style="color:#9E9790;">Subtotal (biaya/hari x hadir)</span>
                            <span class="font-semibold">{{ $pembayaran->getSubtotalFormatted() }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b" style="border-color:rgba(0,0,0,0.06);">
                            <div class="flex items-center gap-2">
                                <span class="text-sm" style="color:#9E9790;">Diskon</span>
                                <button @click="showDiskonModal=true" class="text-xs px-2 py-0.5 rounded" style="color:#1A6B6B;background:#D0E8E8;">Edit</button>
                            </div>
                            <span class="font-semibold" style="color:#C0392B;">
                                {{ $pembayaran->diskon ? $pembayaran->diskon->nama_diskon.' (-'.$pembayaran->getNilaiDiskonFormatted().')' : '-' }}
                            </span>
                        </div>
                        <div class="flex justify-between py-3 px-4 rounded-lg" style="background:#D0E8E8;">
                            <span class="font-bold text-lg" style="color:#1A6B6B;">Total Bayar</span>
                            <span class="font-bold text-2xl" style="color:#1A6B6B;">{{ $pembayaran->getTotalFormatted() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="card p-6">
                    <h3 class="section-title mb-4">Info Siswa</h3>
                    <div class="space-y-3 text-sm">
                        <div><p class="text-xs" style="color:#9E9790;">Nama</p><p class="font-semibold">{{ $pembayaran->anak->name }}</p></div>
                        <div><p class="text-xs" style="color:#9E9790;">Kelas</p><p class="font-semibold">{{ $pembayaran->anak->kelas->name ?? '-' }}</p></div>
                        <div><p class="text-xs" style="color:#9E9790;">Periode</p><p class="font-semibold">{{ $pembayaran->getPeriodeLabel() }}</p></div>
                        <div><p class="text-xs" style="color:#9E9790;">Jenis Biaya</p><p class="font-semibold">{{ $pembayaran->biayaBulananSekolah->nama_biaya ?? '-' }}</p></div>
                    </div>
                </div>

                @if($pembayaran->bukti_transfer)
                    <div class="card p-6" data-tour="pembayaran-bukti">
                        <h3 class="section-title mb-4">Bukti Transfer</h3>
                        <a href="{{ Storage::url($pembayaran->bukti_transfer) }}" target="_blank">
                            <img src="{{ Storage::url($pembayaran->bukti_transfer) }}" alt="Bukti" class="w-full rounded-lg border">
                        </a>
                    </div>
                @endif

                @if($pembayaran->isPending())
                    <div class="card p-6 space-y-3">
                        <button data-tour="pembayaran-approve-btn" data-tour-open-modal="approve" @click="showApproveModal=true" class="btn-primary w-full justify-center">Setujui</button>
                        <button data-tour="pembayaran-reject-btn" data-tour-open-modal="reject" @click="showRejectModal=true" class="btn-danger w-full justify-center">Tolak</button>
                    </div>
                @endif

                <a href="{{ route('admin.pembayaran-bulanan.index', ['bulan' => $pembayaran->periode_bulan, 'tahun' => $pembayaran->periode_tahun]) }}" class="btn-secondary w-full justify-center">Kembali</a>
            </div>
        </div>

        <!-- HARI HADIR -->
        <div x-show="showHariHadirModal" class="modal-overlay" style="display:none;">
            <div x-show="showHariHadirModal" x-transition class="modal-box max-w-sm" @click.away="showHariHadirModal=false">
                <form action="{{ route('admin.pembayaran-bulanan.update-hari-hadir', $pembayaran) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-header"><h3 class="section-title">Edit Hari Hadir</h3></div>
                    <div class="modal-body">
                        <label class="input-label">Jumlah Hari Hadir</label>
                        <input type="number" name="hari_hadir" value="{{ $pembayaran->hari_hadir }}" min="0" required class="input-field">
                    </div>
                    <div class="modal-footer"><button type="button" @click="showHariHadirModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>

        <!-- DISKON -->
        <div x-show="showDiskonModal" class="modal-overlay" style="display:none;">
            <div x-show="showDiskonModal" x-transition class="modal-box max-w-sm" @click.away="showDiskonModal=false">
                <form action="{{ route('admin.pembayaran-bulanan.update-diskon', $pembayaran) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-header"><h3 class="section-title">Edit Diskon</h3></div>
                    <div class="modal-body">
                        <select name="diskon_id" class="input-field">
                            <option value="">Tanpa Diskon</option>
                            @foreach(\App\Models\Diskon::where('sekolah_id', auth()->user()->sekolah_id)->where('is_aktif', true)->get() as $diskon)
                                <option value="{{ $diskon->id }}" {{ $pembayaran->diskon_id == $diskon->id ? 'selected' : '' }}>{{ $diskon->nama_diskon }} ({{ $diskon->getNilaiFormatted() }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showDiskonModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>

        <!-- APPROVE -->
        <div x-show="showApproveModal" data-tour="modal-approve" class="modal-overlay" style="display:none;">
            <div x-show="showApproveModal" x-transition class="modal-box max-w-sm" @click.away="showApproveModal=false">
                <form action="{{ route('admin.pembayaran-bulanan.approve', $pembayaran) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-body text-center py-6">
                        <h3 class="section-title">Setujui Pembayaran?</h3>
                        <p class="section-subtitle mt-1">Total: {{ $pembayaran->getTotalFormatted() }}</p>
                        <textarea name="catatan_admin" rows="2" placeholder="Catatan (opsional)" class="input-field mt-4"></textarea>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showApproveModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-primary">Ya, Setujui</button></div>
                </form>
            </div>
        </div>

        <!-- REJECT -->
        <div x-show="showRejectModal" data-tour="modal-reject" class="modal-overlay" style="display:none;">
            <div x-show="showRejectModal" x-transition class="modal-box max-w-sm" @click.away="showRejectModal=false">
                <form action="{{ route('admin.pembayaran-bulanan.reject', $pembayaran) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-body text-center py-6">
                        <h3 class="section-title">Tolak Pembayaran?</h3>
                        <textarea name="catatan_admin" rows="2" placeholder="Alasan penolakan" required class="input-field mt-4"></textarea>
                    </div>
                    <div class="modal-footer"><button type="button" @click="showRejectModal=false" class="btn-secondary">Batal</button><button type="submit" class="btn-danger">Ya, Tolak</button></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
