@extends('layouts.app')

@section('title', 'Contact Us | ' . setting('company_name'))
@section('description', 'Get in touch with ' . setting('company_name') . ' for inquiries, support or site visits.')

@section('content')

@php
    $label = 'text-sm font-semibold text-slate-700';
    $input = 'h-11 w-full rounded-md border border-slate-300 bg-white pl-11 pr-3.5 text-slate-700 placeholder:text-slate-400 focus:border-brand-blue focus:outline-none focus:ring-1 focus:ring-brand-blue';
    $icon  = 'pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400';
    $cardLink = 'inline-flex items-center gap-1.5 text-sm font-semibold text-brand-blue transition-colors hover:text-brand-blue-hover';
@endphp

{{-- Header --}}
<section class="bg-white">
    <div class="mx-auto max-w-7xl px-4 pb-10 pt-10 sm:px-6 lg:px-8">
        <nav aria-label="Breadcrumb" class="mb-4 text-sm text-slate-500">
            <ol class="flex items-center gap-2">
                <li><a href="{{ url('/') }}" class="hover:text-brand-blue">Home</a></li>
                <li aria-hidden="true"><i class="bi bi-chevron-right text-xs"></i></li>
                <li aria-current="page" class="font-semibold text-slate-800">Contact Us</li>
            </ol>
        </nav>
        <h1 class="mb-2 text-3xl font-semibold text-brand-navy sm:text-4xl">Contact Us</h1>
        <p class="max-w-3xl text-lg text-slate-600">Get in touch with our team for inquiries, support, or site visits.</p>
    </div>
</section>

{{-- Contact cards --}}
<section class="border-t border-slate-200 bg-slate-50 py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

            <div class="flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div>
                    <span class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-brand-tint text-xl text-brand-navy"><i class="bi bi-telephone"></i></span>
                    <h2 class="mb-3 text-xl font-semibold text-brand-navy">Phone inquiries</h2>
                    <p class="font-semibold text-slate-800">{{ setting('phone') }}</p>
                    <p class="text-sm text-slate-500">{{ setting('working_hours') }}</p>
                    <p class="mt-4 text-sm font-semibold text-red-600"><i class="bi bi-exclamation-triangle"></i> Emergency line</p>
                    <p class="font-semibold text-slate-800">{{ setting('emergency_phone') }}</p>
                </div>
                <a href="{{ tel('phone') }}" class="{{ $cardLink }} mt-6">Call us <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div>
                    <span class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-brand-tint text-xl text-brand-navy"><i class="bi bi-envelope"></i></span>
                    <h2 class="mb-3 text-xl font-semibold text-brand-navy">Written inquiries</h2>
                    <p class="text-sm text-slate-500">General and quotation requests</p>
                    <a href="mailto:{{ setting('email') }}" class="break-all font-semibold text-slate-800 transition-colors hover:text-brand-blue">{{ setting('email') }}</a>
                </div>
                <a href="mailto:{{ setting('email') }}" class="{{ $cardLink }} mt-6">Send an email <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div>
                    <span class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-brand-tint text-xl text-brand-navy"><i class="bi bi-geo-alt"></i></span>
                    <h2 class="mb-3 text-xl font-semibold text-brand-navy">Visit us</h2>
                    <p class="font-semibold leading-snug text-slate-800">{{ config('emab.base') }}</p>
                    @if (setting('po_box'))
                        <p class="mt-4 text-sm text-slate-500">Postal address</p>
                        <p class="font-medium text-slate-800">{{ setting('po_box') }}</p>
                    @endif
                </div>
                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(config('emab.base')) }}"
                   target="_blank" rel="noopener noreferrer" class="{{ $cardLink }} mt-6">
                    Get directions <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Form and sidebar --}}
<section class="bg-slate-50 pb-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">

            {{-- Form --}}
            <div id="contact-form" class="scroll-mt-24 rounded-xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-8 md:p-8">
                <h2 class="text-2xl font-semibold text-brand-navy">Send us a message</h2>
                <p class="mb-6 mt-1 text-slate-600">Fill in your details and our team will get back to you.</p>

                @if (session('contact_success'))
                    <div role="status" class="mb-6 flex items-start gap-3 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                        <i class="bi bi-check-circle-fill mt-0.5 text-lg"></i>
                        <p class="text-sm">Thank you. Your message has been received and we will follow up shortly.</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div role="alert" class="mb-6 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                        <i class="bi bi-exclamation-circle-fill mt-0.5 text-lg"></i>
                        <p class="text-sm">Please check the highlighted details below and try again.</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" class="flex flex-col gap-5">
                    @csrf

                    {{-- Honeypot: hidden from people, filled by bots --}}
                    <div class="hidden" aria-hidden="true">
                        <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="flex flex-col gap-1.5">
                            <label for="name" class="{{ $label }}">Full name <span class="text-red-600">*</span></label>
                            <div class="relative">
                                <i class="bi bi-person {{ $icon }}"></i>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" required
                                       autocomplete="name" placeholder="e.g. Jane Wanjiku" class="{{ $input }}">
                            </div>
                            @error('name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="email" class="{{ $label }}">Email <span class="text-red-600">*</span></label>
                            <div class="relative">
                                <i class="bi bi-envelope {{ $icon }}"></i>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                       autocomplete="email" placeholder="name@example.com" class="{{ $input }}">
                            </div>
                            @error('email') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="flex flex-col gap-1.5">
                            <label for="phone" class="{{ $label }}">Phone <span class="font-normal text-slate-400">(optional)</span></label>
                            <div class="relative">
                                <i class="bi bi-telephone {{ $icon }}"></i>
                                <input id="phone" name="phone" type="tel" value="{{ old('phone') }}"
                                       autocomplete="tel" placeholder="0700 000 000" class="{{ $input }}">
                            </div>
                            @error('phone') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="subject" class="{{ $label }}">Subject <span class="text-red-600">*</span></label>
                            <div class="relative">
                                <i class="bi bi-list-ul {{ $icon }}"></i>
                                <select id="subject" name="subject" required class="{{ $input }} cursor-pointer appearance-none pr-10">
                                    <option value="" disabled @selected(! old('subject'))>Select an inquiry topic</option>
                                    @foreach (\App\Models\ContactMessage::SUBJECTS as $subject)
                                        <option value="{{ $subject }}" @selected(old('subject') === $subject)>{{ $subject }}</option>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                            @error('subject') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="message" class="{{ $label }}">Message <span class="text-red-600">*</span></label>
                        <textarea id="message" name="message" rows="5" required
                                  placeholder="How can we help with your electrical or solar needs?"
                                  class="w-full resize-y rounded-md border border-slate-300 bg-white p-3.5 text-slate-700 placeholder:text-slate-400 focus:border-brand-blue focus:outline-none focus:ring-1 focus:ring-brand-blue">{{ old('message') }}</textarea>
                        @error('message') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="flex cursor-pointer items-start gap-3 text-sm text-slate-600">
                            <input type="checkbox" name="consent" value="1" required @checked(old('consent'))
                                   class="mt-0.5 h-4 w-4 accent-brand-blue">
                            <span>
                                I agree to be contacted regarding this inquiry.
                                <a href="{{ url('/privacy-policy') }}" class="font-semibold text-brand-blue hover:underline">Privacy Policy</a>
                            </span>
                        </label>
                        @error('consent') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-brand-blue px-8 py-3.5 text-base font-semibold text-white shadow-sm transition-colors hover:bg-brand-blue-hover sm:w-auto">
                            Send Message <i class="bi bi-send"></i>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Sidebar --}}
            <aside class="flex flex-col gap-4 lg:col-span-4">

                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-3 flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-tint text-lg text-brand-navy"><i class="bi bi-clock"></i></span>
                        <h3 class="text-lg font-semibold text-brand-navy">Working hours</h3>
                    </div>
                    <p class="rounded bg-slate-50 px-3 py-2 text-sm text-slate-600">{{ setting('working_hours') }}</p>
                </div>

                <div class="rounded-xl border border-red-100 bg-red-50 p-6 shadow-sm">
                    <div class="mb-3 flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-red text-lg text-white"><i class="bi bi-exclamation-triangle"></i></span>
                        <h3 class="text-lg font-semibold text-red-900">Electrical emergency?</h3>
                    </div>
                    <p class="mb-4 text-sm leading-relaxed text-red-800">
                        For a sudden power outage, exposed live wiring or a critical breakdown, call us directly instead of using this form.
                    </p>
                    <div class="flex flex-col gap-3">
                        <a href="{{ tel('emergency_phone') }}"
                           class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-brand-red px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-red-hover">
                            <i class="bi bi-telephone-fill"></i> Call {{ setting('emergency_phone') }}
                        </a>
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', (string) setting('whatsapp')) }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-brand-whatsapp px-4 py-3 text-sm font-semibold text-brand-navy transition hover:brightness-105">
                            <i class="bi bi-whatsapp"></i> Chat on WhatsApp
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

{{-- Map --}}
<section id="location-map" class="border-t border-slate-200 bg-white py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="h-[340px] w-full overflow-hidden rounded-xl border border-slate-200 bg-slate-100 shadow-sm">
            <iframe src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d249.35136109504828!2d34.56327688819936!3d0.5687107625513215!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2s!5e0!3m2!1sen!2ske!4v1791975320379!5m2!1sen!2ske"
                    width="600" height="450" title="Map showing {{ setting('company_name') }}"
                    class="h-full w-full border-0" style="border:0;" allowfullscreen=""
                    loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
        </div>
    </div>
</section>

@endsection