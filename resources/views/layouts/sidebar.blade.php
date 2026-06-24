<!-- Sidebar -->
<aside class="hidden lg:flex lg:flex-col lg:static bg-[#FAF6F0] border-r border-black/5 h-screen shrink-0 shadow-[2px_0_8px_rgba(0,0,0,0.04)] sidebar-transition"
       :class="sidebarCollapsed ? 'w-[72px]' : 'w-64'">
    <!-- Logo -->
    <div class="flex items-center h-16 border-b border-black/5 shrink-0"
         :class="sidebarCollapsed ? 'justify-center px-3' : 'px-6'">
        <a href="{{ route($homeRoute) }}" class="flex items-center gap-2.5">
            <div class="h-9 w-9 rounded-xl flex items-center justify-center bg-[#1A6B6B] shadow-[2px_3px_8px_rgba(26,107,107,0.35)] shrink-0">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <span x-show="!sidebarCollapsed" x-transition:enter="fade-enter" x-transition:leave="fade-leave" class="font-bold text-base text-[#2C2C2C] whitespace-nowrap">SIPP</span>
        </a>
    </div>

    <!-- Navigation Links -->
    <div class="flex-1 overflow-y-auto py-4 space-y-1 scroll-smooth"
         :class="sidebarCollapsed ? 'px-2' : 'px-3'"
         x-init="$nextTick(() => { const el = document.getElementById('nav-active-item'); if (el) { el.scrollIntoView({ block: 'nearest' }); } })">
        @if($showDashboardNav)
        <a href="{{ route('dashboard') }}"
           @if(request()->routeIs('dashboard')) id="nav-active-item" @endif
           data-tour="nav-dashboard"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-[#1A6B6B] text-white shadow-sm' : 'text-[#2C2C2C] hover:bg-black/5' }}"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span x-show="!sidebarCollapsed" x-transition:enter="fade-enter" x-transition:leave="fade-leave" class="truncate">Dashboard</span>
        </a>
        @endif
        @foreach ($roleNavItems as $nav)
            @if(!empty($nav['collapsible']))
                @php
                    $collapsiblePatterns = is_array($nav['pattern']) ? $nav['pattern'] : [$nav['pattern']];
                    $collapsibleActive = request()->routeIs($collapsiblePatterns);
                    $collapsibleItems = [];
                    foreach ($nav['sections'] as $section) {
                        foreach ($section['items'] as $item) {
                            $collapsibleItems[] = $item;
                        }
                    }
                @endphp
                @foreach($collapsibleItems as $item)
                    <a href="{{ route($item['route']) }}"
                       x-show="sidebarCollapsed"
                       @if(request()->routeIs($item['pattern'])) id="nav-active-item" @endif
                       data-tour="nav-{{ $item['route'] }}"
                       class="flex items-center justify-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs($item['pattern']) ? 'bg-[#1A6B6B] text-white shadow-sm' : 'text-[#2C2C2C] hover:bg-black/5' }}"
                       title="{{ $item['label'] }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                        </svg>
                    </a>
                @endforeach
                <div x-show="!sidebarCollapsed"
                     x-data="{ open: {{ $collapsibleActive ? 'true' : 'false' }} }"
                     class="pt-4">
                    <button type="button"
                            @click="open = !open"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ $collapsibleActive ? 'bg-[#1A6B6B]/10 text-[#1A6B6B]' : 'text-[#2C2C2C] hover:bg-black/5' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $nav['icon'] }}" />
                        </svg>
                        <span class="truncate flex-1 text-left">{{ $nav['group'] }}</span>
                        <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="mt-1 space-y-0.5">
                        @foreach($nav['sections'] as $section)
                            <div class="pl-4 pt-2 pb-1 text-[9px] font-semibold text-[#9E9790] uppercase tracking-wider">
                                {{ $section['label'] }}
                            </div>
                            @foreach($section['items'] as $item)
                                <a href="{{ route($item['route']) }}"
                                   @if(request()->routeIs($item['pattern'])) id="nav-active-item" @endif
                                   data-tour="nav-{{ $item['route'] }}"
                                   class="flex items-center gap-3 pl-6 pr-3 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs($item['pattern']) ? 'bg-[#1A6B6B] text-white shadow-sm' : 'text-[#2C2C2C] hover:bg-black/5' }}">
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                                    </svg>
                                    <span class="truncate">{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            @elseif(isset($nav['group']))
                <div x-show="!sidebarCollapsed" x-transition:enter="fade-enter" x-transition:leave="fade-leave" class="px-3 pt-4 pb-2 text-[10px] font-bold text-[#9E9790] uppercase tracking-wider">
                    {{ $nav['group'] }}
                </div>
                @foreach($nav['items'] as $item)
                    <a href="{{ route($item['route']) }}"
                       @if(request()->routeIs($item['pattern'])) id="nav-active-item" @endif
                       data-tour="nav-{{ $item['route'] }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs($item['pattern']) ? 'bg-[#1A6B6B] text-white shadow-sm' : 'text-[#2C2C2C] hover:bg-black/5' }}"
                       :class="sidebarCollapsed ? 'justify-center' : ''">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition:enter="fade-enter" x-transition:leave="fade-leave" class="truncate">{{ $item['label'] }}</span>
                        @if(!empty($item['badge']) && $item['badge'] > 0)
                        <span x-show="!sidebarCollapsed" x-transition:enter="fade-enter" x-transition:leave="fade-leave" class="ml-auto inline-flex items-center justify-center h-5 px-2 rounded-full text-white text-[10px] font-bold" style="background:#FF8C42;">{{ $item['badge'] }}</span>
                        @endif
                    </a>
                @endforeach
            @else
                <a href="{{ route($nav['route']) }}"
                   @if(request()->routeIs($nav['pattern'])) id="nav-active-item" @endif
                   data-tour="nav-{{ $nav['route'] }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs($nav['pattern']) ? 'bg-[#1A6B6B] text-white shadow-sm' : 'text-[#2C2C2C] hover:bg-black/5' }}"
                   :class="sidebarCollapsed ? 'justify-center' : ''">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $nav['icon'] }}" />
                    </svg>
                    <span x-show="!sidebarCollapsed" x-transition:enter="fade-enter" x-transition:leave="fade-leave" class="truncate">{{ $nav['label'] }}</span>
                    @if(!empty($nav['badge']) && $nav['badge'] > 0)
                    <span x-show="!sidebarCollapsed" x-transition:enter="fade-enter" x-transition:leave="fade-leave" class="ml-auto inline-flex items-center justify-center h-5 px-2 rounded-full text-white text-[10px] font-bold" style="background:#FF8C42;">{{ $nav['badge'] }}</span>
                    @endif
                </a>
            @endif
        @endforeach
    </div>

    {{-- Toggle Collapse Button (di bawah sidebar) --}}
    <div class="shrink-0 border-t border-black/5 px-3">
        <button type="button"
                @click="sidebarCollapsed = !sidebarCollapsed"
                class="w-full flex items-center gap-2 h-12 rounded-lg text-[#9E9790] hover:bg-black/5 hover:text-[#2C2C2C] transition-colors"
                :class="sidebarCollapsed ? 'justify-center' : 'px-3'"
                title="Toggle sidebar">
            <svg class="h-4 w-4 shrink-0 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"
                      x-show="!sidebarCollapsed" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M6 5l7 7-7 7"
                      x-show="sidebarCollapsed" />
            </svg>
            <span x-show="!sidebarCollapsed" x-transition:enter="fade-enter" x-transition:leave="fade-leave" class="text-xs font-medium whitespace-nowrap">Ciutkan</span>
        </button>
    </div>
</aside>
