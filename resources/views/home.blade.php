@extends('layouts.app')

@section('title', setting('company_name') . ' | Delivering results, Exceeding Expectations')
@section('description', 'Wiring, solar, lighting, security systems and repairs for homes, shops and businesses in ' . config('emab.area_text') . '.')

@section('content')

@php
    // Returns the image URL if the file exists in /public, otherwise null (page shows a navy block instead of a broken image)
    $img = fn ($path) => $path && file_exists(public_path($path)) ? asset($path) : null;

    // ---------- EDIT-ME DATA ----------
    $slides = [
        // 'focus' is optional: 'horizontal% vertical%'. Lower vertical % shows more of the top of the photo.
        ['image' => 'images/hero.jpeg', 'alt' => 'EMAB Electrical team ready to serve customers', 'focus' => '50% 30%',
         'heading' => 'Electrical work you can trust',
         'description' => 'Our experienced team delivers safe, dependable electrical services for homes and businesses.'],
        ['image' => 'images/installer.jpg', 'alt' => 'Decorative ceiling lighting installation',
         'heading' => 'Light up your space',
         'description' => 'From modern lighting installations to upgrades, we help make every space work beautifully.'],
        ['image' => 'images/3.jpg', 'alt' => 'Electrical installation completed by EMAB Electrical',
         'heading' => 'Solutions for every connection',
         'description' => 'Wiring, solar, lighting, security systems and repairs for homes, shops and businesses.'],
    ];

    $reasons = [
        ['bi-person-check', 'Qualified technicians', 'Fully trained electricians dedicated to safe, professional workmanship.'],
        ['bi-clock', 'Quick response', 'Prompt attention for scheduled jobs and urgent electrical service needs.'],
        ['bi-patch-check', 'Quality materials', 'Durable, compliant components and materials for every installation.'],
        ['bi-receipt', 'Honest pricing', 'Clear, transparent quotations with no hidden or unexpected costs.'],
    ];

    $areas = config('emab.areas');

    // Numbers come from Settings (keys below). Empty ones are hidden. No invented figures.
    $stats = array_filter([
        ['value' => setting('stat_projects'), 'label' => 'Projects completed'],
        ['value' => setting('stat_years'),    'label' => 'Years of experience'],
        ['value' => setting('stat_clients'),  'label' => 'Happy clients'],
        ['value' => setting('stat_areas'),    'label' => 'Towns served'],
    ], fn ($s) => filled($s['value']));

    $statColumns = [1 => 'lg:grid-cols-1', 2 => 'lg:grid-cols-2', 3 => 'lg:grid-cols-3', 4 => 'lg:grid-cols-4'];

    $steps = [
        ['image' => 'images/step1.png',         'title' => 'Send your request',   'text' => 'Tell us what you need, where the job is and how to reach you. Add a photo if it helps.'],
        ['image' => 'images/step2.png','title' => 'Get your quotation',  'text' => 'We review your request and send you a clear written quotation as a PDF.'],
        ['image' => 'images/step3.png',        'title' => 'We do the work',      'text' => 'Once you approve, our technicians carry out the job safely and hand it over to you.'],
    ];
@endphp

{{-- ============ 1. HERO (carousel + quote form card) ============ --}}
<section data-carousel aria-roledescription="carousel" aria-label="Featured electrical work"
         class="relative h-[85vh] min-h-[38rem] w-full overflow-hidden bg-brand-navy">

    @foreach ($slides as $i => $slide)
        @php $headingTag = $i === 0 ? 'h1' : 'h2'; @endphp
        <div data-slide aria-hidden="{{ $i === 0 ? 'false' : 'true' }}"
             class="absolute inset-0 transition-opacity duration-700 ease-in-out {{ $i === 0 ? 'pointer-events-auto opacity-100' : 'pointer-events-none opacity-0' }}">
            @if ($img($slide['image']))
                {{-- Fills the full width. 'focus' (see $slides) chooses which part of the photo stays in view --}}
                <img src="{{ $img($slide['image']) }}" alt="{{ $slide['alt'] }}"
                     class="h-full w-full object-cover"
                     style="object-position: {{ $slide['focus'] ?? 'center' }};"
                     @if ($i === 0) fetchpriority="high" @else loading="lazy" @endif>
            @else
                <div role="img" aria-label="{{ $slide['alt'] }}" class="h-full w-full bg-brand-navy"></div>
            @endif
            <div class="absolute inset-0 bg-black/45"></div>

            <div class="absolute inset-0 flex items-center">
                <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="max-w-2xl">
                        <p class="mb-4 text-sm font-semibold uppercase tracking-widest text-white/90">
                            Delivering results, Exceeding Expectations
                        </p>
                        <{{ $headingTag }} class="text-4xl font-bold leading-tight text-white sm:text-5xl lg:text-6xl">
                            {{ $slide['heading'] }}
                        </{{ $headingTag }}>
                        <p class="mt-5 max-w-xl text-lg leading-relaxed text-white/90">
                            {{ $slide['description'] }}
                        </p>
                        <div class="mt-8">
                            <a href="{{ url('/quote') }}"
                               class="inline-flex items-center gap-3 rounded-full bg-brand-blue py-2 pl-7 pr-2 text-base font-semibold text-white transition-colors hover:bg-brand-blue-hover">
                                Get a Free Quote
                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-red text-white"><i class="bi bi-arrow-right"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Quote form card (xl screens only). Posts to the same /quote store route. --}}
    <div class="pointer-events-none absolute inset-0 z-10 hidden items-center xl:flex">
        <div class="mx-auto flex w-full max-w-7xl justify-end px-4 sm:px-6 lg:px-8">
            <div class="pointer-events-auto w-full max-w-sm rounded-2xl bg-brand-navy/95 p-6 text-white shadow-2xl">
                @if (session('success'))
                    <div class="py-8 text-center" role="status">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-brand-red text-2xl"><i class="bi bi-check-lg"></i></div>
                        <h2 class="text-lg font-semibold">Request sent</h2>
                        <p class="mt-2 text-sm text-slate-200">Thank you. We will contact you shortly with your quotation.</p>
                    </div>
                @else
                    <h2 class="mb-4 text-center text-lg font-semibold">Request a free quote</h2>

                    @if ($errors->any())
                        <div class="mb-4 rounded-md bg-brand-red/30 p-3 text-sm" role="alert">
                            <ul class="list-disc space-y-0.5 pl-4">
                                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ url('/quote') }}" class="space-y-3">
                        @csrf
                        <div class="hidden" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>

                        <div>
                            <label for="hero-name" class="mb-1 block text-sm font-medium">Name</label>
                            <input id="hero-name" type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full rounded-lg border-0 bg-white px-3 py-2 text-sm text-slate-900 focus:ring-2 focus:ring-brand-red">
                        </div>
                        <div>
                            <label for="hero-phone" class="mb-1 block text-sm font-medium">Phone</label>
                            <input id="hero-phone" type="tel" name="phone" value="{{ old('phone') }}" required
                                   class="w-full rounded-lg border-0 bg-white px-3 py-2 text-sm text-slate-900 focus:ring-2 focus:ring-brand-red">
                        </div>
                        <div>
                            <label for="hero-service" class="mb-1 block text-sm font-medium">Service</label>
                            <select id="hero-service" name="service_id"
                                    class="w-full rounded-lg border-0 bg-white px-3 py-2 text-sm text-slate-900 focus:ring-2 focus:ring-brand-red">
                                <option value="">Not sure yet</option>
                                @foreach ($categories as $category)
                                    @if ($category->services->isNotEmpty())
                                        <optgroup label="{{ $category->name }}">
                                            @foreach ($category->services as $service)
                                                <option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>{{ $service->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="hero-location" class="mb-1 block text-sm font-medium">Job location</label>
                            <input id="hero-location" type="text" name="location" value="{{ old('location') }}" required
                                   class="w-full rounded-lg border-0 bg-white px-3 py-2 text-sm text-slate-900 focus:ring-2 focus:ring-brand-red">
                        </div>
                        <div>
                            <label for="hero-description" class="mb-1 block text-sm font-medium">What do you need done?</label>
                            <textarea id="hero-description" name="description" rows="3" required
                                      class="w-full rounded-lg border-0 bg-white px-3 py-2 text-sm text-slate-900 focus:ring-2 focus:ring-brand-red">{{ old('description') }}</textarea>
                        </div>
                        <button type="submit"
                                class="w-full rounded-full bg-brand-red px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-red-hover">
                            Send request
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="absolute bottom-5 left-1/2 z-20 flex -translate-x-1/2 gap-2">
        @foreach ($slides as $i => $slide)
            <button type="button" data-dot aria-label="Go to slide {{ $i + 1 }}"
                    class="h-2.5 w-2.5 rounded-full transition-colors {{ $i === 0 ? 'bg-white' : 'bg-white/40' }}"></button>
        @endforeach
    </div>
</section>

{{-- ============ 2. FEATURES + CALL PANEL ============ --}}
<section class="bg-white py-16 lg:py-24">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
        <div class="space-y-4">
            @foreach ($reasons as [$icon, $title, $text])
                <div class="flex items-start gap-5 rounded-2xl bg-white p-5 shadow-lg shadow-slate-200/70">
                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-brand-navy text-2xl text-white">
                        <i class="bi {{ $icon }}"></i>
                    </span>
                    <div>
                        <h3 class="text-xl font-bold text-brand-navy">{{ $title }}</h3>
                        <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $text }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex flex-col items-center justify-center rounded-3xl bg-brand-navy px-6 py-14 text-center text-white">
            <p class="text-2xl font-light">Or give us a call</p>
            <a href="{{ tel('phone') }}" class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">{{ setting('phone') }}</a>
            <a href="{{ url('/quote') }}"
               class="mt-8 inline-flex items-center gap-3 rounded-full bg-brand-red py-2 pl-7 pr-2 text-sm font-semibold text-white transition-colors hover:bg-brand-red-hover">
                Request a Quote
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-brand-navy"><i class="bi bi-arrow-right"></i></span>
            </a>
        </div>
    </div>
</section>

{{-- ============ 3. ABOUT TEASER ============ --}}
<section class="bg-slate-50 py-16 lg:py-24">
    <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
        <div class="relative mx-auto w-full max-w-lg">
            @if ($img('images/hero.jpg'))
                <img src="{{ $img('images/hero.jpg') }}" alt="EMAB Electrical at work" loading="lazy"
                     class="aspect-[4/5] w-full rounded-2xl object-cover shadow-xl">
            @else
                <div class="aspect-[4/5] w-full rounded-2xl bg-brand-navy"></div>
            @endif

            {{-- Circular badge with the tagline written around a small icon --}}
            <div class="absolute -right-4 -top-8 flex h-36 w-36 items-center justify-center rounded-full border-2 border-brand-navy bg-white shadow-lg sm:-right-8 sm:h-44 sm:w-44">
                <svg viewBox="0 0 200 200" class="absolute inset-0 h-full w-full motion-safe:animate-[spin_30s_linear_infinite]" aria-hidden="true">
                    <defs><path id="badgeCircle" d="M100,100 m-72,0 a72,72 0 1,1 144,0 a72,72 0 1,1 -144,0"/></defs>
                    <text font-size="15" font-weight="700" fill="var(--color-brand-blue)" letter-spacing="1.2">
                        <textPath href="#badgeCircle">Delivering results, Exceeding Expectations &#8226;</textPath>
                    </text>
                </svg>
                <i class="bi bi-lightning-charge-fill relative text-4xl text-brand-red"></i>
            </div>
        </div>

        <div>
            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-1.5 text-sm font-semibold text-brand-navy">
                <span class="h-2 w-2 rounded-full bg-brand-red"></span> About Us
            </span>
            <h2 class="mt-5 text-3xl font-bold leading-tight text-brand-navy sm:text-4xl lg:text-5xl">
                Safe, reliable electrical work for homes and businesses
            </h2>
            <p class="mt-5 leading-relaxed text-slate-600">
                {{ setting('company_name') }} is based in {{ config('emab.base') }} and serves {{ config('emab.area_text') }}.
                We are committed to safety, honest pricing and building long-term relationships with every client.
            </p>
            <p class="mt-4 leading-relaxed text-slate-600">
                From wiring and lighting to solar and security systems, our qualified technicians handle every job with care and precision, for homes, shops and businesses.
            </p>
            <a href="{{ url('/about') }}"
               class="mt-8 inline-flex items-center gap-3 rounded-full border border-brand-navy py-2 pl-7 pr-2 text-sm font-semibold text-brand-navy transition-colors hover:bg-white">
                Read More
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-red text-white"><i class="bi bi-arrow-right"></i></span>
            </a>
        </div>
    </div>
</section>

{{-- ============ 4. SERVICES (image cards from DB) ============ --}}
<section id="services" class="bg-brand-tint py-16 lg:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl text-center">
            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-1.5 text-sm font-semibold text-brand-navy">
                <span class="h-2 w-2 rounded-full bg-brand-red"></span> Our Services
            </span>
            <h2 class="mt-5 text-3xl font-bold text-brand-navy sm:text-4xl lg:text-5xl">We Deliver Safe, Reliable Electrical Work</h2>
            <p class="mt-4 leading-relaxed text-slate-600">
                From a single socket to a full commercial installation, our qualified technicians keep your property powered, lit and secure.
            </p>
        </div>

        <div class="mt-14 flex flex-wrap justify-center gap-6">
            @foreach ($categories as $category)
                @php $service = $category->services->first(); @endphp
                <a href="{{ url('/services?category=' . $category->slug) }}"
                   class="group relative block h-80 w-full overflow-hidden rounded-2xl bg-brand-navy sm:w-[calc(50%_-_0.75rem)] lg:w-[calc(33.333%_-_1rem)]">
                    @if ($service?->image_url)
                        <img src="{{ $service->image_url }}" alt="{{ $category->name }}" loading="lazy"
                             class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-navy/95 via-brand-navy/55 to-brand-navy/25"></div>
                    <span class="absolute left-5 top-5 flex h-14 w-14 items-center justify-center rounded-xl bg-white/15 text-2xl text-white backdrop-blur">
                        <i class="bi {{ $category->icon ?? 'bi-lightning-charge' }}"></i>
                    </span>
                    <div class="absolute inset-x-5 bottom-5">
                        <h3 class="text-2xl font-bold text-white">{{ $category->name }}</h3>
                        <p class="mt-1 line-clamp-2 text-sm text-white/80">{{ $category->description }}</p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-12 text-center">
            <a href="{{ url('/services') }}"
               class="inline-flex items-center gap-3 rounded-full bg-brand-navy py-2 pl-7 pr-2 text-sm font-semibold text-white transition-opacity hover:opacity-90">
                View all services
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-red text-white"><i class="bi bi-arrow-right"></i></span>
            </a>
        </div>
    </div>
</section>

{{-- ============ 5. AREAS WE SERVE ============ --}}
<section class="bg-brand-navy py-16 text-white lg:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <p class="text-sm font-medium text-white/80">Where we work</p>
            <h2 class="mt-3 text-3xl font-bold sm:text-4xl lg:text-5xl">Areas We Serve</h2>
        </div>

        <div class="mt-12 flex snap-x snap-mandatory gap-4 overflow-x-auto pb-4 lg:justify-center">
            @foreach ($areas as $area)
                <div class="relative flex h-[26rem] w-56 shrink-0 snap-start items-end justify-center overflow-hidden rounded-2xl bg-brand-navy">
                    @if ($img($area['image']))
                        <img src="{{ $img($area['image']) }}" alt="{{ $area['name'] }}" loading="lazy" class="absolute inset-0 h-full w-full object-cover">
                    @endif
                    <div class="absolute inset-0 bg-black/60"></div>
                    @if ($loop->first)
                        <span class="absolute left-4 top-4 rounded-full bg-brand-red px-3 py-1 text-xs font-semibold text-white">
                            Our base
                        </span>
                    @endif
                    <div class="absolute inset-x-0 bottom-6 flex justify-center">
                        <span class="text-3xl font-bold text-white [writing-mode:vertical-rl] rotate-180">{{ $area['name'] }}</span>
                    </div>
                </div>
            @endforeach

            <div class="flex h-[26rem] w-28 shrink-0 snap-start items-center justify-center overflow-hidden rounded-2xl bg-brand-red px-3 text-center">
                <span class="text-xl font-bold leading-tight text-white [writing-mode:vertical-rl] rotate-180">
                    and surrounding areas
                </span>
            </div>
        </div>

        <p class="mt-6 text-center text-base text-white/90">
            We serve homes, shops and businesses in {{ config('emab.area_text') }}.
        </p>
    </div>
</section>

{{-- ============ 6. NUMBERS STRIP (hidden when no settings are filled) ============ --}}
@if (count($stats))
    <section class="bg-white py-12 lg:py-16">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-y-8 px-4 sm:px-6 lg:px-8 {{ $statColumns[min(count($stats), 4)] }} lg:divide-x lg:divide-slate-200">
            @foreach ($stats as $stat)
                <div class="text-center">
                    <p class="text-4xl font-bold text-brand-navy sm:text-5xl">{{ $stat['value'] }}</p>
                    <p class="mt-2 text-sm font-medium uppercase tracking-wide text-slate-500">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </section>
@endif

{{-- ============ 7. HOW IT WORKS ============ --}}
<section class="bg-slate-50 py-16 lg:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl text-center">
            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-1.5 text-sm font-semibold text-brand-navy">
                <span class="h-2 w-2 rounded-full bg-brand-red"></span> Our Work Process
            </span>
            <h2 class="mt-5 text-3xl font-bold text-brand-navy sm:text-4xl lg:text-5xl">We always follow the best way of working</h2>
        </div>

        <ol class="mt-14 grid grid-cols-1 gap-12 md:grid-cols-3">
            @foreach ($steps as $n => $step)
                <li class="flex flex-col items-center text-center">
                    <div class="relative">
                        @if ($img($step['image']))
                            <img src="{{ $img($step['image']) }}" alt="" loading="lazy" class="h-44 w-44 rounded-full object-cover shadow-lg">
                        @else
                            <div class="h-44 w-44 rounded-full bg-brand-navy"></div>
                        @endif
                        <span class="absolute -right-3 bottom-2 flex h-16 w-16 items-center justify-center rounded-full bg-brand-red text-2xl font-bold text-white shadow">
                            0{{ $n + 1 }}
                        </span>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-brand-navy">Step {{ $n + 1 }}: {{ $step['title'] }}</h3>
                    <p class="mt-2 max-w-xs text-sm leading-relaxed text-slate-600">{{ $step['text'] }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- ============ 8. SCROLLING SERVICE STRIP + PHOTO ============ --}}
<section class="overflow-hidden bg-white" aria-hidden="true">
    <style>
        @keyframes emab-marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }
        .emab-marquee { animation: emab-marquee 35s linear infinite; }
        @media (prefers-reduced-motion: reduce) { .emab-marquee { animation: none; } }
    </style>
    <div class="border-t-4 border-brand-navy py-4">
        <div class="emab-marquee flex w-max items-center whitespace-nowrap">
            @for ($loop2 = 0; $loop2 < 2; $loop2++)
                @foreach ($categories as $category)
                    <span class="px-6 text-4xl font-bold sm:text-5xl"
                          style="color: transparent; -webkit-text-stroke: 1.5px var(--color-brand-navy);">{{ $category->name }}</span>
                    <span class="text-4xl text-brand-red sm:text-5xl">&#10033;</span>
                @endforeach
            @endfor
        </div>
    </div>
    @if ($img('images/hero.jpg'))
        <img src="{{ $img('images/hero.jpg') }}" alt="" loading="lazy" class="h-64 w-full object-cover sm:h-80">
    @else
        <div class="h-64 w-full bg-brand-navy sm:h-80"></div>
    @endif
</section>

{{-- ============ 9. RECENT PROJECTS ============ --}}
@if ($projects->isNotEmpty())
    <section id="projects" class="bg-slate-50 py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-1.5 text-sm font-semibold text-brand-navy">
                    <span class="h-2 w-2 rounded-full bg-brand-red"></span> Our Work
                </span>
                <h2 class="mt-5 text-3xl font-bold text-brand-navy sm:text-4xl lg:text-5xl">Recent Projects</h2>
            </div>

            <div class="mt-14 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($projects as $project)
                    <div class="flex flex-col overflow-hidden rounded-2xl bg-white shadow-lg shadow-slate-200/70">
                        <div class="h-48 w-full overflow-hidden bg-slate-100">
                            @if ($project->coverImage)
                                <img src="{{ $project->cover_url }}" alt="{{ $project->title }}" loading="lazy" class="h-full w-full object-cover">
                            @else
                                <div role="img" aria-label="{{ $project->title }}"
                                     class="flex h-full w-full items-center justify-center bg-slate-200 px-4 text-center text-xs text-slate-500">
                                    Project photo: {{ $project->title }}
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col p-5">
                            @if ($project->service?->category)
                                <span class="mb-2 inline-block w-fit rounded-full bg-brand-red/10 px-3 py-1 text-xs font-semibold text-brand-red">
                                    {{ $project->service->category->name }}
                                </span>
                            @endif
                            <h3 class="mb-1 text-base font-bold text-brand-navy">{{ $project->title }}</h3>
                            <div class="mb-3 flex items-center justify-between gap-2 text-xs text-slate-500">
                                <span>{{ $project->location }}</span>
                                @if ($project->completed_on)<span>Completed {{ $project->completed_on->format('M Y') }}</span>@endif
                            </div>
                            <p class="mt-auto text-sm text-slate-600">{{ str($project->description)->limit(100) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-12 text-center">
                <a href="{{ url('/projects') }}"
                   class="inline-flex items-center gap-3 rounded-full bg-brand-navy py-2 pl-7 pr-2 text-sm font-semibold text-white transition-opacity hover:opacity-90">
                    View all projects
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-red text-white"><i class="bi bi-arrow-right"></i></span>
                </a>
            </div>
        </div>
    </section>
@endif

{{-- ============ 10. TESTIMONIALS ============ --}}
@if ($testimonials->isNotEmpty())
    <section id="testimonials" class="bg-white py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-1.5 text-sm font-semibold text-brand-navy">
                    <span class="h-2 w-2 rounded-full bg-brand-red"></span> Testimonials
                </span>
                <h2 class="mt-5 text-3xl font-bold text-brand-navy sm:text-4xl lg:text-5xl">What Our Clients Say</h2>
            </div>

            <div class="mt-14 grid grid-cols-1 gap-6 md:grid-cols-3">
                @foreach ($testimonials as $testimonial)
                    <div class="flex flex-col justify-between rounded-2xl bg-slate-50 p-7 shadow-lg shadow-slate-200/70">
                        <div>
                            <div class="mb-4 flex items-center gap-1 text-amber-400" aria-label="{{ $testimonial->rating }} out of 5 stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= $testimonial->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                @endfor
                            </div>
                            <p class="mb-6 text-sm leading-relaxed text-slate-700">&ldquo;{{ $testimonial->message }}&rdquo;</p>
                        </div>
                        <span class="text-base font-bold text-brand-navy">{{ $testimonial->client_name }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ============ 11. CLOSING BANNER ============ --}}
@include('partials.cta', ['heading' => 'Ready for safe, reliable electrical work?'])

{{-- ============ 12. SCROLL-TO-TOP BUTTON (sits above the WhatsApp button) ============ --}}
<button type="button" id="scrollTopBtn" aria-label="Back to top"
        class="invisible fixed bottom-24 right-6 z-40 flex h-11 w-11 items-center justify-center rounded-lg border border-slate-300 bg-white text-xl text-brand-navy opacity-0 shadow-md transition-opacity">
    <i class="bi bi-arrow-up"></i>
</button>
<script>
    (function () {
        // ---- Hero carousel ----
        document.querySelectorAll('[data-carousel]').forEach(function (root) {
            var slides = [].slice.call(root.querySelectorAll('[data-slide]'));
            var dots = [].slice.call(root.querySelectorAll('[data-dot]'));
            if (slides.length < 2) return;

            var current = 0, timer = null, delay = 6000;

            function show(n) {
                current = (n + slides.length) % slides.length;
                slides.forEach(function (slide, k) {
                    var on = k === current;
                    slide.setAttribute('aria-hidden', on ? 'false' : 'true');
                    slide.classList.toggle('opacity-100', on);
                    slide.classList.toggle('opacity-0', !on);
                    slide.classList.toggle('pointer-events-auto', on);
                    slide.classList.toggle('pointer-events-none', !on);
                });
                dots.forEach(function (dot, k) {
                    dot.classList.toggle('bg-white', k === current);
                    dot.classList.toggle('bg-white/40', k !== current);
                });
            }

            function stop() { clearInterval(timer); }
            function start() {
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                stop();
                timer = setInterval(function () { show(current + 1); }, delay);
            }

            dots.forEach(function (dot, k) {
                dot.addEventListener('click', function () { show(k); start(); });
            });
            root.addEventListener('mouseenter', stop);
            root.addEventListener('mouseleave', start);

            show(0);
            start();
        });

        // ---- Scroll-to-top button ----
        var btn = document.getElementById('scrollTopBtn');
        if (btn) {
            var toggle = function () {
                var show = window.scrollY > 500;
                btn.classList.toggle('invisible', !show);
                btn.classList.toggle('opacity-0', !show);
            };
            window.addEventListener('scroll', toggle, { passive: true });
            btn.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
            toggle();
        }
    })();
</script>

@endsection