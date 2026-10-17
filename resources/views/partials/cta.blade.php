{{-- Closing banner: solid accent colour with faint dotted pattern. Optional $heading and $text. --}}
<section class="relative overflow-hidden bg-brand-red py-16 text-center lg:py-24">
    <div class="pointer-events-none absolute inset-0 opacity-20"
         style="background-image: radial-gradient(#ffffff 1.2px, transparent 1.2px); background-size: 14px 14px;"></div>

    <div class="relative mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold leading-tight text-white sm:text-4xl lg:text-5xl">
            {{ $heading ?? 'Need an electrician? Get a free quote today.' }}
        </h2>
        <p class="mx-auto mt-4 max-w-2xl text-base text-white/90 sm:text-lg">
            {{ $text ?? 'Tell us about your job in ' . config('emab.area_text') . ' and we will send you a clear written quotation.' }}
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
            <a href="{{ url('/quote') }}"
               class="inline-flex items-center gap-3 rounded-full bg-white py-2 pl-7 pr-2 text-sm font-semibold text-brand-navy transition-opacity hover:opacity-90">
                Get In Touch
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-navy text-white"><i class="bi bi-arrow-right"></i></span>
            </a>
            <a href="{{ tel('phone') }}"
               class="inline-flex items-center gap-2 rounded-full border-2 border-white px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-white hover:text-brand-red">
                <i class="bi bi-telephone-fill"></i>
                <span>Call Now</span>
            </a>
        </div>
    </div>
</section>