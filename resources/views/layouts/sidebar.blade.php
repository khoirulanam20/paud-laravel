<!-- Sidebar -->
<aside class="hidden lg:flex lg:flex-col lg:static w-64 bg-[#FAF6F0] border-r border-black/5 h-screen overflow-y-auto shrink-0 shadow-[2px_0_8px_rgba(0,0,0,0.04)]">
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-black/5 shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
            <div class="h-9 w-9 rounded-xl flex items-center justify-center bg-[#1A6B6B] shadow-[2px_3px_8px_rgba(26,107,107,0.35)]">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <span class="font-bold text-base text-[#2C2C2C]">PAUD Manager</span>
        </a>
    </div>

    <!-- Navigation Links -->
    <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        <a href="{{ route('dashboard') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-[#1A6B6B] text-white shadow-sm' : 'text-[#2C2C2C] hover:bg-black/5' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Dashboard
        </a>
        @foreach ($roleNavItems as $item)
        <a href="{{ route($item['route']) }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs($item['pattern']) ? 'bg-[#1A6B6B] text-white shadow-sm' : 'text-[#2C2C2C] hover:bg-black/5' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
            </svg>
            {{ $item['label'] }}
            @if(!empty($item['badge']) && $item['badge'] > 0)
            <span class="ml-auto inline-flex items-center justify-center h-5 px-2 rounded-full text-white text-[10px] font-bold" style="background:#FF8C42;">{{ $item['badge'] }}</span>
            @endif
        </a>
        @endforeach
    </div>
</aside>
