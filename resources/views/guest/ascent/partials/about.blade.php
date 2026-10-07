@php
    use App\Support\GuestAscent;
    use App\Support\GuestSeo;
    $paragraphs = preg_split("/\n\s*\n/", trim($cms['about_text'] ?? ''), -1, PREG_SPLIT_NO_EMPTY);
@endphp
<section class="lg:pt-15 pt-10 lg:pb-15 pb-10" aria-labelledby="about-heading">
    <div class="container">
        <div class="grid lg:grid-cols-[60%_40%] grid-cols-1 items-center">
            <div class="relative">
                <div class="flex sm:flex-row flex-col sm:items-end gap-6">
                    <div class="wow fadeInUp" data-wow-delay=".3s">
                        <div>
                            <img src="{{ GuestAscent::asset('images/about/shap-1.png') }}" alt="" aria-hidden="true">
                        </div>
                        <div class="ml-9">
                            <img src="{{ GuestAscent::asset('images/about/about-1.png') }}" alt="{{ GuestSeo::aboutPhotoAlt($cms) }}" class="w-full">
                        </div>
                    </div>
                    <div class="flex sm:flex-col gap-8">
                        <div class="bg-warm max-w-[212px] rounded-[11px] px-5 pt-[22px] pb-6 flex flex-col items-center justify-center text-center">
                            <img src="{{ GuestAscent::asset('images/about/icreement.png') }}" alt="" aria-hidden="true">
                            <h6 class="text-xl font-bold">PAUD</h6>
                            <p class="text-sm">Terpadu &amp; modern</p>
                        </div>
                        <div class="bg-background max-w-[212px] rounded-[11px] px-5 pt-[22px] pb-6 flex flex-col justify-center drop-shadow-[0px_4.8px_24.4px_rgba(19,16,34,0.10)]">
                            <h6 class="text-xl font-bold text-secondary-foreground">{{ \App\Support\GuestBrand::name() }}</h6>
                            <p class="text-sm">Satu platform sekolah</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lg:max-w-[439px] pt-7.5">
                <p class="text-secondary-foreground font-bubblegum-sans text-[19px] wow fadeInUp">Tentang Kami</p>
                <h2 id="about-heading" class="font-bold lg:text-[32px] md:text-[28px] text-2xl lg:leading-[130%] pb-5 wow fadeInUp" data-wow-delay=".3s">{{ $cms['about_title'] }}</h2>
                @foreach($paragraphs as $i => $paragraph)
                    <p class="wow fadeInUp {{ $i > 0 ? 'mt-4' : '' }}" data-wow-delay="{{ number_format(0.4 + ($i * 0.1), 1) }}s">{{ trim($paragraph) }}</p>
                @endforeach
                <a href="{{ route('guest.tentang') }}" class="border border-gray-200 rounded-md lg:mt-10 mt-7 hover:text-cream-foreground btn inline-block wow fadeInUp" data-wow-delay=".6s">Pelajari Lebih Lanjut</a>
            </div>
        </div>
    </div>
</section>
