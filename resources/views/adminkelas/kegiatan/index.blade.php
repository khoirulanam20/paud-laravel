<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Jurnal Kegiatan Kelas</h2>
        </div>
    </x-slot>
    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto"
         x-data="{ showDetailModal:false, detailData:{},
            onCalClick(detail){
                const p = detail.extendedProps || {};
                if (p.mode !== 'readonly' || !p.detail) return;
                this.detailData = p.detail;
                this.showDetailModal = true;
            }
         }"
         @kegiatan-cal-click.window="onCalClick($event.detail)">

        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title">Kalender (siswa kelas Anda)</h3>
                <p class="section-subtitle">Menampilkan kegiatan yang memiliki pencapaian untuk siswa di kelas ini. Filter opsional per pengajar.</p>
            </div>
            <form method="get" class="px-6 py-4 flex flex-wrap items-end gap-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                <input type="hidden" name="year" value="{{ $year }}">
                <input type="hidden" name="month" value="{{ $month }}">
                <div class="min-w-[200px]">
                    <label class="input-label">Filter pengajar</label>
                    <select name="pengajar_id" class="input-field" onchange="this.form.submit()">
                        <option value="">Semua pengajar</option>
                        @foreach($pengajars as $pg)
                            <option value="{{ $pg->id }}" @selected(request('pengajar_id') == $pg->id)>{{ $pg->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
            <div class="p-4 md:p-6">
                <x-jurnal-kalender :events="$calendarEvents" :year="$year" :month="$month" />
            </div>
        </div>

        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showDetailModal" x-transition class="modal-box max-w-lg" @click.away="showDetailModal=false">
                <div class="modal-header"><h3 class="section-title" x-text="detailData.title"></h3></div>
                <div class="modal-body space-y-4 max-h-[70vh] overflow-y-auto">
                    <p class="text-sm" style="color:#5A5A5A;"><strong>Tanggal:</strong> <span x-text="detailData.date ? new Date(detailData.date + 'T12:00:00').toLocaleDateString('id-ID') : '-'"></span></p>
                    <p class="text-sm" style="color:#5A5A5A;"><strong>Pengajar:</strong> <span x-text="detailData.pengajar_name || '-'"></span></p>
                    <div x-show="detailData.photo_url" class="rounded-xl overflow-hidden border" style="border-color:rgba(0,0,0,0.08);">
                        <img :src="detailData.photo_url" alt="" class="w-full max-h-64 object-cover">
                    </div>
                    <p class="text-sm leading-relaxed" style="color:#5A5A5A;" x-show="detailData.description" x-text="detailData.description"></p>
                    <div x-show="detailData.pencapaians && detailData.pencapaians.length">
                        <h4 class="font-bold text-sm mb-2" style="color:#2C2C2C;">Pencapaian siswa kelas</h4>
                        <ul class="text-sm space-y-2" style="color:#5A5A5A;">
                            <template x-for="pc in (detailData.pencapaians || [])" :key="pc.id">
                                <li class="rounded-lg px-3 py-2" style="background:#FAF6F0;">
                                    <span class="font-medium" x-text="pc.anak_name"></span>
                                    <span class="block text-xs font-semibold mt-1" style="color:#1A6B6B;" x-text="pc.aspek ? (pc.aspek + (pc.indicator ? ': ' + pc.indicator : '')) : (pc.indicator || 'Aspek')"></span>
                                    <span class="text-xs font-bold mt-0.5 inline-block px-1.5 py-0.5 rounded" style="background:#D0E8E8;" x-text="pc.score"></span>
                                    <span x-show="pc.feedback" class="block text-xs mt-1" x-text="pc.feedback"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" @click="showDetailModal=false" class="btn-primary w-full">Tutup</button></div>
            </div>
        </div>
    </div>
</x-app-layout>
