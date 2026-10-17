@extends('layouts.admin')

@section('title', 'Quotes')

@section('content')

@php
    $tabs = array_merge([null], \App\Models\QuoteRequest::STATUSES);
    $hasFilters = $search !== '' || $serviceId || $date;
    $field = 'h-10 w-full rounded-lg bg-slate-100 text-sm text-slate-800 placeholder:text-slate-400 transition-colors focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue';
@endphp

<x-admin.page-header title="Quotes" :crumbs="['Quotes']" :subtitle="$counts->sum() . ' requests in total'" />

{{-- Status tabs --}}
<div class="mb-4 flex items-center gap-1 overflow-x-auto pb-2">
    @foreach ($tabs as $tab)
        <a href="{{ route('admin.quotes.index', array_filter(['status' => $tab, 'q' => $search, 'service' => $serviceId, 'date' => $date])) }}"
           @class([
               'shrink-0 whitespace-nowrap rounded-lg px-4 py-1.5 text-sm font-semibold transition-colors',
               'bg-brand-tint text-brand-navy' => $status === $tab,
               'bg-white text-slate-600 hover:bg-slate-100' => $status !== $tab,
           ])>
            {{ $tab ? ucfirst($tab) : 'All' }}
            <span class="ml-1 opacity-70">({{ $tab ? ($counts[$tab] ?? 0) : $counts->sum() }})</span>
        </a>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.quotes.index') }}" class="mb-6 rounded-xl bg-white p-4 shadow-sm">
    @if ($status)
        <input type="hidden" name="status" value="{{ $status }}">
    @endif

    <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
        <div class="relative md:col-span-5">
            <i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="q" value="{{ $search }}" aria-label="Search quotes"
                   placeholder="Search by ref, name, phone or location" class="{{ $field }} pl-10 pr-3">
        </div>

        <div class="md:col-span-3">
            <select name="service" aria-label="Filter by service" class="{{ $field }} cursor-pointer px-3">
                <option value="">All services</option>
                @foreach ($categories as $category)
                    @if ($category->services->isNotEmpty())
                        <optgroup label="{{ $category->name }}">
                            @foreach ($category->services as $service)
                                <option value="{{ $service->id }}" @selected($serviceId === $service->id)>{{ $service->name }}</option>
                            @endforeach
                        </optgroup>
                    @endif
                @endforeach
            </select>
        </div>

        <div class="md:col-span-2">
            <input type="date" name="date" value="{{ $date }}" aria-label="Filter by date" class="{{ $field }} px-3">
        </div>

        <div class="flex gap-2 md:col-span-2">
            <button type="submit"
                    class="h-10 flex-1 rounded-lg bg-brand-blue px-4 text-sm font-semibold text-white transition-colors hover:bg-brand-blue-hover">
                Filter
            </button>
            @if ($hasFilters)
                <a href="{{ route('admin.quotes.index', array_filter(['status' => $status])) }}"
                   class="flex h-10 items-center rounded-lg bg-slate-100 px-3 text-sm font-semibold text-slate-600 hover:bg-slate-200">Clear</a>
            @endif
        </div>
    </div>
</form>

{{-- Table --}}
<div class="mb-6 overflow-hidden rounded-xl bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left text-sm">
            <thead>
                <tr class="bg-slate-100 text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <th class="px-6 py-4" scope="col">Ref</th>
                    <th class="px-4 py-4" scope="col">Date</th>
                    <th class="px-4 py-4" scope="col">Name</th>
                    <th class="px-4 py-4" scope="col">Phone</th>
                    <th class="px-4 py-4" scope="col">Service</th>
                    <th class="px-4 py-4" scope="col">Location</th>
                    <th class="px-4 py-4" scope="col">Status</th>
                    <th class="px-6 py-4 text-right" scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($quotes as $quote)
                    <tr class="transition-colors even:bg-slate-50/60 hover:bg-slate-100/70">
                        <td class="whitespace-nowrap px-6 py-4 font-mono font-semibold text-brand-blue">{{ $quote->ref }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-slate-500">{{ friendly_date($quote->created_at) }}</td>
                        <td class="px-4 py-4 font-semibold text-slate-900">{{ $quote->name }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-slate-500">{{ $quote->phone }}</td>
                        <td class="max-w-xs truncate px-4 py-4" title="{{ $quote->service?->name }}">{{ $quote->service?->name ?? 'Not sure' }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-slate-500">{{ $quote->location }}</td>
                        <td class="whitespace-nowrap px-4 py-4"><x-admin.status-badge :status="$quote->status" /></td>
                        <td class="whitespace-nowrap px-6 py-4 text-right">
                            <a href="{{ route('admin.quotes.show', $quote) }}"
                               class="inline-flex items-center gap-1 font-semibold text-brand-blue transition-colors hover:text-brand-blue-hover">
                                View <i class="bi bi-chevron-right text-xs"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-500">No quote requests match.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $quotes->links() }}

@endsection