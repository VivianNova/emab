@php
    $quickLinks = [
        ['Home', '/'],
        ['About', '/about'],
        ['Services', '/services'],
        ['Projects', '/projects'],
        ['Contact', '/contact'],
    ];

    $socials = [
        ['facebook', 'bi-facebook', 'Facebook', 'https://www.facebook.com/'],
        ['instagram', 'bi-instagram', 'Instagram', 'https://www.instagram.com/'],
        ['x', 'bi-twitter-x', 'X', 'https://x.com/'],
        ['linkedin', 'bi-linkedin', 'LinkedIn', 'https://www.linkedin.com/'],
    ];

    $poBox = preg_replace('/^P\.O\.\s*Box\s*/i', '', (string) setting('po_box'));
@endphp

<footer class="border-t border-slate-800 bg-brand-navy pb-12 pt-16 text-slate-300">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-12 grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-4">

            {{-- Logo and sentence --}}
            <div class="flex flex-col items-start">
                <a href="{{ url('/') }}" class="mb-4 flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ setting('company_name') }} logo"
                         class="h-12 w-12 shrink-0 rounded-full bg-white object-contain p-0.5">
                    <span class="text-lg font-bold tracking-tight text-white">{{ setting('company_name') }}</span>
                </a>
                <p class="text-sm leading-relaxed text-slate-400">
                    Professional electrical contracting services for homes, shops, and businesses in {{ config('emab.area_text') }}.
                </p>
            </div>

            {{-- Quick links --}}
            <div>
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-white">Quick Links</h3>
                <ul class="space-y-2 text-sm">
                    @foreach ($quickLinks as [$label, $href])
                        <li><a href="{{ url($href) }}" class="text-slate-400 transition-colors hover:text-white">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Contact details --}}
            <div>
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-white">Contact Details</h3>
                <ul class="space-y-2 text-sm text-slate-400">
                    <li>
                        <span class="font-medium text-white">Phone:</span>
                        <a href="{{ tel('phone') }}" class="transition-colors hover:text-white">{{ setting('phone') }}</a>
                    </li>
                    <li>
                        <span class="font-semibold text-brand-red">Emergency:</span>
                        <a href="{{ tel('emergency_phone') }}" class="transition-colors hover:text-white">{{ setting('emergency_phone') }}</a>
                    </li>
                    <li>
                        <span class="font-medium text-white">Email:</span>
                        <a href="mailto:{{ setting('email') }}" class="transition-colors hover:text-white">{{ setting('email') }}</a>
                    </li>
                    <li><span class="font-medium text-white">Address:</span> {{ config('emab.base') }}</li>
                    @if (setting('po_box'))
                        <li><span class="font-medium text-white">P.O. Box:</span> {{ $poBox }}</li>
                    @endif
                    <li><span class="font-medium text-white">Hours:</span> {{ setting('working_hours') }}</li>
                </ul>
            </div>

            {{-- Social --}}
            <div>
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-white">Connect With Us</h3>
                <div class="flex items-center gap-3">
                    @foreach ($socials as [$key, $icon, $name, $baseUrl])
                        @php($profile = setting($key))
                        @continue(blank($profile) || $profile === '#')
                        @php($url = str_starts_with($profile, 'http') ? $profile : $baseUrl.ltrim($profile, '@/'))
                        <a href="{{ $url }}" aria-label="{{ $name }}" target="_blank" rel="noopener noreferrer"
                           class="flex h-9 w-9 items-center justify-center rounded-md bg-slate-800 text-slate-300 transition-colors hover:bg-brand-blue hover:text-white">
                            <i class="bi {{ $icon }} text-lg"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="border-t border-slate-800 pt-8 text-center">
            <p class="text-xs text-slate-500">
                &copy; {{ date('Y') }} {{ setting('company_name') }}. All rights reserved.
            </p>
        </div>
    </div>
</footer>