@props([])

@php
    $user = auth()->user();
    $tabs = [];

    if ($user?->can('menu.role')) {
        $tabs[] = ['route' => 'admin.role.index', 'label' => 'Role', 'active' => request()->routeIs('admin.role.*')];
    }
    if ($user?->can('menu.pengguna')) {
        $tabs[] = ['route' => 'admin.pengguna.index', 'label' => 'Pengguna', 'active' => request()->routeIs('admin.pengguna.*')];
    }
    if ($user?->can('menu.log-aktivitas')) {
        $tabs[] = ['route' => 'admin.activity-log.index', 'label' => 'Log Aktivitas', 'active' => request()->routeIs('admin.activity-log.*')];
    }
    if ($user?->can('menu.setting-akuntansi')) {
        $tabs[] = ['route' => 'admin.akuntansi-setting.index', 'label' => 'Akuntansi', 'active' => request()->routeIs('admin.akuntansi-setting.*')];
    }
    if ($user?->can('menu.pengaturan-ai')) {
        $tabs[] = ['route' => 'admin.ai-persona.index', 'label' => 'AI', 'active' => request()->routeIs('admin.ai-persona.*')];
    }
@endphp

@if(count($tabs) > 1)
    <div {{ $attributes->merge(['class' => 'card p-4 mb-5']) }}>
        <div class="flex flex-wrap gap-2">
            @foreach($tabs as $tab)
                <a href="{{ route($tab['route']) }}"
                   class="px-4 py-2 rounded-xl text-sm font-semibold border transition-all {{ $tab['active'] ? 'text-white border-transparent shadow-md' : 'text-[#6B6560] border-black/10 bg-white hover:bg-black/5' }}"
                   @if($tab['active']) style="background:#1A6B6B" @endif>
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </div>
    </div>
@endif
