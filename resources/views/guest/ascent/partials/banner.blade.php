@php
    use App\Support\GuestAscent;
    use App\Support\GuestCmsImage;
@endphp
<section class="bg-warm relative overflow-hidden lg:mb-12 mb-8" aria-labelledby="hero-heading">
    <div class="container relative py-10 md:py-12 lg:py-14">
        <div class="grid lg:grid-cols-[minmax(0,9.5rem)_minmax(0,1fr)_minmax(0,9.5rem)] lg:items-center gap-8 lg:gap-6">
            <div class="relative mx-auto hidden lg:flex justify-center items-center animate-up-down guest-hero-side" aria-hidden="true">
                <img src="{{ GuestCmsImage::url($cms, 'hero_left_photo', 'images/banner/boy_img_1.png') }}" alt="" class="guest-cms-img--hero-side" decoding="async">
                <span class="absolute -left-2 top-2 border-2 border-primary rounded-[125px] w-[calc(100%-0.25rem)] h-[calc(100%-0.5rem)] pointer-events-none"></span>
            </div>
            <div class="flex flex-col items-center text-center relative z-10 min-w-0 col-span-1">
                <h1 id="hero-heading" class="font-normal xl:text-[70px] lg:text-6xl md:text-5xl text-4xl xl:leading-[128%] lg:leading-[125%] md:leading-[120%] max-w-[776px] wow fadeInUp" data-wow-delay=".3s">
                    {{ $cms['hero_title'] }}
                </h1>
                <div class="flex absolute right-4 md:right-8 lg:right-[87px] top-0 lg:top-14 animate-skw max-lg:hidden" aria-hidden="true">
                    <img src="{{ GuestAscent::asset('images/shapes/shap.png') }}" alt="" class="w-7.5 h-12.5 relative top-9">
                    <img src="{{ GuestAscent::asset('images/shapes/shap.png') }}" alt="">
                    <img src="{{ GuestAscent::asset('images/shapes/shap.png') }}" alt="" class="w-5 h-8 -mt-7">
                </div>
                <p class="pt-4 md:pt-5 max-w-[431px] wow fadeInUp" data-wow-delay=".5s">{{ $cms['hero_subtitle'] }}</p>
                <div class="mt-5 md:mt-6 flex flex-wrap justify-center gap-3 md:gap-4 wow fadeInUp" data-wow-delay=".6s">
                    <a href="{{ route('guest.daftar-sekolah') }}" class="bg-green text-cream-foreground rounded-md max-h-15 leading-normal btn">Daftar Sekolah</a>
                    <a href="{{ route('guest.kontak') }}" class="border border-gray-200 rounded-md px-6 py-3 btn hover:text-cream-foreground">Hubungi Kami</a>
                </div>
            </div>
            <div class="relative mx-auto hidden lg:flex justify-center items-center animate-up-down guest-hero-side" aria-hidden="true">
                <img src="{{ GuestCmsImage::url($cms, 'hero_right_photo', 'images/banner/boy_img_2.png') }}" alt="" class="guest-cms-img--hero-side" decoding="async">
                <span class="absolute -left-2 top-2 border-2 border-secondary rounded-[125px] w-[calc(100%-0.25rem)] h-[calc(100%-0.5rem)] pointer-events-none"></span>
            </div>
        </div>
    </div>
    <div class="lg:block hidden pointer-events-none" aria-hidden="true">
        <div class="absolute left-0 top-8 animate-left-right-2">
            <img src="{{ GuestAscent::asset('images/banner/left-circle-1.png') }}" alt="" class="max-w-[4.5rem] opacity-90">
        </div>
        <div class="absolute left-[37px] top-28 animate-left-right-2">
            <img src="{{ GuestAscent::asset('images/banner/left-circle-2.png') }}" alt="" class="max-w-[3rem] opacity-90">
        </div>
        <div class="absolute right-0 top-1/2 -translate-y-1/2 animate-up-down">
            <img src="{{ GuestAscent::asset('images/banner/right-circle.png') }}" alt="" class="max-w-[5rem] opacity-90">
        </div>
    </div>
</section>
