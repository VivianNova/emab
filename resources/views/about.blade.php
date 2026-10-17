@extends('layouts.app')

@section('title', 'About Us | ' . setting('company_name'))
@section('description', 'Learn about ' . setting('company_name') . ': reliable, safe and professional electrical services for homes, shops and businesses in ' . config('emab.area_text') . '.')

@section('content')

@php
    // ------------------------------------------------------------------
    // All About page content lives here. Edit this block, not the HTML.
    // Images: put files in public/images and set the path below.
    // A missing file shows a placeholder instead of a broken image.
    // ------------------------------------------------------------------

    // Backgrounds. Header: an image, a video, or both (the image shows while the video loads
    // and for visitors who prefer reduced motion). Put videos in public/videos.
    $headerImage = 'images/about_hero.jpg';
    $headerVideo = null;                      // e.g. 'videos/about.mp4'
    $bandImage   = 'images/ceilinglight.jpg'; // photo behind the Motto, Vision & Mission section

    $storyImage = 'images/CEO.png';

    $story = [
        'EMAB Electrical is based in ' . config('emab.base') . ' and serves ' . config('emab.area_text') . '. We take pride in delivering reliable, safe, and professional electrical solutions for homes, shops, and businesses in our service areas.',
        'From prompt troubleshooting in residential properties to large-scale wiring for retail spaces and commercial facilities, our dedication remains steadfast. We believe in open communication, dependable work, and fostering long-term relationships founded on genuine safety and everyday peace of mind.',
    ];

    $values = [
        ['bi-lightning-charge', 'Motto', 'Delivering results, exceeding expectations.'],
        ['bi-eye', 'Vision', 'To be a trusted provider of safe, reliable electrical solutions in ' . config('emab.area_text') . '.'],
        ['bi-bullseye', 'Mission', 'To deliver quality electrical services through skilled workmanship, dependable solutions, and a commitment to safety.'],
    ];

    $founder = ['name' => 'Erastus Mabuku', 'role' => 'Founder and CEO'];

    $reasons = [
        ['bi-shield-check', 'Safety first', 'Every job is wired, protected and tested to keep people and property safe.'],
        ['bi-receipt', 'Honest pricing', 'A clear written quotation before work starts, with no surprise charges.'],
        ['bi-tools', 'Quality workmanship', 'Neat, properly terminated and clearly labelled work that is easy to maintain.'],
        ['bi-people', 'Lasting relationships', 'We treat every client as a long-term relationship, not a one-off job.'],
    ];

    // Questions answered only with things the website actually does. Edit freely.
    $faqs = [
        [
            'question' => 'How do I get a quotation?',
            'answer'   => 'Tell us what you need and where the job is using the quote form. A photo helps but is optional. We review your request and send a clear written quotation as a PDF.',
            'link'     => ['Request a quote', url('/quote')],
        ],
        [
            'question' => 'What kind of work do you do?',
            'answer'   => 'Home and commercial wiring, lighting, solar and backup power, security systems and electric fencing, safety checks and fault finding, plus maintenance and repairs for homes, shops and businesses.',
            'link'     => ['See all services', url('/services')],
        ],
        [
            'question' => 'Which areas do you serve?',
            'answer'   => 'We are based in ' . config('emab.base') . ' and serve ' . config('emab.area_text') . '.',
        ],
        [
            'question' => 'Can I book a technician instead of asking for a quote?',
            'answer'   => 'Yes. Use the booking page to request a visit and we will get back to you to confirm.',
            'link'     => ['Book a technician', url('/book')],
        ],
        [
            'question' => 'How do you keep the work safe?',
            'answer'   => 'Safety comes first on every job. We use the right cable sizes, proper protection devices and earthing, and we check the work before handing it over.',
        ],
        [
            'question' => 'What is the best way to reach you?',
            'answer'   => 'Call us, message us on WhatsApp, or send a message from the contact page and we will respond.',
            'link'     => ['Contact us', url('/contact')],
        ],
    ];
@endphp

{{-- 1. PAGE HEADER (image / video banner) --}}
<section class="relative isolate overflow-hidden bg-brand-navy py-20 lg:py-28">
    @if (file_exists(public_path($headerImage)))
        <img src="{{ asset($headerImage) }}" alt="" class="absolute inset-0 -z-30 h-full w-full object-cover">
    @endif
    @if ($headerVideo && file_exists(public_path($headerVideo)))
        <video autoplay muted loop playsinline preload="metadata" aria-hidden="true"
               class="absolute inset-0 -z-20 h-full w-full object-cover motion-reduce:hidden">
            <source src="{{ asset($headerVideo) }}" type="video/mp4">
        </video>
    @endif
    <div class="absolute inset-0 -z-10 bg-black/55"></div>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <nav aria-label="Breadcrumb" class="mb-4">
            <ol class="flex items-center gap-2 text-sm text-white/70">
                <li><a href="{{ url('/') }}" class="transition-colors hover:text-white">Home</a></li>
                <li aria-hidden="true"><i class="bi bi-chevron-right text-xs"></i></li>
                <li aria-current="page" class="font-semibold text-white">About Us</li>
            </ol>
        </nav>
        <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl">
            About {{ setting('company_name') }}
        </h1>
        <p class="mt-4 max-w-2xl text-lg leading-relaxed text-white/90">
            Reliable, safe and professional electrical solutions for homes, shops and businesses.
        </p>
    </div>
</section>

{{-- 2. OUR STORY --}}
<section class="border-t border-slate-200 bg-slate-50 py-16 lg:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-2">
            <div class="flex flex-col gap-4">
                <h2 class="text-2xl font-semibold text-brand-navy sm:text-3xl">Our Story</h2>
                @foreach ($story as $paragraph)
                    <p class="text-base leading-relaxed text-slate-600">{{ $paragraph }}</p>
                @endforeach
            </div>

            <figure class="relative h-80 w-full overflow-hidden rounded-lg bg-slate-200 shadow-sm sm:h-96">
                @if (file_exists(public_path($storyImage)))
                    <img src="{{ asset($storyImage) }}" alt="{{ $founder['name'] }}, {{ $founder['role'] }} of {{ setting('company_name') }}"
                         loading="lazy" class="h-full w-full object-cover object-top">
                    <figcaption class="absolute inset-x-0 bottom-0 bg-brand-navy/85 px-5 py-3 text-white">
                        <span class="block text-base font-semibold">{{ $founder['name'] }}</span>
                        <span class="block text-sm text-white/80">{{ $founder['role'] }}</span>
                    </figcaption>
                @else
                    <div role="img" aria-label="Our story photo"
                         class="flex h-full w-full items-center justify-center px-4 text-center text-sm text-slate-500">
                        Story photo ({{ $storyImage }})
                    </div>
                @endif
            </figure>
        </div>
    </div>
</section>

{{-- 3. WHAT WE STAND FOR --}}
<section class="relative isolate overflow-hidden border-t border-slate-200 bg-brand-navy py-16 lg:py-20">
    @if (file_exists(public_path($bandImage)))
        <img src="{{ asset($bandImage) }}" alt="" loading="lazy" class="absolute inset-0 -z-20 h-full w-full object-cover">
    @endif
    <div class="absolute inset-0 -z-10 bg-brand-navy/80"></div>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 class="mb-12 text-2xl font-semibold text-white sm:text-3xl">Our Motto, Vision &amp; Mission</h2>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            @foreach ($values as [$icon, $title, $text])
                <div class="flex flex-col items-start rounded-lg border border-slate-200 bg-white p-6">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-brand-tint text-xl text-brand-sky">
                        <i class="bi {{ $icon }}"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-brand-navy">{{ $title }}</h3>
                    <p class="text-sm leading-relaxed text-slate-600">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- 4. WHY CHOOSE US --}}
<section id="why-us" class="border-t border-slate-200 bg-slate-50 py-16 lg:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 class="mb-12 text-2xl font-semibold text-brand-navy sm:text-3xl">Why Choose {{ setting('company_name') }}</h2>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($reasons as [$icon, $title, $text])
                <div class="flex flex-col items-start rounded-lg border border-slate-200 bg-white p-6">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-brand-tint text-xl text-brand-sky">
                        <i class="bi {{ $icon }}"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-brand-navy">{{ $title }}</h3>
                    <p class="text-sm leading-relaxed text-slate-600">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- 5. FREQUENTLY ASKED QUESTIONS --}}
<section id="faq" class="border-t border-slate-200 bg-white py-16 lg:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 class="mb-12 text-2xl font-semibold text-brand-navy sm:text-3xl">Frequently Asked Questions</h2>

        <div class="max-w-3xl divide-y divide-slate-200 rounded-lg border border-slate-200 bg-slate-50">
            @foreach ($faqs as $faq)
                <details class="group p-5">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-base font-semibold text-brand-navy [&::-webkit-details-marker]:hidden">
                        {{ $faq['question'] }}
                        <i class="bi bi-plus-lg shrink-0 text-brand-sky transition-transform group-open:rotate-45"></i>
                    </summary>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $faq['answer'] }}</p>
                    @if (! empty($faq['link']))
                        <a href="{{ $faq['link'][1] }}"
                           class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-brand-blue transition-colors hover:text-brand-navy">
                            {{ $faq['link'][0] }} <i class="bi bi-arrow-right"></i>
                        </a>
                    @endif
                </details>
            @endforeach
        </div>
    </div>
</section>

{{-- 6. CLOSING BANNER --}}
@include('partials.cta')

@endsection