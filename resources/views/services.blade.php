@extends('layouts.app')

@section('title', 'Our Services | ' . setting('company_name'))
@section('description', 'Residential, commercial, solar, lighting, security, inspection and repair electrical services in ' . config('emab.area_text') . '.')

@section('content')

{{-- ============ 1. PAGE HEADER ============ --}}
<section class="bg-white pb-8 pt-10 lg:pt-14">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <nav aria-label="Breadcrumb" class="mb-5">
            <ol class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
                <li>
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-1 transition-colors hover:text-brand-blue">
                        <i class="bi bi-house"></i> Home
                    </a>
                </li>
                <li aria-hidden="true"><i class="bi bi-chevron-right text-xs"></i></li>
                <li aria-current="page" class="font-semibold text-brand-navy">Services</li>
            </ol>
        </nav>

        <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-1.5 text-sm font-semibold text-brand-navy">
            <span class="h-2 w-2 rounded-full bg-brand-red"></span> Our Services
        </span>
        <h1 class="mt-5 max-w-3xl text-4xl font-bold leading-tight text-brand-navy sm:text-5xl">
            Basic, dependable electrical services
        </h1>
        <p class="mt-4 max-w-2xl text-lg leading-relaxed text-slate-600">
            Safe electrical work for homes, shops and businesses in {{ config('emab.area_text') }}.
        </p>

        {{-- Quick jump links to each service block --}}
        <div class="mt-8 w-full overflow-x-auto pb-2">
            <ul class="flex min-w-max flex-nowrap items-center gap-2">
                @foreach ($categories as $category)
                    <li>
                        <a href="#cat-{{ $category->slug }}"
                           class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 transition-colors hover:border-brand-blue hover:text-brand-blue">
                            <i class="bi {{ $category->icon ?? 'bi-lightning-charge' }}"></i> {{ $category->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

{{-- ============ 2. SERVICE BLOCKS (one per category, alternating sides) ============ --}}
<section class="bg-white pb-16 lg:pb-24">
    <div class="mx-auto max-w-7xl space-y-10 px-4 sm:px-6 lg:px-8">
        @foreach ($categories as $i => $category)
            @php
                $flip  = $i % 2 === 1;
                $cover = $category->services->first(fn ($s) => $s->image_url)?->image_url;
                $items = $category->services->take(4);
                $more  = $category->services->count() - $items->count();
            @endphp

            <article id="cat-{{ $category->slug }}" class="grid scroll-mt-24 overflow-hidden rounded-2xl bg-brand-tint lg:grid-cols-2">

                {{-- Image side --}}
                <div class="relative min-h-[18rem] bg-brand-navy lg:min-h-[30rem] {{ $flip ? 'lg:order-2' : '' }}">
                    @if ($cover)
                        <img src="{{ $cover }}" alt="{{ $category->name }}" loading="lazy"
                             class="absolute inset-0 h-full w-full object-cover">
                    @else
                        <i class="bi {{ $category->icon ?? 'bi-lightning-charge' }} absolute inset-0 flex items-center justify-center text-9xl text-white/10"></i>
                    @endif
                    <span class="absolute left-6 top-6 flex h-16 w-16 items-center justify-center rounded-xl bg-brand-red text-3xl text-white shadow-lg">
                        <i class="bi {{ $category->icon ?? 'bi-lightning-charge' }}"></i>
                    </span>
                </div>

                {{-- Text side --}}
                <div class="flex flex-col justify-center p-8 sm:p-12 {{ $flip ? 'lg:order-1' : '' }}">
                    <h2 class="break-words text-4xl font-medium leading-[1.05] sm:text-5xl lg:text-6xl"
                        style="color: transparent; -webkit-text-stroke: 1.5px var(--color-brand-navy);">
                        {{ $category->name }}
                    </h2>

                    @if ($category->description)
                        <p class="mt-6 max-w-xl text-base leading-relaxed text-slate-600">{{ $category->description }}</p>
                    @endif

                    @if ($items->isNotEmpty())
                        <ul class="mt-5 space-y-2">
                            @foreach ($items as $service)
                                <li class="flex items-start gap-2 text-sm text-slate-700">
                                    <i class="bi bi-check-circle-fill mt-0.5 text-brand-red"></i>
                                    <a href="{{ route('services.show', $service->slug) }}" class="font-medium hover:text-brand-blue hover:underline">{{ $service->name }}</a>
                                </li>
                            @endforeach
                            @if ($more > 0)
                                <li class="pl-6 text-sm text-slate-500">and more. Ask us for what you need.</li>
                            @endif
                        </ul>
                    @endif

                    <div class="mt-8">
                        <a href="{{ route('quote') }}"
                           class="inline-flex items-center gap-3 rounded-full border border-brand-navy py-2 pl-7 pr-2 text-sm font-semibold text-brand-navy transition-colors hover:bg-white">
                            Get a Free Quote
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-red text-white"><i class="bi bi-arrow-right"></i></span>
                        </a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>

{{-- ============ 3. CLOSING BANNER ============ --}}
@include('partials.cta')

{{-- Old links like /services?category=slug still work: scroll to that block --}}
<script>
    (function () {
        var slug = new URLSearchParams(window.location.search).get('category');
        if (!slug) return;
        var el = document.getElementById('cat-' + slug);
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    })();
</script>

@endsection