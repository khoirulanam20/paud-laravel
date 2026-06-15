@php
    use App\Support\AiChatDataSource;
    use App\Support\AiPersonaScope;
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background:#1A6B6B;">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-xl" style="color:#2C2C2C;">Persona AI</h2>
                <p class="text-xs" style="color:#9E9790;">Atur persona terpisah per fungsi AI · {{ $sekolah->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-4 md:py-8 px-3 md:px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto"
         x-data="{
            activeTab: @js($activeTab),
            generateLoading: {},
            generateResult: {},
            briefs: {},
            fields: @js(collect($personas)->mapWithKeys(fn ($p, $scope) => [$scope => [
                'name' => old('name', $p->name),
                'role_title' => old('role_title', $p->role_title ?? ''),
                'description' => old('description', $p->description ?? ''),
                'gender' => old('gender', $p->gender ?? ''),
                'age' => old('age', $p->age),
                'dialog_language' => old('dialog_language', $p->dialog_language ?? 'Bahasa Indonesia'),
                'personality_traits' => old('personality_traits', $p->personality_traits ?? ''),
                'communication_style' => old('communication_style', $p->communication_style ?? ''),
                'behavior_guidelines' => old('behavior_guidelines', $p->behavior_guidelines ?? ''),
                'background' => old('background', $p->background ?? ''),
            ]])->toArray()),
            setTab(scope) {
                this.activeTab = scope;
                const url = new URL(window.location);
                url.searchParams.set('tab', scope);
                window.history.replaceState({}, '', url);
            },
            async generatePersona(scope) {
                if (this.generateLoading[scope]) {
                    return;
                }

                this.generateLoading[scope] = true;
                delete this.generateResult[scope];

                try {
                    const res = await fetch('{{ route('admin.ai-persona.generate') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({ scope, brief: this.briefs[scope] || '' }),
                    });

                    let data = {};
                    try {
                        data = await res.json();
                    } catch (parseError) {
                        throw new Error('Respons server tidak valid.');
                    }

                    if (!res.ok || data.ok !== true) {
                        this.generateResult[scope] = {
                            ok: false,
                            error: data.error || data.message || 'Generate gagal. Coba lagi.',
                        };
                        return;
                    }

                    if (data.fields) {
                        this.fields[scope] = { ...this.fields[scope], ...data.fields };
                    }

                    this.generateResult[scope] = { ok: true };
                } catch (e) {
                    this.generateResult[scope] = {
                        ok: false,
                        error: e.message || 'Gagal generate.',
                    };
                } finally {
                    this.generateLoading[scope] = false;
                }
            }
         }">

        @if(session('success'))
            <div class="alert-success mb-5">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert-danger mb-5">
                <ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="mb-6 rounded-2xl border p-5 flex items-start gap-4"
            style="background:{{ $aiConfigured ? '#D0E8E8' : '#FEF9EC' }}; border-color: {{ $aiConfigured ? '#1A6B6B33' : '#F0B84233' }};">
            <div class="h-10 w-10 rounded-xl flex items-center justify-center shrink-0"
                style="background:{{ $aiConfigured ? '#1A6B6B' : '#F0B842' }};">
                @if($aiConfigured)
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @else
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                @endif
            </div>
            <div>
                <div class="font-bold text-sm" style="color:#2C2C2C;">
                    {{ $aiConfigured ? 'AI Aktif — siap generate persona' : 'AI Belum Dikonfigurasi' }}
                </div>
                <div class="text-xs mt-1" style="color:#6B6560;">
                    Setiap tab punya persona sendiri untuk fungsi AI berbeda. Nonaktifkan persona jika ingin pakai default sistem.
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 mb-6">
            @foreach(AiPersonaScope::labels() as $scope => $label)
                <button type="button"
                        @click="setTab('{{ $scope }}')"
                        class="px-4 py-2 rounded-xl text-sm font-semibold border transition-all"
                        :class="activeTab === '{{ $scope }}'
                            ? 'text-white border-transparent shadow-md'
                            : 'text-[#6B6560] border-black/10 bg-white hover:bg-black/5'"
                        :style="activeTab === '{{ $scope }}' ? 'background:#1A6B6B' : ''">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        @foreach($personas as $scope => $persona)
            <div x-show="activeTab === '{{ $scope }}'" x-cloak style="display:none;">
                <div x-show="!generateLoading['{{ $scope }}'] && generateResult['{{ $scope }}']" x-transition
                     class="mb-6 rounded-2xl border p-4"
                     :style="generateResult['{{ $scope }}']?.ok ? 'background:#D0E8E8; border-color:#1A6B6B33' : 'background:#FEE2E2; border-color:#EF444433'">
                    <div class="text-sm font-semibold" style="color:#2C2C2C;"
                         x-text="generateResult['{{ $scope }}']?.ok ? 'Persona berhasil di-generate. Tinjau lalu simpan.' : 'Generate gagal'"></div>
                    <div class="text-xs mt-1" style="color:#DC2626;"
                         x-show="generateResult['{{ $scope }}'] && !generateResult['{{ $scope }}'].ok"
                         x-text="generateResult['{{ $scope }}']?.error || 'Terjadi kesalahan saat generate.'"></div>
                </div>

                <div class="card overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                        <h3 class="section-title">Generate — {{ AiPersonaScope::label($scope) }}</h3>
                        <p class="section-subtitle mt-1">{{ AiPersonaScope::generateContext($scope) }}</p>
                    </div>
                    <div class="px-6 py-6 space-y-4">
                        <div>
                            <label class="input-label">Deskripsi sekolah untuk AI (opsional)</label>
                            <textarea rows="2" maxlength="500" class="input-field resize-none"
                                x-model="briefs['{{ $scope }}']"
                                placeholder="Contoh: PAUD dengan fokus karakter dan kreativitas."></textarea>
                        </div>
                        <button type="button" @click="generatePersona('{{ $scope }}')"
                            :disabled="generateLoading['{{ $scope }}'] || {{ $aiConfigured ? 'false' : 'true' }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold border transition-all disabled:opacity-50"
                            style="color:#1A6B6B; background:#D0E8E8; border-color:#1A6B6B33;">
                            <span x-show="!generateLoading['{{ $scope }}']">Generate dengan AI</span>
                            <span x-show="generateLoading['{{ $scope }}']">Men-generate...</span>
                        </button>
                    </div>
                </div>

                @if($scope === AiPersonaScope::CHAT_ORANGTUA)
                    <div class="card overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                            <h3 class="section-title">Akses Data Chat</h3>
                            <p class="section-subtitle mt-1">Pilih data apa saja yang boleh dibaca AI saat menjawab chat orang tua.</p>
                        </div>
                        <form action="{{ route('admin.ai-persona.data-access.update') }}" method="POST">
                            @csrf
                            <div class="px-6 py-6 space-y-5">
                                <div class="grid sm:grid-cols-2 gap-3">
                                    @foreach(AiChatDataSource::toggleKeys() as $key)
                                        <div class="flex items-center gap-3">
                                            <input type="hidden" name="{{ $key }}" value="0">
                                            <input type="checkbox" name="{{ $key }}" value="1" id="data_access_{{ $key }}"
                                                class="rounded border-gray-300 text-[#1A6B6B] focus:ring-[#1A6B6B]"
                                                {{ old($key, $dataAccess->{$key}) ? 'checked' : '' }}>
                                            <label for="data_access_{{ $key }}" class="text-sm font-medium" style="color:#2C2C2C;">
                                                {{ AiChatDataSource::label($key) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="grid md:grid-cols-2 gap-5">
                                    <div class="rounded-xl border p-4 space-y-3" style="background:#FAF6F0; border-color:rgba(0,0,0,0.06);">
                                        <div class="text-sm font-semibold" style="color:#2C2C2C;">Rentang Agenda Belajar</div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="input-label">Hari ke belakang</label>
                                                <input type="number" name="agenda_days_back" min="0" max="30" required class="input-field bg-white"
                                                    value="{{ old('agenda_days_back', $dataAccess->agenda_days_back) }}">
                                            </div>
                                            <div>
                                                <label class="input-label">Hari ke depan</label>
                                                <input type="number" name="agenda_days_forward" min="0" max="30" required class="input-field bg-white"
                                                    value="{{ old('agenda_days_forward', $dataAccess->agenda_days_forward) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rounded-xl border p-4 space-y-3" style="background:#FAF6F0; border-color:rgba(0,0,0,0.06);">
                                        <div class="text-sm font-semibold" style="color:#2C2C2C;">Rentang Kegiatan Rutin</div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="input-label">Hari ke belakang</label>
                                                <input type="number" name="kegiatan_rutin_days_back" min="0" max="30" required class="input-field bg-white"
                                                    value="{{ old('kegiatan_rutin_days_back', $dataAccess->kegiatan_rutin_days_back) }}">
                                            </div>
                                            <div>
                                                <label class="input-label">Hari ke depan</label>
                                                <input type="number" name="kegiatan_rutin_days_forward" min="0" max="30" required class="input-field bg-white"
                                                    value="{{ old('kegiatan_rutin_days_forward', $dataAccess->kegiatan_rutin_days_forward) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-6 pb-6 flex justify-end border-t pt-5" style="border-color:rgba(0,0,0,0.06);">
                                <button type="submit" class="btn-primary">Simpan Pengaturan Akses Data</button>
                            </div>
                        </form>
                    </div>
                @endif

                <div class="card overflow-hidden">
                    <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                        <h3 class="section-title">Pengaturan — {{ AiPersonaScope::label($scope) }}</h3>
                    </div>
                    <form action="{{ route('admin.ai-persona.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="scope" value="{{ $scope }}">
                        <div class="px-6 py-6 space-y-5">
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" id="is_active_{{ $scope }}"
                                    class="rounded border-gray-300 text-[#1A6B6B] focus:ring-[#1A6B6B]"
                                    {{ old('is_active', $persona->is_active) ? 'checked' : '' }}>
                                <label for="is_active_{{ $scope }}" class="text-sm font-medium" style="color:#2C2C2C;">Aktifkan persona</label>
                            </div>

                            <div class="grid md:grid-cols-2 gap-5">
                                <div>
                                    <label class="input-label">1. Nama Persona</label>
                                    <input type="text" name="name" maxlength="120" required class="input-field"
                                        x-model="fields['{{ $scope }}'].name">
                                </div>
                                <div>
                                    <label class="input-label">2. Judul Peran</label>
                                    <input type="text" name="role_title" maxlength="120" class="input-field"
                                        x-model="fields['{{ $scope }}'].role_title"
                                        placeholder="{{ AiPersonaScope::defaultRoleTitle($scope) }}">
                                </div>
                            </div>

                            <div>
                                <label class="input-label">3. Deskripsi</label>
                                <textarea name="description" rows="2" maxlength="2000" class="input-field resize-none"
                                    x-model="fields['{{ $scope }}'].description"></textarea>
                            </div>

                            <div class="grid md:grid-cols-3 gap-5">
                                <div>
                                    <label class="input-label">4. Jenis Kelamin</label>
                                    <select name="gender" class="input-field" x-model="fields['{{ $scope }}'].gender">
                                        <option value="">— Pilih —</option>
                                        <option value="perempuan">Perempuan</option>
                                        <option value="laki_laki">Laki-laki</option>
                                        <option value="netral">Netral</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="input-label">5. Usia</label>
                                    <input type="number" name="age" min="18" max="80" class="input-field"
                                        x-model="fields['{{ $scope }}'].age">
                                </div>
                                <div>
                                    <label class="input-label">6. Bahasa Dialog</label>
                                    <input type="text" name="dialog_language" maxlength="60" required class="input-field"
                                        x-model="fields['{{ $scope }}'].dialog_language">
                                </div>
                            </div>

                            <div class="rounded-xl border p-4 space-y-4" style="background:#FAF6F0; border-color:rgba(0,0,0,0.06);">
                                <div class="text-sm font-semibold" style="color:#2C2C2C;">7. Karakter</div>
                                <div>
                                    <label class="input-label">Sifat Kepribadian</label>
                                    <textarea name="personality_traits" rows="2" maxlength="2000" class="input-field resize-none bg-white"
                                        x-model="fields['{{ $scope }}'].personality_traits"></textarea>
                                </div>
                                <div>
                                    <label class="input-label">Gaya Komunikasi</label>
                                    <textarea name="communication_style" rows="2" maxlength="2000" class="input-field resize-none bg-white"
                                        x-model="fields['{{ $scope }}'].communication_style"></textarea>
                                </div>
                                <div>
                                    <label class="input-label">Panduan Perilaku AI</label>
                                    <textarea name="behavior_guidelines" rows="3" maxlength="2000" class="input-field resize-none bg-white"
                                        x-model="fields['{{ $scope }}'].behavior_guidelines"></textarea>
                                </div>
                            </div>

                            <div>
                                <label class="input-label">8. Latar Belakang</label>
                                <textarea name="background" rows="3" maxlength="2000" class="input-field resize-none"
                                    x-model="fields['{{ $scope }}'].background"></textarea>
                            </div>

                            @if($persona->ai_generated_at)
                                <p class="text-[11px]" style="color:#9E9790;">
                                    Terakhir di-generate AI: {{ $persona->ai_generated_at->format('d M Y, H:i') }}
                                </p>
                            @endif

                            <div class="rounded-xl border p-4" style="background:#FAF6F0; border-color:rgba(0,0,0,0.06);">
                                <div class="text-xs font-semibold mb-1" style="color:#2C2C2C;">Preview identitas</div>
                                <p class="text-sm" style="color:#6B6560;">
                                    <span x-text="fields['{{ $scope }}'].name || 'Nama persona'"></span>
                                    · <span x-text="fields['{{ $scope }}'].role_title || '{{ AiPersonaScope::defaultRoleTitle($scope) }}'"></span>
                                    <template x-if="fields['{{ $scope }}'].age">
                                        <span> · <span x-text="fields['{{ $scope }}'].age"></span> tahun</span>
                                    </template>
                                </p>
                            </div>
                        </div>

                        <div class="px-6 pb-6 flex justify-end border-t pt-5" style="border-color:rgba(0,0,0,0.06);">
                            <button type="submit" class="btn-primary">Simpan Persona</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
