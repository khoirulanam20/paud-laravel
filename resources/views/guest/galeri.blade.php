@php
    use App\Support\GuestSeo;
    use Illuminate\Support\Facades\Storage;
    $pageSeo = GuestSeo::forInnerPage(
        $cms,
        'galeri',
        'Galeri',
        route('guest.galeri'),
        $cms['seo_galeri_description'] ?? '',
    );
    $galleries = [];
    for ($gi = 1; $gi <= 6; $gi++) {
        if (!empty($cms['gallery_'.$gi])) {
            $galleries[] = ['path' => $cms['gallery_'.$gi], 'index' => $gi];
        }
    }
@endphp
<x-guest-ascent :cms="$cms" title="Galeri" :metaDesc="$pageSeo['description']" :canonical="route('guest.galeri')">
    @include('guest.ascent.partials.page-hero', [
        'title' => $pageSeo['h1'],
        'breadcrumbLabel' => 'Galeri',
        'subtitle' => $cms['page_galeri_intro'] ?? '',
    ])
    <section class="lg:pb-15 pb-10" aria-labelledby="gallery-page-heading">
        <div class="container">
            <div class="text-center flex flex-col items-center mb-10">
                <h2 id="gallery-page-heading" class="font-bold lg:text-[32px] text-2xl max-w-[630px]">{{ $cms['section_gallery_title'] }}</h2>
                <p class="mt-3 max-w-xl text-muted-foreground">{{ $cms['section_gallery_subtitle'] }}</p>
            </div>
            @if(count($galleries))
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6" x-data="{ lightbox: null }">
                    @foreach($galleries as $photo)
                        @php $alt = GuestSeo::galleryAlt($cms, $photo['index']); @endphp
                        <button type="button"
                                @click="lightbox = '{{ Storage::url($photo['path']) }}'"
                                class="overflow-hidden rounded-[10px] aspect-square cursor-pointer group focus:outline-none focus:ring-2 focus:ring-primary/40"
                                aria-label="{{ $alt }}">
                            <img src="{{ Storage::url($photo['path']) }}" alt="{{ $alt }}"
                                 class="w-full h-full max-h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy" decoding="async">
                        </button>
                    @endforeach
                    <div x-show="lightbox" x-transition x-cloak
                         @click="lightbox = null" @keydown.escape.window="lightbox = null"
                         class="fixed inset-0 z-[100] flex items-center justify-center p-4 cursor-pointer bg-black/75"
                         style="display: none;">
                        <img :src="lightbox" alt="Preview" class="max-w-full max-h-[90vh] rounded-[10px] shadow-2xl" @click.stop>
                    </div>
                </div>
            @else
                <div class="text-center max-w-lg mx-auto py-10">
                    <p class="text-muted-foreground">{{ $cms['section_gallery_subtitle'] }}</p>
                    <a href="{{ route('guest.kontak') }}" class="btn-rounded-full inline-flex mt-8">Hubungi Kami</a>
                </div>
            @endif
        </div>
    </section>
    @include('guest.ascent.partials.stay-cta', ['cms' => $cms])
</x-guest-ascent>
