@php
    $maxBottomItems = 4; // Max items from roleNavItems (total 5 with Dashboard)
    
    if (count($roleNavItems) <= $maxBottomItems) {
        $bottomItems = $roleNavItems;
        $moreItems = [];
    } else {
        $bottomItems = array_slice($roleNavItems, 0, $maxBottomItems - 1); // take 3
        $moreItems = array_slice($roleNavItems, $maxBottomItems - 1); // take the rest
    }
@endphp

<nav class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-black/5 shadow-[0_-4px_10px_rgba(0,0,0,0.03)] px-1 flex items-center justify-around h-[68px]" style="padding-bottom: max(env(safe-area-inset-bottom), 0.25rem); height: calc(68px + env(safe-area-inset-bottom));">
    <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center w-full h-full text-[10px] font-medium transition-colors pt-1 {{ request()->routeIs('dashboard') ? 'text-[#1A6B6B]' : 'text-gray-400 hover:text-gray-600' }}">
        <svg class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
        <span class="truncate w-full text-center">Home</span>
    </a>
    
    @foreach($bottomItems as $item)
    <a href="{{ route($item['route']) }}" class="relative flex flex-col items-center justify-center w-full h-full text-[10px] font-medium transition-colors pt-1 {{ request()->routeIs($item['pattern']) ? 'text-[#1A6B6B]' : 'text-gray-400 hover:text-gray-600' }}">
        <svg class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
        </svg>
        <span class="truncate w-full text-center px-1">{{ $item['label'] }}</span>
        @if(!empty($item['badge']) && $item['badge'] > 0)
        <span class="absolute top-1 right-2 inline-flex items-center justify-center h-4 min-w-[1rem] px-1 rounded-full text-white text-[9px] font-bold" style="background:#FF8C42;">{{ $item['badge'] }}</span>
        @endif
    </a>
    @endforeach

    @if(count($moreItems) > 0)
    <button @click="moreMenuOpen = true" class="flex flex-col items-center justify-center w-full h-full text-[10px] font-medium text-gray-400 hover:text-gray-600 focus:outline-none transition-colors pt-1">
        <svg class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <span class="truncate w-full text-center px-1">Lainnya</span>
    </button>
    @endif
</nav>

<!-- More Menu Bottom Sheet -->
@if(count($moreItems) > 0)
<div x-show="moreMenuOpen" class="lg:hidden fixed inset-0 z-50 flex flex-col justify-end" style="display: none;">
    <!-- Backdrop -->
    <div x-show="moreMenuOpen" x-transition.opacity class="absolute inset-0 bg-gray-900 bg-opacity-50" @click="moreMenuOpen = false"></div>
    
    <!-- Sheet -->
    <div x-show="moreMenuOpen" 
         x-transition:enter="transition ease-out duration-300 transform" 
         x-transition:enter-start="translate-y-full" 
         x-transition:enter-end="translate-y-0" 
         x-transition:leave="transition ease-in duration-200 transform" 
         x-transition:leave-start="translate-y-0" 
         x-transition:leave-end="translate-y-full" 
         class="relative bg-white w-full rounded-t-3xl flex flex-col shadow-2xl"
         style="padding-bottom: env(safe-area-inset-bottom);">
         
        <div class="px-6 py-4 border-b border-black/5 flex justify-between items-center shrink-0">
            <h3 class="font-bold text-lg text-[#2C2C2C]">Menu Lainnya</h3>
            <button @click="moreMenuOpen = false" class="text-gray-400 hover:text-gray-700 bg-gray-100 p-2 rounded-full focus:outline-none transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto px-4 py-4 space-y-2 max-h-[60vh]">
            @foreach($moreItems as $item)
            <a href="{{ route($item['route']) }}" @click="moreMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition-all {{ request()->routeIs($item['pattern']) ? 'bg-[#1A6B6B] text-white shadow-md shadow-[#1A6B6B]/20' : 'text-[#2C2C2C] bg-[#FAF6F0] hover:bg-black/5' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                </svg>
                <div class="flex-1">{{ $item['label'] }}</div>
                @if(!empty($item['badge']) && $item['badge'] > 0)
                <span class="inline-flex items-center justify-center h-5 px-2 rounded-full text-white text-[10px] font-bold" style="background:#FF8C42;">{{ $item['badge'] }}</span>
                @endif
                <svg class="w-4 h-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif
