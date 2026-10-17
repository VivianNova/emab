@extends('layouts.app')

@section('title', $service->name . ' | ' . setting('company_name'))
@section('description', str(strip_tags((string) $service->description))->limit(155))

@section('content')

@php
    $input = 'w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm '
           . 'placeholder:text-slate-400 focus:border-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue/30';
    $label = 'mb-1 block text-xs font-semibold text-brand-navy';

    // The page body is the service description. A blank line starts a new paragraph.
    $paragraphs = preg_split('/\R{2,}/', trim((string) $service->description), -1, PREG_SPLIT_NO_EMPTY);

    $categoryIcon = $service->category?->icon ?? 'bi-lightning-charge';

    $phone    = setting('phone');
    $whatsapp = setting('whatsapp');
@endphp

{{-- 1. BREADCRUMB --}}
<div class="border-b border-slate-200 bg-white py-3">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <nav aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
                <li><a href="{{ url('/') }}" class="transition-colors hover:text-brand-blue">Home</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('services') }}" class="transition-colors hover:text-brand-blue">Services</a></li>
                @if ($service->category)
                    <li aria-hidden="true">/</li>
                    <li>
                        <a href="{{ route('services', ['category' => $service->category->slug]) }}"
                           class="transition-colors hover:text-brand-blue">{{ $service->category->name }}</a>
                    </li>
                @endif
                <li aria-hidden="true">/</li>
                <li aria-current="page" class="font-semibold text-brand-navy">{{ $service->name }}</li>
            </ol>
        </nav>
    </div>
</div>

{{-- 2. MAIN CONTENT --}}
<section class="bg-slate-50 py-10 lg:py-14">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 items-start gap-10 lg:grid-cols-12">

            {{-- Left column --}}
            <div class="flex flex-col gap-10 lg:col-span-8">

                <div class="flex flex-col gap-2">
                    @if ($service->category)
                        <span class="inline-flex items-center gap-1.5 self-start rounded-full bg-brand-tint px-3 py-1 text-xs font-semibold text-brand-navy">
                            <i class="bi {{ $categoryIcon }}"></i> {{ $service->category->name }}
                        </span>
                    @endif
                    <h1 class="mt-1 text-3xl font-semibold tracking-tight text-brand-navy sm:text-4xl">{{ $service->name }}</h1>
                </div>

                @if ($service->image_url)
                    <img src="{{ $service->image_url }}" alt="{{ $service->name }}"
                         class="aspect-[16/9] w-full rounded-xl object-cover">
                @endif

                {{-- Description --}}
                <section class="flex flex-col gap-4">
                    <h2 class="text-xl font-semibold text-brand-navy sm:text-2xl">About this service</h2>
                    <div class="flex flex-col gap-4 text-base leading-relaxed text-slate-600">
                        @foreach ($paragraphs as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                </section>

                {{-- Related services --}}
                @if ($related->isNotEmpty())
                    <section class="flex flex-col gap-4">
                        <h2 class="text-xl font-semibold text-brand-navy sm:text-2xl">Related services</h2>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            @foreach ($related as $item)
                                <div class="flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-brand-blue hover:shadow-sm">
                                    @if ($item->image_url)
                                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                                             loading="lazy" class="aspect-video w-full object-cover">
                                    @else
                                        <div class="flex aspect-video w-full items-center justify-center bg-brand-tint text-4xl text-brand-sky">
                                            <i class="bi {{ $item->category?->icon ?? 'bi-lightning-charge' }}"></i>
                                        </div>
                                    @endif
                                    <div class="flex flex-1 flex-col p-5">
                                        <h3 class="text-base font-semibold text-brand-navy">{{ $item->name }}</h3>
                                        <p class="text-sm leading-relaxed text-slate-600">
                                            {{ str(strip_tags((string) $item->description))->limit(90) }}
                                        </p>
                                        <a href="{{ route('services.show', $item->slug) }}"
                                           class="mt-auto inline-flex items-center gap-1 pt-4 text-sm font-semibold text-brand-blue hover:underline">
                                            View details <i class="bi bi-chevron-right text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            {{-- Sidebar --}}
            <aside class="flex flex-col gap-4 lg:sticky lg:top-24 lg:col-span-4">

                {{-- Quote form: same fields and rules as the main quote page --}}
                <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-1 text-lg font-bold text-brand-navy">Request a quote for this service</h2>
                    <p class="mb-5 text-sm text-slate-600">
                        Fill in your details and we will get back to you with an initial estimate.
                    </p>

                    <form method="POST" action="{{ route('quote.store') }}" enctype="multipart/form-data" novalidate
                          class="flex flex-col gap-3">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ $service->id }}">

                        {{-- Honeypot --}}
                        <div aria-hidden="true" style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;">
                            <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                        </div>

                        <div>
                            <label for="q-name" class="{{ $label }}">Full name</label>
                            <input type="text" id="q-name" name="name" value="{{ old('name') }}" required
                                   placeholder="e.g. Jane Wanjiku" class="{{ $input }}">
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="q-phone" class="{{ $label }}">Phone</label>
                            <input type="tel" id="q-phone" name="phone" value="{{ old('phone') }}" required
                                   inputmode="tel" placeholder="e.g. 0700 000 000" class="{{ $input }}">
                            @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="q-email" class="{{ $label }}">Email <span class="font-normal text-slate-500">(optional)</span></label>
                            <input type="email" id="q-email" name="email" value="{{ old('email') }}"
                                   placeholder="name@example.com" class="{{ $input }}">
                            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="q-location" class="{{ $label }}">Job location</label>
                            <input type="text" id="q-location" name="location" value="{{ old('location') }}" required
                                   placeholder="e.g. {{ config('emab.base') }}, {{ config('emab.areas.1.name') }} or {{ config('emab.areas.2.name') }}" class="{{ $input }}">
                            @error('location') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="q-description" class="{{ $label }}">Description</label>
                            <textarea id="q-description" name="description" rows="3" required
                                      placeholder="Tell us about your property and what you need..."
                                      class="{{ $input }} resize-none">{{ old('description') }}</textarea>
                            @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="q-photo" class="{{ $label }}">
                                Photos or plans <span class="font-normal text-slate-500">(optional, JPG, PNG or PDF, max 5 MB)</span>
                            </label>
                            <input type="file" id="q-photo" name="photo" accept=".jpg,.jpeg,.png,.pdf"
                                   class="block w-full text-sm text-slate-600 file:mr-3 file:cursor-pointer file:rounded-md file:border-0 file:bg-brand-tint file:px-3 file:py-2 file:text-sm file:font-semibold file:text-brand-navy hover:file:bg-brand-tint">
                            @error('photo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <div class="flex items-start gap-2 pt-1">
                                <input type="checkbox" id="q-consent" name="privacy_consent" value="1" @checked(old('privacy_consent')) required
                                       class="mt-0.5 h-4 w-4 cursor-pointer rounded border-slate-300 text-brand-blue focus:ring-brand-blue">
                                <label for="q-consent" class="cursor-pointer text-sm leading-tight text-slate-600">
                                    I agree to be contacted.
                                    <a href="{{ url('/privacy-policy') }}" class="text-brand-blue hover:underline">Privacy Policy</a>
                                </label>
                            </div>
                            @error('privacy_consent') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit"
                                class="mt-1 inline-flex h-11 w-full items-center justify-center gap-1.5 rounded-md bg-brand-blue text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-blue-hover">
                            Request Quote <i class="bi bi-arrow-right"></i>
                        </button>
                    </form>
                </div>

                {{-- Prefer to talk? (numbers come from settings) --}}
                @if ($phone || $whatsapp)
                    <div class="flex flex-col gap-3 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <div>
                            <h3 class="text-lg font-bold text-brand-navy">Prefer to talk?</h3>
                            <p class="text-sm text-slate-600">Speak directly with our team for quick guidance or urgent inquiries.</p>
                        </div>
                        <div class="flex flex-col gap-2">
                            @if ($phone)
                                <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}"
                                   class="flex h-11 w-full items-center justify-center gap-2 rounded-md bg-brand-red text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-red-hover">
                                    <i class="bi bi-telephone"></i> Call {{ $phone }}
                                </a>
                            @endif
                            @if ($whatsapp)
                                <a href="https://wa.me/{{ preg_replace('/\D/', '', $whatsapp) }}" target="_blank" rel="noopener noreferrer"
                                   class="flex h-11 w-full items-center justify-center gap-2 rounded-md bg-brand-whatsapp text-sm font-semibold text-brand-navy shadow-sm transition-colors hover:brightness-105">
                                    <i class="bi bi-whatsapp"></i> Chat on WhatsApp
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</section>

{{-- Success message after a quote is sent from this page --}}
@include('partials.quote-success')

@endsection