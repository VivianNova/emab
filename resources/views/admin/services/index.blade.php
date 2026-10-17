@extends('layouts.admin')

@section('title', 'Services')

@section('content')

<x-admin.page-header title="Services" :crumbs="['Services']" subtitle="Manage the services shown on the public website">
    <a href="{{ route('admin.services.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-brand-blue px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-blue-hover">
        <i class="bi bi-plus-lg"></i> Add Service
    </a>
</x-admin.page-header>

{{-- Search --}}
<form method="GET" action="{{ route('admin.services.index') }}" class="mb-4 rounded-xl bg-white p-4 shadow-sm">
    <div class="relative max-w-md">
        <i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input type="text" name="q" value="{{ $search }}" aria-label="Search services" placeholder="Search services..."
               class="w-full rounded-lg bg-slate-100 py-2 pl-10 pr-4 text-sm text-slate-800 placeholder:text-slate-400 transition-colors focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue">
    </div>
</form>

{{-- Services table --}}
<div class="mb-6 overflow-hidden rounded-xl bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left">
            <thead>
                <tr class="bg-slate-100 text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <th class="px-4 py-3.5" scope="col">Name</th>
                    <th class="px-4 py-3.5" scope="col">Category</th>
                    <th class="w-28 px-4 py-3.5 text-center" scope="col">Active</th>
                    <th class="w-44 px-4 py-3.5 text-right" scope="col">Actions</th>
                </tr>
            </thead>
            <tbody class="text-slate-800">
                @forelse ($services as $service)
                    <tr class="transition-colors even:bg-slate-50/60 hover:bg-slate-100/60">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3 font-semibold">
                                @if ($service->image_url)
                                    <img src="{{ $service->image_url }}" alt="" class="h-10 w-10 rounded-lg object-cover">
                                @endif
                                <span>{{ $service->name }}</span>
                            </div>
                        </td>

                        <td class="px-4 py-4">
                            <span class="inline-flex rounded bg-brand-tint px-2.5 py-0.5 text-xs font-semibold text-brand-navy">
                                {{ $service->category->name }}
                            </span>
                        </td>

                        <td class="px-4 py-4 text-center">
                            <form method="POST" action="{{ route('admin.services.toggle', $service) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" role="switch"
                                        aria-checked="{{ $service->is_active ? 'true' : 'false' }}"
                                        aria-label="{{ $service->is_active ? 'Hide' : 'Show' }} {{ $service->name }}"
                                        @class([
                                            'relative inline-flex h-5 w-10 items-center rounded-full transition-colors',
                                            'bg-brand-blue' => $service->is_active,
                                            'bg-slate-300' => ! $service->is_active,
                                        ])>
                                    <span @class([
                                        'inline-block h-4 w-4 rounded-full bg-white transition-transform',
                                        'translate-x-5' => $service->is_active,
                                        'translate-x-0.5' => ! $service->is_active,
                                    ])></span>
                                </button>
                            </form>
                        </td>

                        <td class="px-4 py-4">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.services.edit', $service) }}"
                                   class="inline-flex items-center gap-1 rounded px-2.5 py-1.5 text-sm font-semibold text-brand-blue transition-colors hover:bg-slate-100">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>

                                <form method="POST" action="{{ route('admin.services.destroy', $service) }}"
                                      onsubmit="return confirm(@js('Delete ' . $service->name . '? This cannot be undone.'))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 rounded px-2.5 py-1.5 text-sm font-semibold text-brand-red transition-colors hover:bg-slate-100">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500">No services match.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $services->links() }}

@endsection