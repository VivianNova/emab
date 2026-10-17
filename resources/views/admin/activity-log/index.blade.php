@extends('layouts.admin')

@section('content')
<x-admin.page-header title="Activity Log" :crumbs="['Admin', 'Activity Log']" subtitle="Who did what, and when" />

<form method="GET" action="{{ route('admin.activity-log.index') }}"
      class="bg-white rounded-xl shadow-sm p-4 mb-6 grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
    <div class="md:col-span-5">
        <label for="q" class="text-sm font-semibold text-slate-700">Search</label>
        <div class="relative mt-1">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Search by description or user"
                   class="w-full pl-10 pr-4 py-2 bg-slate-100 text-sm rounded-lg focus:ring-2 focus:ring-brand-blue focus:outline-none">
        </div>
        @error('q') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-3">
        <label for="action" class="text-sm font-semibold text-slate-700">Action</label>
        <select id="action" name="action"
                class="mt-1 w-full px-3 py-2 bg-slate-100 text-sm rounded-lg focus:ring-2 focus:ring-brand-blue focus:outline-none">
            <option value="">All actions</option>
            @foreach ($actions as $action)
                <option value="{{ $action }}" @selected(request('action') === $action)>
                    {{ \Illuminate\Support\Str::headline($action) }}
                </option>
            @endforeach
        </select>
        @error('action') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label for="date" class="text-sm font-semibold text-slate-700">Date</label>
        <input type="date" id="date" name="date" value="{{ request('date') }}"
               class="mt-1 w-full px-3 py-2 bg-slate-100 text-sm rounded-lg focus:ring-2 focus:ring-brand-blue focus:outline-none">
        @error('date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2 flex items-center gap-3">
        <button type="submit" class="bg-brand-blue hover:bg-brand-blue-hover text-white text-sm font-semibold px-4 py-2 rounded-lg">
            Filter
        </button>
        @if (request()->hasAny(['q', 'action', 'date']))
            <a href="{{ route('admin.activity-log.index') }}" class="text-sm text-slate-600 hover:text-brand-navy">Clear</a>
        @endif
    </div>
</form>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-100 text-slate-600">
                <tr>
                    <th class="py-3 px-5 font-semibold whitespace-nowrap">Date and time</th>
                    <th class="py-3 px-5 font-semibold">User</th>
                    <th class="py-3 px-5 font-semibold">Action</th>
                    <th class="py-3 px-5 font-semibold">Description</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($logs as $log)
                    @php
                        [$icon, $badge] = match ($log->action) {
                            'status_change'       => ['bi-arrow-left-right', 'bg-brand-tint text-brand-navy'],
                            'technician_assigned' => ['bi-person-gear', 'bg-brand-tint text-brand-navy'],
                            'deleted'             => ['bi-trash', 'bg-red-100 text-brand-red'],
                            'settings_changed'    => ['bi-sliders', 'bg-slate-100 text-slate-700'],
                            'user_changed'        => ['bi-people', 'bg-slate-100 text-slate-700'],
                            'login'               => ['bi-key', 'bg-slate-100 text-slate-700'],
                            default               => ['bi-clock-history', 'bg-slate-100 text-slate-700'],
                        };
                        $name = $log->user?->name ?? 'System';
                        $initials = collect(explode(' ', $name))->take(2)
                            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
                    @endphp
                    <tr class="{{ $log->action === 'deleted' ? 'bg-red-50' : 'hover:bg-slate-50' }}">
                        <td class="py-3 px-5 whitespace-nowrap font-semibold text-slate-800">
                            {{ $log->created_at->format('d M Y, h:i A') }}
                        </td>
                        <td class="py-3 px-5 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-brand-navy text-white text-xs font-semibold flex items-center justify-center">
                                    {{ $initials }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-semibold text-slate-800 truncate">{{ $name }}</div>
                                    @if ($log->user)
                                        <div class="text-xs text-slate-500">{{ \Illuminate\Support\Str::headline($log->user->role) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-5 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                                <i class="bi {{ $icon }}"></i>
                                {{ \Illuminate\Support\Str::headline($log->action) }}
                            </span>
                        </td>
                        <td class="py-3 px-5 text-slate-700">{{ $log->description }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-10 px-5 text-center text-slate-500">No activity found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($logs->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection