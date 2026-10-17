@extends('layouts.app')

@section('title', $project->title . ' | ' . setting('company_name'))
@section('description', (string) str($project->description)->limit(150))

@section('content')

@php
    $category = $project->service?->category;
    $images = $project->images;
    $paragraphs = array_filter(preg_split('/\R{2,}/', trim((string) $project->description)));
    $quoteUrl = url('/quote') . ($project->service ? '?service=' . $project->service->slug : '');
    $whatsappDigits = preg_replace('/\D/', '', (string) setting('whatsapp'));
@endphp

{{-- ============ 1. HEADER ============ --}}
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
                <li><a href="{{ route('projects') }}" class="transition-colors hover:text-brand-blue">Projects</a></li>
                <li aria-hidden="true"><i class="bi bi-chevron-right text-xs"></i></li>
                <li aria-current="page" class="font-semibold text-brand-navy">{{ $project->title }}</li>
            </ol>
        </nav>

        @if ($category)
            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-1.5 text-sm font-semibold text-brand-navy">
                <span class="h-2 w-2 rounded-full bg-brand-red"></span> {{ $category->name }}
            </span>
        @endif
        <h1 class="mt-5 max-w-3xl text-4xl font-bold leading-tight text-brand-navy sm:text-5xl">{{ $project->title }}</h1>

        <div class="mt-4 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-slate-500">
            @if ($project->location)
                <span class="inline-flex items-center gap-1.5"><i class="bi bi-geo-alt"></i> {{ $project->location }}</span>
            @endif
            @if ($project->completed_on)
                <span class="inline-flex items-center gap-1.5"><i class="bi bi-calendar3"></i> Completed {{ $project->completed_on->format('M Y') }}</span>
            @endif
        </div>
    </div>
</section>

{{-- ============ 2. GALLERY, OVERVIEW AND SIDEBAR ============ --}}
<section class="bg-slate-50 py-12 lg:py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-12">

            <div class="flex flex-col gap-8 lg:col-span-8">

                {{-- Gallery (data-gallery attributes and the ring classes are used by the gallery script: keep them) --}}
                <div data-gallery class="rounded-2xl bg-white p-4 shadow-lg shadow-slate-200/70">
                    @if ($images->isNotEmpty())
                        <div class="aspect-video w-full overflow-hidden rounded-xl bg-slate-100">
                            <img data-gallery-main src="{{ asset('storage/' . $images->first()->image_path) }}"
                                 alt="{{ $project->title }}" class="h-full w-full object-cover">
                        </div>

                        @if ($images->count() > 1)
                            <div class="mt-4 grid grid-cols-4 gap-2">
                                @foreach ($images as $i => $image)
                                    <button type="button" data-gallery-thumb
                                            data-src="{{ asset('storage/' . $image->image_path) }}"
                                            data-alt="{{ $project->title }} photo {{ $i + 1 }}"
                                            aria-label="Show photo {{ $i + 1 }}"
                                            @class([
                                                'aspect-[4/3] overflow-hidden rounded-lg bg-slate-100 p-0.5 transition-all focus:outline-none',
                                                'ring-2 ring-brand-blue' => $i === 0,
                                                'hover:ring-2 hover:ring-brand-sky' => $i !== 0,
                                            ])>
                                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="" loading="lazy"
                                             class="h-full w-full rounded object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div role="img" aria-label="{{ $project->title }}"
                             class="flex aspect-video w-full items-center justify-center rounded-xl bg-brand-navy text-7xl text-white/20">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                    @endif
                </div>

                {{-- Overview --}}
                @if ($paragraphs)
                    <div class="rounded-2xl bg-white p-6 shadow-lg shadow-slate-200/70 sm:p-8">
                        <h2 class="mb-4 text-2xl font-bold text-brand-navy">Project overview</h2>
                        <div class="space-y-4 leading-relaxed text-slate-600">
                            @foreach ($paragraphs as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <aside class="lg:col-span-4 lg:sticky lg:top-24 lg:self-start">
                <div class="flex flex-col gap-3 rounded-2xl bg-brand-navy p-6 text-white shadow-lg">
                    <div class="mb-2">
                        <h2 class="text-xl font-bold">Need something similar?</h2>
                        <p class="mt-1 text-sm leading-relaxed text-white/80">
                            Tell us about your property and we will get back to you with a quotation.
                        </p>
                    </div>

                    <a href="{{ $quoteUrl }}"
                       class="inline-flex w-full items-center justify-between gap-3 rounded-full bg-white py-2 pl-6 pr-2 text-sm font-semibold text-brand-navy transition-opacity hover:opacity-90">
                        Request a similar project
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-red text-white"><i class="bi bi-arrow-right"></i></span>
                    </a>
                    <a href="{{ tel('phone') }}"
                       class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-brand-red px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-red-hover">
                        <i class="bi bi-telephone-fill"></i> Call {{ setting('phone') }}
                    </a>
                    @if ($whatsappDigits)
                        <a href="https://wa.me/{{ $whatsappDigits }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-brand-whatsapp px-4 py-3 text-sm font-semibold text-brand-navy transition hover:brightness-105">
                            <i class="bi bi-whatsapp"></i> Chat on WhatsApp
                        </a>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</section>

{{-- ============ 3. MORE PROJECTS ============ --}}
@if ($related->isNotEmpty())
    <section class="bg-white py-12 lg:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto mb-10 max-w-3xl text-center">
                <h2 class="text-3xl font-bold text-brand-navy sm:text-4xl">More projects</h2>
                <p class="mt-3 text-slate-600">Other recent installations in {{ config('emab.area_text') }}.</p>
            </div>

            <div class="flex flex-wrap justify-center gap-6">
                @foreach ($related as $other)
                    <div class="flex w-full md:w-[calc(50%_-_0.75rem)] lg:w-[calc(33.333%_-_1rem)] [&>*]:w-full">
                        @include('partials.project-card', ['project' => $other])
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ============ 4. CLOSING BANNER ============ --}}
@include('partials.cta', ['heading' => 'Need something similar?'])

@endsection