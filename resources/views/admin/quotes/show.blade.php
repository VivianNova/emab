@extends('layouts.admin')

@section('title', $quote->ref)

@section('content')

@php
    $cardIcon = 'flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-tint text-xl text-brand-navy';
    $label = 'text-xs font-semibold uppercase tracking-wider text-slate-500';
@endphp

<x-admin.page-header :title="$quote->ref"
                     :crumbs="['Quotes' => route('admin.quotes.index'), $quote->ref]"
                     :subtitle="'Submitted on ' . $quote->created_at->format('l, j F Y \a\t h:i A')">
    <x-admin.status-badge :status="$quote->status" class="px-3 py-1 text-sm" />
</x-admin.page-header>

<div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">

    {{-- Left: request and notes --}}
    <div class="flex flex-col gap-6 lg:col-span-8">

        <section class="flex flex-col gap-4 rounded-xl bg-white p-6 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="{{ $cardIcon }}"><i class="bi bi-clipboard-check"></i></span>
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Request</h2>
                    <p class="text-xs text-slate-500">What the client asked for</p>
                </div>
            </div>

            <div class="flex flex-col gap-1 rounded-lg bg-slate-100 p-4">
                <span class="{{ $label }}">Requested service</span>
                <span class="flex items-center gap-2 font-semibold text-slate-900">
                    <i class="bi bi-lightning-charge text-brand-blue"></i>
                    {{ $quote->service?->name ?? 'Not sure / General' }}
                </span>
                @if ($quote->service?->category)
                    <span class="text-xs text-slate-500">{{ $quote->service->category->name }}</span>
                @endif
            </div>

            <div class="flex flex-col gap-1">
                <span class="{{ $label }}">Description</span>
                <div class="rounded-lg bg-slate-100 p-4">
                    <p class="whitespace-pre-line leading-relaxed text-slate-700">{{ $quote->description }}</p>
                </div>
            </div>

            @if ($quote->photo)
                <div class="flex flex-col gap-1">
                    <span class="{{ $label }}">Attached file</span>

                    @if ($file)
                        <div class="flex flex-col justify-between gap-3 rounded-lg bg-slate-100 p-4 sm:flex-row sm:items-center">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded bg-brand-tint text-xl text-brand-sky">
                                    <i class="bi {{ $file['type'] === 'PDF' ? 'bi-file-earmark-pdf' : 'bi-file-earmark-image' }}"></i>
                                </span>
                                <div class="flex min-w-0 flex-col">
                                    <span class="font-semibold text-slate-900">{{ $file['type'] }} file</span>
                                    <span class="text-xs text-slate-500">{{ $file['size'] }}</span>
                                </div>
                            </div>
                            <a href="{{ route('admin.quotes.file', $quote) }}"
                               class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm transition-colors hover:bg-brand-tint">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </div>
                    @else
                        <p class="rounded-lg bg-slate-100 p-4 text-sm text-slate-500">The attached file could not be found.</p>
                    @endif
                </div>
            @endif
        </section>

        <section class="flex flex-col gap-4 rounded-xl bg-white p-6 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="{{ $cardIcon }}"><i class="bi bi-pencil-square"></i></span>
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Admin notes</h2>
                    <p class="text-xs text-slate-500">Internal remarks, never shown to the client</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.quotes.notes', $quote) }}" class="flex flex-col gap-4">
                @csrf
                @method('PATCH')

                <div class="flex flex-col gap-1">
                    <label for="admin_notes" class="text-xs font-semibold text-slate-500">Staff remarks</label>
                    <textarea id="admin_notes" name="admin_notes" rows="4"
                              class="w-full resize-y rounded-lg bg-slate-100 p-4 text-sm text-slate-800 transition-colors focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue">{{ old('admin_notes', $quote->admin_notes) }}</textarea>
                    @error('admin_notes') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-brand-blue px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-blue-hover">
                        <i class="bi bi-save"></i> Save
                    </button>
                </div>
            </form>
        </section>
    </div>

    {{-- Right: contact and status --}}
    <div class="flex flex-col gap-6 lg:col-span-4">

        <section class="flex flex-col gap-4 rounded-xl bg-white p-6 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="{{ $cardIcon }}"><i class="bi bi-person-lines-fill"></i></span>
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Contact details</h2>
                    <p class="text-xs text-slate-500">Customer identity and channels</p>
                </div>
            </div>

            <dl class="flex flex-col divide-y divide-slate-100 text-sm">
                <div class="flex items-start gap-3 py-3">
                    <i class="bi bi-person-circle mt-0.5 text-lg text-slate-400"></i>
                    <div class="min-w-0"><dt class="text-xs text-slate-500">Name</dt><dd class="font-semibold text-slate-900">{{ $quote->name }}</dd></div>
                </div>
                <div class="flex items-start gap-3 py-3">
                    <i class="bi bi-telephone mt-0.5 text-lg text-slate-400"></i>
                    <div class="min-w-0"><dt class="text-xs text-slate-500">Phone</dt>
                        <dd><a href="tel:{{ $quote->phone }}" class="font-semibold text-brand-blue hover:underline">{{ $quote->phone }}</a></dd></div>
                </div>
                <div class="flex items-start gap-3 py-3">
                    <i class="bi bi-envelope mt-0.5 text-lg text-slate-400"></i>
                    <div class="min-w-0"><dt class="text-xs text-slate-500">Email</dt>
                        <dd class="break-all">
                            @if ($quote->email)
                                <a href="mailto:{{ $quote->email }}" class="font-semibold text-brand-blue hover:underline">{{ $quote->email }}</a>
                            @else
                                <span class="text-slate-500">Not given</span>
                            @endif
                        </dd></div>
                </div>
                <div class="flex items-start gap-3 py-3">
                    <i class="bi bi-geo-alt mt-0.5 text-lg text-slate-400"></i>
                    <div class="min-w-0"><dt class="text-xs text-slate-500">Location</dt><dd class="text-slate-800">{{ $quote->location }}</dd></div>
                </div>
            </dl>

            <div class="grid grid-cols-2 gap-2">
                <a href="tel:{{ $quote->phone }}"
                   class="flex items-center justify-center gap-2 rounded-lg bg-slate-100 px-3 py-2.5 text-sm font-semibold text-slate-800 transition-colors hover:bg-slate-200">
                    <i class="bi bi-telephone text-brand-blue"></i> Call
                </a>
                @if ($quote->whatsapp_url)
                    <a href="{{ $quote->whatsapp_url }}" target="_blank" rel="noopener"
                       class="flex items-center justify-center gap-2 rounded-lg bg-brand-whatsapp px-3 py-2.5 text-sm font-semibold text-brand-navy transition hover:brightness-105">
                        <i class="bi bi-whatsapp"></i> WhatsApp
                    </a>
                @endif
            </div>
        </section>

        <section class="flex flex-col gap-4 rounded-xl bg-white p-6 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="{{ $cardIcon }}"><i class="bi bi-arrow-left-right"></i></span>
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Update status</h2>
                    <p class="text-xs text-slate-500">Where this quote is now</p>
                </div>
            </div>

            <p class="flex items-start gap-2 rounded-lg bg-brand-tint p-3 text-xs leading-normal text-brand-navy">
                <i class="bi bi-info-circle mt-0.5"></i>
                <span>Currently marked as <strong>{{ ucfirst($quote->status) }}</strong>. Change it as you contact the client and send the quote.</span>
            </p>

            <form method="POST" action="{{ route('admin.quotes.status', $quote) }}" class="flex flex-col gap-4">
                @csrf
                @method('PATCH')

                <div class="flex flex-col gap-1">
                    <label for="status" class="text-xs font-semibold text-slate-500">Status</label>
                    <select id="status" name="status"
                            class="w-full cursor-pointer rounded-lg bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-800 transition-colors focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue">
                        @foreach (\App\Models\QuoteRequest::STATUSES as $option)
                            <option value="{{ $option }}" @selected(old('status', $quote->status) === $option)>{{ ucfirst($option) }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-brand-blue py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-blue-hover">
                    <i class="bi bi-check-lg"></i> Save
                </button>
            </form>
        </section>
    </div>
</div>

@endsection