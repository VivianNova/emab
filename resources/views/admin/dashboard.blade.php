@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

@php
    $cards = [
        ['New quotes', $stats['new_quotes'], 'bi-file-earmark-text', url('/admin/quotes?status=new')],
        ['Upcoming bookings', $stats['upcoming_bookings'], 'bi-calendar-check', url('/admin/bookings')],
        ['Unread messages', $stats['unread_messages'], 'bi-envelope-exclamation', url('/admin/messages')],
    ];

    $tabs = array_merge([null], \App\Models\QuoteRequest::STATUSES);
    $linkClass = 'inline-flex items-center gap-1 text-sm font-semibold text-brand-blue transition-colors hover:text-brand-blue-hover';
@endphp

<x-admin.page-header title="Overview" :crumbs="['Dashboard']">
    <div class="flex items-center gap-2 rounded-xl bg-white px-4 py-1.5 text-xs font-semibold text-slate-600 shadow-sm">
        <i class="bi bi-calendar3 text-brand-blue"></i> {{ now()->format('l, j F Y') }}
    </div>
</x-admin.page-header>

{{-- Cards --}}
<section class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($cards as [$label, $count, $icon, $href])
        <div class="flex flex-col justify-between rounded-xl bg-white p-4 shadow-sm">
            <div class="mb-3 flex items-start justify-between">
                <span class="text-sm font-semibold text-slate-500">{{ $label }}</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-tint text-brand-navy">
                    <i class="bi {{ $icon }} text-lg"></i>
                </span>
            </div>
            <p class="mb-4 text-3xl font-bold leading-none text-slate-900">{{ $count }}</p>
            <a href="{{ $href }}" class="{{ $linkClass }}">View <i class="bi bi-arrow-right"></i></a>
        </div>
    @endforeach
</section>

{{-- Today's bookings --}}
<section class="mb-6 rounded-xl bg-white p-4 shadow-sm sm:p-6">
    <div class="mb-4 flex flex-col justify-between gap-2 rounded-lg bg-slate-100 px-4 py-3 sm:flex-row sm:items-center">
        <div class="flex items-center gap-3">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-brand-blue text-white"><i class="bi bi-calendar-event"></i></span>
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Today's bookings</h2>
                <p class="text-sm text-slate-500">{{ now()->format('l, j F Y') }}</p>
            </div>
        </div>
        <a href="{{ url('/admin/bookings') }}" class="{{ $linkClass }}">View all bookings <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="space-y-1">
        @forelse ($todaysBookings as $booking)
            <div class="flex flex-col justify-between gap-3 rounded-xl bg-slate-50 p-4 transition-colors hover:bg-slate-100 md:flex-row md:items-center">
                <div class="flex min-w-0 items-center gap-4">
                    <div class="flex min-w-[130px] flex-col text-xs font-semibold">
                        <span class="text-slate-500">{{ $booking->slot['label'] ?? '' }} {{ isset($booking->slot['hours']) ? '· ' . $booking->slot['hours'] : '' }}</span>
                        <span class="text-brand-blue">{{ $booking->ref }}</span>
                    </div>
                    <div class="flex min-w-0 flex-col">
                        <span class="truncate text-lg font-semibold text-slate-900">{{ $booking->name }}</span>
                        <span class="truncate text-sm text-slate-500">{{ $booking->service?->name ?? 'Not specified' }}</span>
                    </div>
                </div>

                <div class="flex shrink-0 items-center justify-between gap-6 md:justify-end">
                    <span class="flex items-center gap-1.5 text-sm text-slate-600">
                        <i class="bi bi-person-gear text-slate-400"></i>
                        {{ $booking->technician?->name ?? 'Unassigned' }}
                    </span>
                    <x-admin.status-badge :status="$booking->status" />
                </div>
            </div>
        @empty
            <p class="rounded-xl bg-slate-50 p-6 text-center text-sm text-slate-500">No bookings today.</p>
        @endforelse
    </div>
</section>

{{-- Latest quote requests --}}
<section class="rounded-xl bg-white p-4 shadow-sm sm:p-6">
    <div class="mb-4 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div class="flex items-center gap-3">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-brand-blue text-white"><i class="bi bi-receipt"></i></span>
            <h2 class="text-lg font-semibold text-slate-900">Latest quote requests</h2>
        </div>
        <a href="{{ url('/admin/quotes') }}" class="{{ $linkClass }}">View all quotes <i class="bi bi-arrow-right"></i></a>
    </div>

    {{-- Status tabs --}}
    <div class="mb-4 flex items-center gap-1 overflow-x-auto pb-2">
        @foreach ($tabs as $tab)
            <a href="{{ route('admin.dashboard', $tab ? ['status' => $tab] : []) }}"
               @class([
                   'whitespace-nowrap rounded-xl px-4 py-1.5 text-sm font-semibold transition-colors',
                   'bg-brand-blue text-white' => $status === $tab,
                   'bg-slate-100 text-slate-600 hover:bg-slate-200' => $status !== $tab,
               ])>{{ $tab ? ucfirst($tab) : 'All' }}</a>
        @endforeach
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-slate-100 text-xs font-semibold text-slate-500">
                    <th class="rounded-l-lg px-4 py-2">Ref</th>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Service</th>
                    <th class="px-4 py-2">Location</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="rounded-r-lg px-4 py-2 text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($quotes as $quote)
                    <tr class="transition-colors hover:bg-slate-50">
                        <td class="whitespace-nowrap px-4 py-4 font-semibold text-brand-blue">{{ $quote->ref }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-slate-500">{{ friendly_date($quote->created_at) }}</td>
                        <td class="px-4 py-4 font-semibold text-slate-900">{{ $quote->name }}</td>
                        <td class="px-4 py-4 text-slate-600">{{ $quote->service?->name ?? 'Not sure' }}</td>
                        <td class="px-4 py-4 text-slate-500">{{ $quote->location }}</td>
                        <td class="px-4 py-4"><x-admin.status-badge :status="$quote->status" /></td>
                        <td class="px-4 py-4 text-right">
                            <a href="{{ url('/admin/quotes/' . $quote->id) }}"
                               class="inline-flex items-center rounded-lg bg-brand-tint px-3 py-1 text-xs font-semibold text-brand-navy transition-colors hover:bg-brand-blue hover:text-white">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-slate-500">No quote requests yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $quotes->links() }}</div>
</section>

@endsection