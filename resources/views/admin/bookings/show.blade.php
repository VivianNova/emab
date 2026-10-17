@extends('layouts.admin')

@section('title', $booking->ref)

@section('content')

@php
    $isStaff = auth()->user()->isStaff();
    $cardIcon = 'flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-tint text-xl text-brand-navy';
    $label = 'text-xs font-semibold uppercase tracking-wider text-slate-500';
    $select = 'w-full cursor-pointer rounded-lg bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-800 transition-colors focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue';
    $saveBtn = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-blue px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-blue-hover';
    $whatsapp = 'https://wa.me/' . ltrim($booking->phone, '+');
    $slot = $booking->slot;
@endphp

<x-admin.page-header :title="$booking->ref"
                     :crumbs="['Bookings' => route('admin.bookings.index'), $booking->ref]"
                     :subtitle="'Requested on ' . $booking->created_at->format('l, j F Y \a\t h:i A')">
    <x-admin.status-badge :status="$booking->status" class="px-3 py-1 text-sm" />
    <a href="{{ route('admin.bookings.index') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-800 transition-colors hover:bg-slate-300">
        <i class="bi bi-arrow-left"></i> Back to Bookings
    </a>
</x-admin.page-header>

<div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">

    {{-- Left: booking and notes --}}
    <div class="flex flex-col gap-6 lg:col-span-8">

        <section class="flex flex-col gap-4 rounded-xl bg-white p-6 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="{{ $cardIcon }}"><i class="bi bi-calendar-event"></i></span>
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Booking information</h2>
                    <p class="text-xs text-slate-500">The visit the client asked for</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 rounded-lg bg-slate-100 p-4 sm:grid-cols-3">
                <div class="flex flex-col gap-1">
                    <span class="{{ $label }}">Service</span>
                    <span class="flex items-center gap-1.5 text-sm font-semibold text-slate-900">
                        <i class="bi bi-lightning-charge text-brand-blue"></i> {{ $booking->service?->name ?? 'Not sure / General' }}
                    </span>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="{{ $label }}">Date</span>
                    <span class="flex items-center gap-1.5 text-sm font-semibold text-slate-900">
                        <i class="bi bi-calendar3 text-slate-400"></i> {{ $booking->preferred_date->format('l, j M Y') }}
                    </span>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="{{ $label }}">Time slot</span>
                    <span class="flex items-center gap-1.5 text-sm font-semibold text-slate-900">
                        <i class="bi bi-clock text-slate-400"></i>
                        {{ $slot ? $slot['label'] . ' (' . $slot['hours'] . ')' : 'Not set' }}
                    </span>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <span class="{{ $label }}">Client job notes</span>
                <div class="rounded-lg bg-slate-100 p-4">
                    @if ($booking->notes)
                        <p class="whitespace-pre-line leading-relaxed text-slate-700">{{ $booking->notes }}</p>
                    @else
                        <p class="text-sm text-slate-500">The client left no notes.</p>
                    @endif
                </div>
            </div>
        </section>

        @unless ($isStaff)
            <section class="flex flex-col gap-4 rounded-xl bg-white p-6 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="{{ $cardIcon }}"><i class="bi bi-pencil-square"></i></span>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Admin notes</h2>
                        <p class="text-xs text-slate-500">Internal remarks, never shown to the client</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.bookings.notes', $booking) }}" class="flex flex-col gap-4">
                    @csrf
                    @method('PATCH')

                    <div class="flex flex-col gap-1">
                        <label for="admin_notes" class="text-xs font-semibold text-slate-500">Operational remarks</label>
                        <textarea id="admin_notes" name="admin_notes" rows="4"
                                  class="w-full resize-y rounded-lg bg-slate-100 p-4 text-sm text-slate-800 transition-colors focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue">{{ old('admin_notes', $booking->admin_notes) }}</textarea>
                        @error('admin_notes') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="{{ $saveBtn }}"><i class="bi bi-save"></i> Save notes</button>
                    </div>
                </form>
            </section>
        @endunless
    </div>

    {{-- Right: contact, status, technician --}}
    <div class="flex flex-col gap-6 lg:col-span-4">

        <section class="flex flex-col gap-4 rounded-xl bg-white p-6 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="{{ $cardIcon }}"><i class="bi bi-geo-alt"></i></span>
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Contact and address</h2>
                    <p class="text-xs text-slate-500">Customer and site location</p>
                </div>
            </div>

            <dl class="flex flex-col divide-y divide-slate-100 text-sm">
                <div class="flex items-start gap-3 py-3">
                    <i class="bi bi-person-circle mt-0.5 text-lg text-slate-400"></i>
                    <div class="min-w-0"><dt class="text-xs text-slate-500">Client name</dt><dd class="font-semibold text-slate-900">{{ $booking->name }}</dd></div>
                </div>
                <div class="flex items-start gap-3 py-3">
                    <i class="bi bi-telephone mt-0.5 text-lg text-slate-400"></i>
                    <div class="min-w-0"><dt class="text-xs text-slate-500">Phone</dt>
                        <dd><a href="tel:{{ $booking->phone }}" class="font-semibold text-brand-blue hover:underline">{{ $booking->phone }}</a></dd></div>
                </div>
                <div class="flex items-start gap-3 py-3">
                    <i class="bi bi-envelope mt-0.5 text-lg text-slate-400"></i>
                    <div class="min-w-0"><dt class="text-xs text-slate-500">Email</dt>
                        <dd class="break-all">
                            @if ($booking->email)
                                <a href="mailto:{{ $booking->email }}" class="font-semibold text-brand-blue hover:underline">{{ $booking->email }}</a>
                            @else
                                <span class="text-slate-500">Not given</span>
                            @endif
                        </dd></div>
                </div>
                <div class="flex items-start gap-3 py-3">
                    <i class="bi bi-pin-map mt-0.5 text-lg text-slate-400"></i>
                    <div class="min-w-0"><dt class="text-xs text-slate-500">Site address</dt><dd class="text-slate-800">{{ $booking->address }}</dd></div>
                </div>
            </dl>

            <div class="grid grid-cols-2 gap-2">
                <a href="tel:{{ $booking->phone }}"
                   class="flex items-center justify-center gap-2 rounded-lg bg-slate-100 px-3 py-2.5 text-sm font-semibold text-slate-800 transition-colors hover:bg-slate-200">
                    <i class="bi bi-telephone text-brand-blue"></i> Call
                </a>
                <a href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer"
                   class="flex items-center justify-center gap-2 rounded-lg bg-brand-whatsapp px-3 py-2.5 text-sm font-semibold text-brand-navy transition hover:brightness-105">
                    <i class="bi bi-whatsapp"></i> WhatsApp
                </a>
            </div>
        </section>

        <section class="flex flex-col gap-4 rounded-xl bg-white p-6 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="{{ $cardIcon }}"><i class="bi bi-arrow-left-right"></i></span>
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Update status</h2>
                    <p class="text-xs text-slate-500">Booking progress</p>
                </div>
            </div>

            @if ($canUpdateStatus)
                <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="flex flex-col gap-4">
                    @csrf
                    @method('PATCH')

                    <div class="flex flex-col gap-1">
                        <label for="status" class="text-xs font-semibold text-slate-500">Status</label>
                        <select id="status" name="status" class="{{ $select }}">
                            @foreach ($statusOptions as $option)
                                <option value="{{ $option }}" @selected(old('status', $booking->status) === $option)>
                                    {{ str($option)->replace('_', ' ')->title() }}
                                </option>
                            @endforeach
                        </select>
                        @error('status') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="{{ $saveBtn }}">Save</button>
                    </div>
                </form>
            @else
                <p class="rounded-lg bg-slate-100 p-3 text-sm text-slate-600">
                    You can update this booking once the office has confirmed it.
                </p>
            @endif
        </section>

        @unless ($isStaff)
            <section class="flex flex-col gap-4 rounded-xl bg-white p-6 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="{{ $cardIcon }}"><i class="bi bi-person-gear"></i></span>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Assign technician</h2>
                        <p class="text-xs text-slate-500">One technician per job</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.bookings.technician', $booking) }}" class="flex flex-col gap-4">
                    @csrf
                    @method('PATCH')

                    <div class="flex flex-col gap-1">
                        <label for="technician_id" class="text-xs font-semibold text-slate-500">Staff member</label>
                        <select id="technician_id" name="technician_id" class="{{ $select }}">
                            <option value="">Unassigned</option>
                            @foreach ($technicians as $tech)
                                <option value="{{ $tech->id }}" @selected((int) old('technician_id', $booking->technician_id) === $tech->id)>
                                    {{ $tech->name }}{{ $tech->is_active ? '' : ' (inactive)' }}
                                </option>
                            @endforeach
                        </select>
                        @error('technician_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                        @if ($technicians->isEmpty())
                            <p class="text-xs text-slate-500">No staff users yet. Add them on the Users screen.</p>
                        @endif
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="{{ $saveBtn }}">Save</button>
                    </div>
                </form>
            </section>
        @endunless
    </div>
</div>

@endsection