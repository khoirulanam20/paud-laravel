<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background:#1A6B6B;"><svg
                    class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg></div>
            <h2 class="font-bold text-xl" style="color:#2C2C2C;">Agenda Belajar</h2>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-data="{ showDetailModal:false, detailData:{},
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

        {{-- DETAIL MODAL --}}
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="display:none; background:rgba(0,0,0,0.45);">
            <div x-show="showDetailModal" x-transition class="modal-box max-w-2xl" @click.away="showDetailModal=false">
                <div class="modal-header">
                    <h3 class="section-title">Detail: <span x-text="detailData.title"></span></h3>
                </div>
                <div class="modal-body max-h-[75vh] overflow-y-auto space-y-5">
                    <div class="bg-gray-50 rounded-xl p-4 border" style="border-color:rgba(0,0,0,0.06);">
                        <p class="text-sm" style="color:#5A5A5A;"><strong class="text-gray-800">Tanggal:</strong> <span
                                x-text="detailData.date ? new Date(detailData.date + 'T12:00:00').toLocaleDateString('id-ID') : '-'"></span>
                        </p>
                        <div class="flex items-center gap-2 mt-2 text-sm" style="color:#5A5A5A;">
                            <strong class="text-gray-800 shrink-0">Pengajar:</strong>
                            <img x-show="detailData.pengajar_photo_url" :src="detailData.pengajar_photo_url" alt=""
                                class="h-8 w-8 rounded-xl object-cover shrink-0 border border-black/5">
                            <div x-show="!detailData.pengajar_photo_url"
                                class="h-8 w-8 rounded-xl shrink-0 flex items-center justify-center text-xs font-bold text-white bg-[#1A6B6B]"
                                x-text="(detailData.pengajar_name || '?').charAt(0).toUpperCase()"></div>
                            <span x-text="detailData.pengajar_name || '-'"></span>
                        </div>
                        <p class="text-sm mt-2" style="color:#5A5A5A;"><strong class="text-gray-800">Kelas:</strong>
                            <span x-text="detailData.kelas_name || '-'"></span>
                        </p>
                        <p class="text-sm mt-2" style="color:#5A5A5A;" x-show="detailData.description"><strong
                                class="text-gray-800">Deskripsi:</strong> <br><span
                                x-text="detailData.description"></span></p>
                    </div>

                    <div x-show="detailData.photo_urls && detailData.photo_urls.length > 0">
                        <h4 class="font-bold mb-3 text-sm text-gray-800">Dokumentasi</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <template x-for="url in detailData.photo_urls" :key="url">
                                <div
                                    class="relative aspect-square rounded-xl overflow-hidden border bg-gray-100 shadow-sm flex flex-col group transition-all hover:shadow-md">
                                    <div class="h-full w-full overflow-hidden">
                                        <img :src="url" class="w-full h-full object-cover cursor-pointer"
                                            @click="window.open(url, '_blank')">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Daftar Pencapaian Siswa Section --}}
                    <div>
                        <h4 class="font-bold mb-3 text-sm text-gray-800">Pencapaian Terkait</h4>
                        <div class="table-responsive border rounded-xl overflow-hidden"
                            style="border-color:rgba(0,0,0,0.06);">
                            <table class="w-full text-sm">
                                <thead style="background:#F5F5F3;">
                                    <tr>
                                        <th class="text-left px-4 py-3 font-semibold text-xs" style="color:#5A5A5A;">
                                            Anak</th>
                                        <th class="text-left px-4 py-3 font-semibold text-xs" style="color:#5A5A5A;">
                                            Aspek / Indikator</th>
                                        <th class="text-left px-4 py-3 font-semibold text-xs" style="color:#5A5A5A;">
                                            Nilai</th>
                                        <th class="text-left px-4 py-3 font-semibold text-xs" style="color:#5A5A5A;">
                                            Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="pc in (detailData.pencapaians || [])" :key="pc.id">
                                        <tr class="border-t" style="border-color:rgba(0,0,0,0.04);">
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <img x-show="pc.anak_photo_url" :src="pc.anak_photo_url" alt=""
                                                        class="h-8 w-8 rounded-xl object-cover shrink-0 border border-black/5">
                                                    <div x-show="!pc.anak_photo_url"
                                                        class="h-8 w-8 rounded-xl shrink-0 flex items-center justify-center text-xs font-bold text-white bg-[#1A6B6B]"
                                                        x-text="(pc.anak_name || '?').charAt(0).toUpperCase()"></div>
                                                    <span class="font-medium text-sm" style="color:#2C2C2C;"
                                                        x-text="pc.anak_name || '-'"></span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-xs" style="color:#5A5A5A;">
                                                <span class="font-semibold text-[#1A6B6B]"
                                                    x-text="pc.aspek || '—'"></span>
                                                <span x-show="pc.indicator" class="block mt-0.5"
                                                    x-text="pc.indicator"></span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span
                                                    class="text-xs font-bold px-2 py-1 rounded max-w-[12rem] leading-snug"
                                                    x-bind:style="'background:' + (pc.score_color || '#eee')"
                                                    x-text="pc.score_label || pc.score"></span>
                                            </td>
                                            <td class="px-4 py-3 text-xs" style="color:#5A5A5A;" x-text="pc.feedback">
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="!detailData.pencapaians || detailData.pencapaians.length === 0">
                                        <td colspan="4" class="px-4 py-6 text-center text-xs" style="color:#9E9790;">
                                            Belum ada data pencapaian pada kegiatan ini.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer flex gap-2">
                    <button type="button" @click="showDetailModal=false" class="btn-primary ml-auto">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>