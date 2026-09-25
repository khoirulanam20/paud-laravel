@props([
    'name',
    'options',
    'value' => '',
    'required' => false,
    'placeholder' => 'Ketik kode atau nama akun…',
    'watchParent' => false,
])

@php
    $optionsJson = collect($options)->map(function ($a) {
        $text = (string) ($a->uraian ?? $a->nama ?? '');
        return [
            'id' => (string) $a->id,
            'label' => $a->kode.' — '.$text,
            'search' => strtolower($a->kode.' '.$text.' '.($a->nama ?? '')),
        ];
    })->values()->all();
    $initial = (string) old($name, $value);
@endphp

<div
    x-data="{
        selected: @js($initial),
        q: '',
        open: false,
        pos: { top: 0, left: 0, width: 0 },
        options: @js($optionsJson),
        labelFor(id) {
            const o = this.options.find(x => x.id === String(id));
            return o ? o.label : '';
        },
        syncQ() { this.q = this.selected ? this.labelFor(this.selected) : ''; },
        get filteredOptions() {
            const t = this.q.toLowerCase().trim();
            if (!t) return this.options.slice(0, 80);
            const exact = this.labelFor(this.selected).toLowerCase();
            if (t === exact) return this.options.slice(0, 80);
            return this.options.filter(o => o.search.includes(t) || o.label.toLowerCase().includes(t)).slice(0, 80);
        },
        pick(id) {
            this.selected = String(id);
            this.syncQ();
            this.open = false;
        },
        onInput() {
            this.open = true;
            this.updatePosition();
            if (this.q !== this.labelFor(this.selected)) this.selected = '';
        },
        updatePosition() {
            const el = this.$refs.searchInput;
            if (!el) return;
            const r = el.getBoundingClientRect();
            this.pos = { top: r.bottom + 4, left: r.left, width: r.width };
        },
    }"
    x-init="
        @if($watchParent)
        if ($parent.editData?.akun_lawan_id) {
            selected = String($parent.editData.akun_lawan_id);
        }
        $watch('$parent.editData.akun_lawan_id', id => {
            if (!$parent.showEditModal) return;
            selected = id ? String(id) : '';
            syncQ();
        });
        @endif
        syncQ();
    "
    class="relative"
    @click.outside="open = false"
    {{ $attributes->except(['class']) }}
>
    <input type="hidden" name="{{ $name }}" x-model="selected" @if($required) required @endif>
    <input
        type="text"
        x-ref="searchInput"
        class="input-field {{ $attributes->get('class') }}"
        x-model="q"
        @focus="updatePosition(); open = true"
        @input="onInput()"
        placeholder="{{ $placeholder }}"
        autocomplete="off"
        role="combobox"
        aria-autocomplete="list"
        :aria-expanded="open"
    >
    @if(count($optionsJson) === 0)
        <p class="text-xs mt-1" style="color:#9E9790;">Belum ada akun untuk jenis ini. Cek Kode Rekening / COA.</p>
    @endif
    <template x-teleport="body">
        <ul
            x-show="open && filteredOptions.length"
            x-cloak
            :style="`position:fixed;top:${pos.top}px;left:${pos.left}px;width:${pos.width}px;z-index:9999`"
            class="max-h-52 overflow-y-auto rounded-lg border bg-white shadow-lg text-sm list-none p-1"
            style="border-color:rgba(0,0,0,0.1);"
            role="listbox"
            @click.outside="open = false"
        >
            <template x-for="opt in filteredOptions" :key="opt.id">
                <li
                    class="px-3 py-2 rounded-md cursor-pointer hover:bg-teal-50"
                    :class="selected === opt.id && 'bg-teal-50 font-medium'"
                    role="option"
                    @mousedown.prevent="pick(opt.id)"
                    x-text="opt.label"
                ></li>
            </template>
        </ul>
    </template>
    <p x-show="open && q.trim() && filteredOptions.length === 0" class="text-xs mt-1 px-1" style="color:#9E9790;">Tidak ada akun yang cocok.</p>
</div>
