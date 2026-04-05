<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: #1A6B6B;"><svg
                    class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg></div>
            <h2 class="font-bold text-xl" style="color: #2C2C2C;">Agenda Kegiatan</h2>
        </div>
    </x-slot>
    <div class="py-3 md:py-8 px-2 sm:px-4 md:px-6 lg:px-8 max-w-7xl mx-auto" x-data="{ showDetailModal:false, detailData:{},
            onCalClick(detail){
                const p = detail.extendedProps || {};
                if (p.mode !== 'readonly' || !p.detail) return;
                this.detailData = p.detail;
                this.showDetailModal = true;
            }
         }" @kegiatan-cal-click.window="onCalClick($event.detail)">

        <div class="card overflow-hidden">
            <div class="px-4 py-3 md:px-6 md:py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                <h3 class="section-title text-sm md:text-base">Kalender Jurnal Kegiatan</h3>
            </div>
            <div class="p-2 sm:p-3 md:p-6">
                <x-jurnal-kalender :events="$calendarEvents" :year="$year" :month="$month" />
            </div>
        </div>

        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showDetailModal" x-transition class="modal-box max-w-lg" @click.away="showDetailModal=false">
                <div class="modal-header">
                    <h3 class="section-title" x-text="detailData.title"></h3>
                </div>
                <div class="modal-body space-y-4 max-h-[70vh] overflow-y-auto">
                    <p class="text-sm" style="color:#5A5A5A;"><strong>Tanggal:</strong> <span
                            x-text="detailData.date ? new Date(detailData.date + 'T12:00:00').toLocaleDateString('id-ID') : '-'"></span>
                    </p>
                    <div class="flex items-center gap-2 text-sm" style="color:#5A5A5A;">
                        <strong class="shrink-0">Pengajar:</strong>
                        <img x-show="detailData.pengajar_photo_url" :src="detailData.pengajar_photo_url" alt=""
                            class="h-8 w-8 rounded-xl object-cover shrink-0 border border-black/5">
                        <div x-show="!detailData.pengajar_photo_url"
                            class="h-8 w-8 rounded-xl shrink-0 flex items-center justify-center text-xs font-bold text-white bg-[#1A6B6B]"
                            x-text="(detailData.pengajar_name || '?').charAt(0).toUpperCase()"></div>
                        <span x-text="detailData.pengajar_name || '-'"></span>
                    </div>
                    <p class="text-sm" style="color:#5A5A5A;"><strong>Kelas:</strong> <span
                            x-text="detailData.kelas_name || '-'"></span></p>
                    <div x-show="detailData.photo_urls && detailData.photo_urls.length > 0"
                        class="grid grid-cols-2 gap-2">
                        <template x-for="url in detailData.photo_urls" :key="url">
                            <div class="rounded-xl overflow-hidden border bg-gray-50"
                                style="border-color:rgba(0,0,0,0.08);">
                                <img :src="url" alt="" class="w-full aspect-video object-cover cursor-pointer"
                                    @click="window.open(url, '_blank')">
                            </div>
                        </template>
                    </div>
                    <p class="text-sm leading-relaxed" style="color:#5A5A5A;" x-show="detailData.description"
                        x-text="detailData.description"></p>
                    <div x-show="detailData.pencapaians && detailData.pencapaians.length">
                        <h4 class="font-bold text-sm mb-2" style="color:#2C2C2C;">Pencapaian terkait</h4>
                        <ul class="text-sm space-y-2" style="color:#5A5A5A;">
                            <template x-for="pc in (detailData.pencapaians || [])" :key="pc.id">
                                <li class="rounded-lg px-3 py-2" style="background:#FAF6F0;">
                                    <div x-show="pc.anak_name" class="flex items-center gap-2 mb-1">
                                        <img x-show="pc.anak_photo_url" :src="pc.anak_photo_url" alt=""
                                            class="h-7 w-7 rounded-lg object-cover shrink-0 border border-black/5">
                                        <div x-show="!pc.anak_photo_url"
                                            class="h-7 w-7 rounded-lg shrink-0 flex items-center justify-center text-[10px] font-bold text-white bg-[#1A6B6B]"
                                            x-text="(pc.anak_name || '?').charAt(0).toUpperCase()"></div>
                                        <span class="text-xs font-semibold" style="color:#2C2C2C;"
                                            x-text="pc.anak_name"></span>
                                    </div>
                                    <span class="block text-xs font-semibold mt-1" style="color:#1A6B6B;"
                                        x-text="pc.aspek ? (pc.aspek + (pc.indicator ? ': ' + pc.indicator : '')) : (pc.indicator || 'Aspek')"></span>
                                    <span
                                        class="text-xs font-bold mt-0.5 inline-block px-1.5 py-0.5 rounded max-w-[12rem] leading-snug"
                                        x-bind:style="'background:' + (pc.score_color || '#eee')"
                                        x-text="pc.score_label || pc.score"></span>
                                    <span x-show="pc.feedback" class="block text-xs mt-1" x-text="pc.feedback"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" @click="showDetailModal=false"
                        class="btn-primary w-full">Tutup</button></div>
            </div>
        </div>
    </div>
</x-app-layout>