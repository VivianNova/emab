@extends('layouts.app')

@section('title', 'Request a Quote | ' . setting('company_name'))
@section('description', 'Request a free quote for wiring, solar, lighting, security systems or repairs from ' . setting('company_name') . '.')

@section('content')

@php
    // Shared input styling for the form fields
    $input = 'w-full rounded-md border border-slate-300 bg-white px-4 py-2.5 text-base text-slate-900 shadow-sm '
           . 'placeholder:text-slate-400 focus:border-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue/30';

    // "What happens next" is static text (per the project plan)
    $steps = [
        ['We review your request', 'Our team looks at what you need and where.'],
        ['We contact you', 'We call or email you to clarify details or arrange a site visit if needed.'],
        ['We send your quote', 'You receive a clear, itemised quote with no hidden charges.'],
    ];

    // Contact details always come from settings (each value exists once)
    $phone    = setting('phone');
    $whatsapp = setting('whatsapp');
    $email    = setting('email');
    $hours    = setting('working_hours');
@endphp

{{-- 1. PAGE HEADER + FORM --}}
<section class="bg-slate-50 py-12 lg:py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <nav aria-label="Breadcrumb" class="mb-3">
            <ol class="flex items-center gap-2 text-sm text-slate-500">
                <li><a href="{{ url('/') }}" class="transition-colors hover:text-brand-blue">Home</a></li>
                <li aria-hidden="true"><i class="bi bi-chevron-right text-xs"></i></li>
                <li aria-current="page" class="font-semibold text-brand-navy">Request a Quote</li>
            </ol>
        </nav>
        <h1 class="text-3xl font-semibold tracking-tight text-brand-navy sm:text-4xl">Request a Quote</h1>
        <p class="mt-2 mb-10 text-lg text-slate-600">Tell us what you need and we'll get back to you.</p>

        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">

            {{-- Form --}}
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-8 lg:col-span-8">
                <form method="POST" action="{{ route('quote.store') }}" enctype="multipart/form-data" novalidate
                      class="flex flex-col gap-6">
                    @csrf

                    {{-- Honeypot: real visitors never see or fill this --}}
                    <div aria-hidden="true" style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;">
                        <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="name" class="mb-1.5 block text-sm font-semibold text-brand-navy">
                                Full name <span class="text-red-600">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                   placeholder="e.g. Jane Wanjiku" class="{{ $input }}">
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone" class="mb-1.5 block text-sm font-semibold text-brand-navy">
                                Phone <span class="text-red-600">*</span>
                            </label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                                   inputmode="tel" placeholder="e.g. 0700 000 000" class="{{ $input }}">
                            @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="email" class="mb-1.5 block text-sm font-semibold text-brand-navy">Email (optional)</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   placeholder="name@example.com" class="{{ $input }}">
                            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="location" class="mb-1.5 block text-sm font-semibold text-brand-navy">
                                Job location <span class="text-red-600">*</span>
                            </label>
                            <input type="text" id="location" name="location" value="{{ old('location') }}" required
                                   placeholder="e.g. {{ config('emab.base') }}, {{ config('emab.areas.1.name') }} or {{ config('emab.areas.2.name') }}" class="{{ $input }}">
                            @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Service: grouped by category, preselected from /quote?service=slug --}}
                    <div>
                        <label for="service_id" class="mb-1.5 block text-sm font-semibold text-brand-navy">Service needed</label>
                        <select id="service_id" name="service_id" class="{{ $input }} cursor-pointer">
                            <option value="">Not sure / General electrical inquiry</option>
                            @foreach ($categories as $category)
                                <optgroup label="{{ $category->name }}">
                                    @foreach ($category->services as $service)
                                        <option value="{{ $service->id }}" @selected((string) $selectedServiceId === (string) $service->id)>
                                            {{ $service->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('service_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="mb-1.5 block text-sm font-semibold text-brand-navy">
                            Description <span class="text-red-600">*</span>
                        </label>
                        <textarea id="description" name="description" rows="5" required
                                  placeholder="Describe what you need, the type of property, or any problem you are facing..."
                                  class="{{ $input }} resize-y">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- File upload: the invisible input covers the box, so click and drag-and-drop both work --}}
                    <div>
                        <span class="mb-1.5 block text-sm font-semibold text-brand-navy">Attach photos or plans (optional)</span>
                        <div class="relative flex flex-col items-center justify-center gap-1 rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 p-6 text-center transition-colors hover:border-brand-blue hover:bg-brand-tint">
                            <div class="mb-1 flex h-10 w-10 items-center justify-center rounded-full bg-brand-tint text-xl text-brand-navy">
                                <i class="bi bi-cloud-upload"></i>
                            </div>
                            <p class="text-sm font-semibold text-brand-navy">Click to browse or drop a file here</p>
                            <p class="text-sm text-slate-500">JPG, PNG or PDF, max 5 MB</p>
                            <span id="file-name" class="mt-1 text-sm font-semibold text-brand-blue"></span>
                            <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png,.pdf"
                                   class="absolute inset-0 h-full w-full cursor-pointer opacity-0">
                        </div>
                        @error('photo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <div class="flex items-start gap-2">
                            <input type="checkbox" id="privacy_consent" name="privacy_consent" value="1"
                                   @checked(old('privacy_consent')) required
                                   class="mt-1 h-4 w-4 cursor-pointer rounded border-slate-300 text-brand-blue focus:ring-brand-blue">
                            <label for="privacy_consent" class="cursor-pointer text-sm text-slate-600">
                                I agree to be contacted about this request.
                                <a href="{{ url('/privacy-policy') }}" class="text-brand-blue hover:underline">Privacy Policy</a>
                                <span class="text-red-600">*</span>
                            </label>
                        </div>
                        @error('privacy_consent') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-md bg-brand-blue px-8 py-3.5 text-base font-semibold text-white shadow-sm transition-colors hover:bg-brand-blue-hover">
                            Submit Request <i class="bi bi-send"></i>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Sidebar --}}
            <div class="flex flex-col gap-6 lg:sticky lg:top-28 lg:col-span-4">

                <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-6 flex items-center gap-2 text-lg font-semibold text-brand-navy">
                        <i class="bi bi-clipboard-check text-brand-blue"></i> What happens next
                    </h2>
                    <ol class="flex flex-col gap-6">
                        @foreach ($steps as $i => [$title, $text])
                            <li class="flex items-start gap-4">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-tint text-sm font-semibold text-brand-navy">
                                    {{ $i + 1 }}
                                </span>
                                <div>
                                    <h3 class="text-sm font-semibold text-brand-navy">{{ $title }}</h3>
                                    <p class="mt-0.5 text-sm leading-relaxed text-slate-600">{{ $text }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>

                <div class="flex flex-col gap-4 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="flex items-center gap-2 text-lg font-semibold text-brand-navy">
                        <i class="bi bi-headset text-brand-blue"></i> Prefer to talk?
                    </h2>
                    <p class="text-sm text-slate-600">If your request is time-sensitive, call or message us directly.</p>

                    <div class="flex flex-col gap-2">
                        @if ($phone)
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}"
                               class="flex items-center gap-3 rounded-md bg-slate-100 px-4 py-2.5 text-sm font-semibold text-brand-navy transition-colors hover:bg-slate-200">
                                <i class="bi bi-telephone text-brand-blue"></i> {{ $phone }}
                            </a>
                        @endif
                        @if ($whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $whatsapp) }}" target="_blank" rel="noopener noreferrer"
                               class="flex items-center gap-3 rounded-md bg-brand-whatsapp px-4 py-2.5 text-sm font-semibold text-brand-navy transition-colors hover:brightness-105">
                                <i class="bi bi-whatsapp"></i> Chat on WhatsApp
                            </a>
                        @endif
                        @if ($email)
                            <a href="mailto:{{ $email }}"
                               class="flex items-center gap-3 rounded-md bg-slate-100 px-4 py-2.5 text-sm font-semibold text-brand-navy transition-colors hover:bg-slate-200">
                                <i class="bi bi-envelope text-brand-blue"></i> {{ $email }}
                            </a>
                        @endif
                    </div>

                    @if ($hours)
                        <div class="flex items-start gap-3 rounded-md bg-slate-50 p-4">
                            <i class="bi bi-clock mt-0.5 text-slate-500"></i>
                            <div>
                                <span class="block text-xs font-semibold text-brand-navy">Working Hours</span>
                                <span class="text-sm text-slate-600">{{ $hours }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 2. SUCCESS MESSAGE (shared with the service detail page) --}}
@include('partials.quote-success')

<script>
    // Show the chosen file name and warn early if it is over 5 MB (the server checks again)
    document.getElementById('photo').addEventListener('change', function () {
        var label = document.getElementById('file-name');
        var file = this.files[0];
        if (!file) { label.textContent = ''; return; }
        if (file.size > 5 * 1024 * 1024) {
            label.textContent = 'File is over 5 MB. Please choose a smaller one.';
            this.value = '';
            return;
        }
        label.textContent = file.name;
    });
</script>

@endsection