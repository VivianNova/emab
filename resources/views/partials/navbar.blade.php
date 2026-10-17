@php
    $links = [
        ['Home', '/', '/'],
        ['About', '/about', 'about'],
        ['Services', '/services', 'services*'],
        ['Projects', '/projects', 'projects*'],
        ['Contact', '/contact', 'contact'],
    ];
@endphp

<header class="sticky top-0 z-40 border-b border-slate-800 bg-brand-navy text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex shrink-0 items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="{{ setting('company_name') }} logo"
                     class="h-10 w-10 shrink-0 rounded-full bg-white object-contain p-0.5">
                <span class="text-lg font-bold leading-tight tracking-tight text-white">{{ setting('company_name') }}</span>
            </a>

            {{-- Desktop links --}}
            <nav class="hidden items-center gap-8 lg:flex" aria-label="Main">
                @foreach ($links as [$label, $href, $pattern])
                    <a href="{{ url($href) }}"
                       @if (request()->is($pattern)) aria-current="page" @endif
                       @class([
                           'pb-1 text-sm transition-colors',
                           'border-b-2 border-brand-blue font-semibold text-white' => request()->is($pattern),
                           'font-medium text-slate-300 hover:text-white' => ! request()->is($pattern),
                       ])>{{ $label }}</a>
                @endforeach
            </nav>

            {{-- Actions --}}
            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ tel('phone') }}" aria-label="Call now"
                   class="inline-flex items-center gap-1.5 rounded-md bg-brand-red px-3 py-2 text-sm font-semibold text-white transition-colors hover:bg-brand-red-hover sm:px-4">
                    <i class="bi bi-telephone-fill text-base"></i>
                    <span class="hidden sm:inline">Call Now</span>
                </a>

                <a href="{{ url('/quote') }}"
                   class="hidden items-center rounded-md bg-brand-blue px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-brand-blue-hover sm:inline-flex">
                    Get Quote
                </a>

                <button type="button" data-toggle="#mobile-menu" aria-label="Toggle menu"
                        aria-controls="mobile-menu" aria-expanded="false"
                        class="rounded-md p-2 text-slate-300 hover:bg-brand-blue hover:text-white lg:hidden">
                    <i class="bi bi-list text-2xl"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div id="mobile-menu" class="hidden border-t border-slate-800 lg:hidden">
        <nav class="mx-auto max-w-7xl space-y-1 px-4 py-4 sm:px-6" aria-label="Mobile">
            @foreach ($links as [$label, $href, $pattern])
                <a href="{{ url($href) }}"
                   @class([
                       'block rounded-md px-3 py-2 text-base',
                       'bg-brand-blue font-semibold text-white' => request()->is($pattern),
                       'font-medium text-slate-300 hover:bg-brand-blue hover:text-white' => ! request()->is($pattern),
                   ])>{{ $label }}</a>
            @endforeach

            <a href="{{ url('/quote') }}"
               class="mt-2 block rounded-md bg-brand-blue px-3 py-2 text-center text-base font-semibold text-white hover:bg-brand-blue-hover sm:hidden">
                Get Quote
            </a>
        </nav>
    </div>
</header>