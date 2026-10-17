@extends('layouts.app')

@section('title', 'Book a Technician | ' . setting('company_name'))
@section('description', 'Choose a date and time and we will confirm your electrical service booking by phone.')

@section('content')

@php
    $label = 'text-sm font-semibold text-slate-700';
    $input = 'h-11 w-full rounded-md border border-slate-300 bg-white pl-11 pr-3.5 text-slate-700 placeholder:text-slate-400 focus:border-brand-blue focus:outline-none focus:ring-1 focus:ring-brand-blue';
    $icon  = 'pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400';
@endphp

{{-- Header --}}
<section class="bg-white">
    <div class="mx-auto max-w-7xl px-4 pb-8 pt-10 sm:px-6 lg:px-8">
        <nav aria-label="Breadcrumb" class="mb-4 text-sm text-slate-500">
            <ol class="flex items-center gap-2">
                <li><a href="{{ url('/') }}" class="hover:text-brand-blue">Home</a></li>
                <li aria-hidden="true"><i class="bi bi-chevron-right text-xs"></i></li>
                <li aria-current="page" class="font-semibold text-slate-800">Book a Technician</li>
            </ol>
        </nav>
        <h1 class="mb-2 text-3xl font-semibold text-brand-navy sm:text-4xl">Book a Technician</h1>
        <p class="text-lg text-slate-600">Choose a date and time and we'll confirm by phone.</p>
    </div>
</section>

<section class="bg-slate-50 py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">

            {{-- Form --}}
            <div id="booking-form" class="scroll-mt-24 rounded-xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-8 md:p-8">
                <div class="mb-6 border-b border-slate-100 pb-5">
                    <h2 class="text-xl font-semibold text-brand-navy">Service schedule form</h2>
                    <p class="mt-1 text-sm text-slate-500">Fields marked <span class="text-red-600">*</span> are required.</p>
                </div>

                @if (session('booking_success'))
                    <div role="status" class="mb-6 flex items-start gap-3 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                        <i class="bi bi-check-circle-fill mt-0.5 text-lg"></i>
                        <p class="text-sm">Thank you. Your booking request has been received. We will call you to confirm.</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div role="alert" class="mb-6 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                        <i class="bi bi-exclamation-circle-fill mt-0.5 text-lg"></i>
                        <p class="text-sm">Please check the highlighted details below and try again.</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('book.store') }}" class="flex flex-col gap-6">
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
                            <label for="phone" class="{{ $label }}">Phone <span class="text-red-600">*</span></label>
                            <div class="relative">
                                <i class="bi bi-telephone {{ $icon }}"></i>
                                <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required
                                       autocomplete="tel" placeholder="0700 000 000" class="{{ $input }}">
                            </div>
                            @error('phone') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="flex flex-col gap-1.5">
                            <label for="email" class="{{ $label }}">Email <span class="font-normal text-slate-400">(optional)</span></label>
                            <div class="relative">
                                <i class="bi bi-envelope {{ $icon }}"></i>
                                <input id="email" name="email" type="email" value="{{ old('email') }}"
                                       autocomplete="email" placeholder="name@example.com" class="{{ $input }}">
                            </div>
                            @error('email') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="service_id" class="{{ $label }}">Service needed</label>
                            <div class="relative">
                                <i class="bi bi-lightning-charge {{ $icon }}"></i>
                                <select id="service_id" name="service_id" class="{{ $input }} cursor-pointer appearance-none pr-10">
                                    <option value="">Not sure / General</option>
                                    @foreach ($categories as $category)
                                        <optgroup label="{{ $category->name }}">
                                            @foreach ($category->services as $service)
                                                <option value="{{ $service->id }}"
                                                        @selected((string) old('service_id', $selected) === (string) $service->id)>
                                                    {{ $service->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                <i class="bi bi-chevron-down pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                            @error('service_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="address" class="{{ $label }}">Address <span class="text-red-600">*</span></label>
                        <div class="relative">
                            <i class="bi bi-geo-alt {{ $icon }}"></i>
                            <input id="address" name="address" type="text" value="{{ old('address') }}" required
                                   autocomplete="street-address" placeholder="House number, street, area and town" class="{{ $input }}">
                        </div>
                        @error('address') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 items-start gap-4 md:grid-cols-2">
                        <div class="flex flex-col gap-1.5">
                            <label for="preferred_date" class="{{ $label }}">Preferred date <span class="text-red-600">*</span></label>
                            <div class="relative">
                                <i class="bi bi-calendar-event {{ $icon }}"></i>
                                <input id="preferred_date" name="preferred_date" type="date" required
                                       value="{{ old('preferred_date') }}" min="{{ now()->toDateString() }}" class="{{ $input }}">
                            </div>
                            @error('preferred_date') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <fieldset class="flex flex-col gap-1.5">
                            <legend class="{{ $label }}">Preferred time <span class="text-red-600">*</span></legend>
                            <div class="mt-1.5 grid grid-cols-3 gap-2">
                                @foreach (\App\Models\Booking::SLOTS as $value => $slot)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="preferred_time" value="{{ $value }}" class="peer sr-only"
                                               @checked(old('preferred_time', 'morning') === $value)>
                                        <div class="flex h-12 select-none flex-col items-center justify-center rounded-md bg-slate-100 text-slate-600 transition-colors peer-checked:bg-brand-blue peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-brand-blue">
                                            <span class="text-sm font-semibold leading-none">{{ $slot['label'] }}</span>
                                            <span class="mt-0.5 text-[11px] opacity-80">{{ $slot['hours'] }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('preferred_time') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                        </fieldset>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="notes" class="{{ $label }}">Job notes</label>
                        <textarea id="notes" name="notes" rows="4" placeholder="Describe the problem or work needed"
                                  class="w-full resize-y rounded-md border border-slate-300 bg-white p-3.5 text-slate-700 placeholder:text-slate-400 focus:border-brand-blue focus:outline-none focus:ring-1 focus:ring-brand-blue">{{ old('notes') }}</textarea>
                        @error('notes') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="flex cursor-pointer items-start gap-3 text-sm text-slate-600">
                            <input type="checkbox" name="consent" value="1" required @checked(old('consent'))
                                   class="mt-0.5 h-4 w-4 accent-brand-blue">
                            <span>
                                I agree to be contacted about this booking.
                                <a href="{{ url('/privacy-policy') }}" class="font-semibold text-brand-blue hover:underline">Privacy Policy</a>
                            </span>
                        </label>
                        @error('consent') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-brand-blue px-8 py-3.5 text-base font-semibold text-white shadow-sm transition-colors hover:bg-brand-blue-hover sm:w-auto">
                            <i class="bi bi-calendar-check"></i> Request Booking
                        </button>
                    </div>
                </form>
            </div>

            {{-- Sidebar --}}
            <aside class="flex flex-col gap-4 lg:col-span-4">

                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-3 flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-tint text-lg text-brand-navy"><i class="bi bi-clock"></i></span>
                        <h3 class="text-lg font-semibold text-brand-navy">Service hours</h3>
                    </div>
                    <p class="rounded bg-slate-50 px-3 py-2 text-sm text-slate-600">{{ setting('working_hours') }}</p>
                </div>

                <div class="rounded-xl border border-red-100 bg-red-50 p-6 shadow-sm">
                    <div class="mb-3 flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-red text-lg text-white"><i class="bi bi-exclamation-triangle"></i></span>
                        <h3 class="text-lg font-semibold text-red-900">Electrical emergency?</h3>
                    </div>
                    <p class="mb-4 text-sm text-red-800">For a live hazard, call us now instead of using this form.</p>
                    <a href="{{ tel('emergency_phone') }}"
                       class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-brand-red px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-red-hover">
                        <i class="bi bi-telephone-fill"></i> Call {{ setting('emergency_phone') }}
                    </a>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-lg text-slate-700"><i class="bi bi-list-check"></i></span>
                        <h3 class="text-lg font-semibold text-brand-navy">What to expect</h3>
                    </div>
                    <ol class="flex flex-col gap-5">
                        @foreach ([
                            ['We review your request', 'We check technician availability for the slot you chose.'],
                            ['We confirm by phone', 'We call to confirm the details and the arrival time.'],
                            ['A technician visits', 'A qualified electrician arrives at the agreed time.'],
                        ] as $i => [$title, $text])
                            <li class="flex items-start gap-4">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-blue text-sm font-bold text-white">{{ $i + 1 }}</span>
                                <div>
                                    <h4 class="font-semibold text-brand-navy">{{ $title }}</h4>
                                    <p class="mt-0.5 text-sm text-slate-600">{{ $text }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </aside>
        </div>
    </div>
</section>

@endsection