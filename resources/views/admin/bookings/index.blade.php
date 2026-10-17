@extends('layouts.admin')

@section('title', 'Bookings')

@section('content')

@php
    $isStaff = auth()->user()->isStaff();
    $tabs = array_merge([null], \App\Models\Booking::STATUSES);
    $label = fn ($s) => str($s)->replace('_', ' ')->title();
    $hasFilters = $date || $serviceId || $technician;
    $field = 'h-10 w-full rounded-lg bg-slate-100 px-3 text-sm text-slate-800 transition-colors focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue';
    $filterLabel = 'text-xs font-semibold text-slate-500';
@endphp

<x-admin.page-header title="Bookings" :crumbs="['Bookings']"
                     :subtitle="$isStaff ? 'Bookings assigned to you' : $counts->sum() . ' bookings in total'" />

{{-- Status tabs --}}
<div class="mb-4 flex items-center gap-1 overflow-x-auto pb-2">
    @foreach ($tabs as $tab)
        <a href="{{ route('admin.bookings.index', array_filter(['status' => $tab, 'date' => $date, 'service' => $serviceId, 'technician' => $technician])) }}"
           @class([
               'shrink-0 whitespace-nowrap rounded-lg px-4 py-1.5 text-sm font-semibold transition-colors',
               'bg-brand-tint text-brand-navy' => $status === $tab,
               'bg-white text-slate-600 hover:bg-slate-100' => $status !== $tab,
           ])>
            {{ $tab ? $label($tab) : 'All' }}
            <span class="ml-1 opacity-70">({{ $tab ? ($counts[$tab] ?? 0) : $counts->sum() }})</span>
        </a>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.bookings.index') }}" class="mb-6 rounded-xl bg-white p-4 shadow-sm">
    @if ($status)
        <input type="hidden" name="status" value="{{ $status }}">
    @endif

    <div @class(['grid grid-cols-1 gap-3 md:grid-cols-2', 'lg:grid-cols-4' => ! $isStaff, 'lg:grid-cols-3' => $isStaff])>
        <div class="flex flex-col gap-1">
            <label for="date" class="{{ $filterLabel }}">Date</label>
            <input id="date" type="date" name="date" value="{{ $date }}" class="{{ $field }}">
        </div>

        <div class="flex flex-col gap-1">
            <label for="service" class="{{ $filterLabel }}">Service</label>
            <select id="service" name="service" class="{{ $field }} cursor-pointer">
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

        @unless ($isStaff)
            <div class="flex flex-col gap-1">
                <label for="technician" class="{{ $filterLabel }}">Technician</label>
                <select id="technician" name="technician" class="{{ $field }} cursor-pointer">
                    <option value="">All technicians</option>
                    <option value="unassigned" @selected($technician === 'unassigned')>Unassigned</option>
                    @foreach ($technicians as $tech)
                        <option value="{{ $tech->id }}" @selected((string) $technician === (string) $tech->id)>{{ $tech->name }}</option>
                    @endforeach
                </select>
            </div>
        @endunless

        <div class="flex items-end gap-2">
            <button type="submit"
                    class="h-10 flex-1 rounded-lg bg-brand-blue px-4 text-sm font-semibold text-white transition-colors hover:bg-brand-blue-hover">
                Filter
            </button>
            @if ($hasFilters)
                <a href="{{ route('admin.bookings.index', array_filter(['status' => $status])) }}"
                   class="flex h-10 items-center rounded-lg bg-slate-100 px-3 text-sm font-semibold text-slate-600 hover:bg-slate-200">Clear</a>
            @endif
        </div>
    </div>
</form>

{{-- Table --}}
<div class="mb-6 overflow-hidden rounded-xl bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full whitespace-nowrap text-left text-sm">
            <thead>
                <tr class="bg-slate-100 text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <th class="px-4 py-3" scope="col">Ref</th>
                    <th class="px-4 py-3" scope="col">Date</th>
                    <th class="px-4 py-3" scope="col">Time slot</th>
                    <th class="px-4 py-3" scope="col">Client</th>
                    <th class="px-4 py-3" scope="col">Service</th>
                    <th class="px-4 py-3" scope="col">Address</th>
                    <th class="px-4 py-3" scope="col">Technician</th>
                    <th class="px-4 py-3" scope="col">Status</th>
                    <th class="px-4 py-3 text-right" scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $booking)
                    <tr class="transition-colors even:bg-slate-50/60 hover:bg-slate-100/70">
                        <td class="px-4 py-3.5 font-semibold text-brand-blue">{{ $booking->ref }}</td>
                        <td class="px-4 py-3.5 font-medium text-slate-800">{{ $booking->preferred_date->format('d M Y') }}</td>
                        <td class="px-4 py-3.5 text-slate-500">{{ $booking->slot['label'] ?? '' }}</td>
                        <td class="px-4 py-3.5 font-semibold text-slate-900">{{ $booking->name }}</td>
                        <td class="max-w-[14rem] truncate px-4 py-3.5 text-slate-500" title="{{ $booking->service?->name }}">{{ $booking->service?->name ?? 'Not sure' }}</td>
                        <td class="max-w-[14rem] truncate px-4 py-3.5 text-slate-500" title="{{ $booking->address }}">{{ $booking->address }}</td>
                        <td class="px-4 py-3.5">
                            @if ($booking->technician)
                                <span class="flex items-center gap-2">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-brand-tint text-xs font-semibold text-brand-navy">
                                        {{ str($booking->technician->name)->explode(' ')->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('') }}
                                    </span>
                                    {{ $booking->technician->name }}
                                </span>
                            @else
                                <span class="flex items-center gap-2 italic text-slate-400"><i class="bi bi-person-dash"></i> Unassigned</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5"><x-admin.status-badge :status="$booking->status" /></td>
                        <td class="px-4 py-3.5 text-right">
                            <a href="{{ route('admin.bookings.show', $booking) }}"
                               class="inline-flex items-center gap-1 rounded bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-800 transition-colors hover:bg-slate-200">
                                View <i class="bi bi-arrow-right"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-slate-500">No bookings match.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $bookings->links() }}

@endsection