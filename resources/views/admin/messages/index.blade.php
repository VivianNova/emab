@extends('layouts.admin')

@section('title', 'Messages')

@section('content')

@php
    $tabs = ['all' => 'All', 'unread' => 'Unread', 'read' => 'Read'];
@endphp

<x-admin.page-header title="Messages" :crumbs="['Messages']" subtitle="Messages sent from the contact form" />

{{-- Tabs and search --}}
<div class="mb-6 flex flex-col items-stretch justify-between gap-4 rounded-xl bg-white p-4 shadow-sm md:flex-row md:items-center">

    <div class="flex items-center gap-1 rounded-xl bg-slate-100 p-1">
        @foreach ($tabs as $key => $name)
            @php
                $status = $key === 'all' ? null : $key;
                $count = $counts[$key];
            @endphp
            <a href="{{ route('admin.messages.index', array_filter(['status' => $status, 'q' => $search])) }}"
               @class([
                   'flex items-center gap-2 rounded-lg px-4 py-1.5 text-sm font-semibold transition-all',
                   'bg-brand-blue text-white shadow-sm' => $filter === $status,
                   'text-slate-600 hover:text-slate-900' => $filter !== $status,
               ])>
                {{ $name }}
                <span @class([
                    'rounded-full px-1.5 text-xs',
                    'bg-white/20 text-white' => $filter === $status,
                    'bg-slate-200 text-slate-600' => $filter !== $status,
                ])>{{ $count }}</span>
            </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('admin.messages.index') }}" class="relative w-full md:w-96">
        @if ($filter)
            <input type="hidden" name="status" value="{{ $filter }}">
        @endif
        <i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input type="text" name="q" value="{{ $search }}" aria-label="Search messages"
               placeholder="Search by name, email, phone or subject"
               class="w-full rounded-lg bg-slate-100 py-2 pl-10 pr-4 text-sm text-slate-800 placeholder:text-slate-400 transition-colors focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue">
    </form>
</div>

{{-- Table --}}
<div class="mb-6 overflow-hidden rounded-xl bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] border-collapse text-left text-sm">
            <thead>
                <tr class="bg-slate-100 text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <th class="w-10 py-3.5 pl-5 pr-2 text-center" scope="col"><span class="sr-only">Unread</span></th>
                    <th class="px-4 py-3.5" scope="col">Date</th>
                    <th class="px-4 py-3.5" scope="col">Name</th>
                    <th class="px-4 py-3.5" scope="col">Email</th>
                    <th class="px-4 py-3.5" scope="col">Phone</th>
                    <th class="px-4 py-3.5" scope="col">Subject</th>
                    <th class="px-4 py-3.5 text-center" scope="col">Status</th>
                    <th class="py-3.5 pl-4 pr-6 text-right" scope="col">View</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($messages as $message)
                    <tr @class([
                        'transition-colors hover:bg-slate-50',
                        'font-semibold text-slate-900' => ! $message->is_read,
                        'text-slate-500' => $message->is_read,
                    ])>
                        <td class="py-4 pl-5 pr-2 text-center">
                            @unless ($message->is_read)
                                <span class="inline-block h-2.5 w-2.5 rounded-full bg-brand-blue" title="Unread"></span>
                            @endunless
                        </td>
                        <td class="whitespace-nowrap px-4 py-4">{{ friendly_date($message->created_at) }}</td>
                        <td class="whitespace-nowrap px-4 py-4">
                            <span class="flex items-center gap-3">
                                <span @class([
                                    'flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-semibold',
                                    'bg-brand-tint text-brand-navy' => ! $message->is_read,
                                    'bg-slate-100 text-slate-500' => $message->is_read,
                                ])>{{ $message->initials }}</span>
                                {{ $message->name }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-4 font-normal text-slate-500">{{ $message->email }}</td>
                        <td class="whitespace-nowrap px-4 py-4 font-normal text-slate-500">{{ $message->phone ?? '-' }}</td>
                        <td class="px-4 py-4">{{ $message->subject }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-center">
                            @if ($message->is_read)
                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-500">Read</span>
                            @else
                                <span class="inline-flex rounded-full bg-brand-tint px-2.5 py-0.5 text-xs font-semibold text-brand-navy">Unread</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap py-4 pl-4 pr-6 text-right">
                            <a href="{{ route('admin.messages.show', $message) }}"
                               class="inline-flex items-center gap-1 font-semibold text-brand-blue transition-colors hover:text-brand-blue-hover">
                                View <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center font-normal text-slate-500">No messages match.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $messages->links() }}

@endsection