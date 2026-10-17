@extends('layouts.admin')

@section('title', 'Projects')

@section('content')

@php
    $field = 'h-10 w-full rounded-lg bg-slate-100 text-sm text-slate-800 placeholder:text-slate-400 transition-colors focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue';
    $hasFilters = $search !== '' || $activeCategory;
@endphp

<x-admin.page-header title="Projects" :crumbs="['Projects']" subtitle="Completed work shown on the public website">
    <a href="{{ route('admin.projects.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-brand-blue px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-blue-hover">
        <i class="bi bi-plus-lg"></i> Add Project
    </a>
</x-admin.page-header>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.projects.index') }}" class="mb-4 rounded-xl bg-white p-4 shadow-sm">
    <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
        <div class="relative md:col-span-6">
            <i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="q" value="{{ $search }}" aria-label="Search projects"
                   placeholder="Search by title, description or location" class="{{ $field }} pl-10 pr-3">
        </div>

        <div class="md:col-span-4">
            <select name="category" aria-label="Filter by category" class="{{ $field }} cursor-pointer px-3">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->slug }}" @selected($activeCategory?->id === $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-2 md:col-span-2">
            <button type="submit"
                    class="h-10 flex-1 rounded-lg bg-brand-blue px-4 text-sm font-semibold text-white transition-colors hover:bg-brand-blue-hover">
                Filter
            </button>
            @if ($hasFilters)
                <a href="{{ route('admin.projects.index') }}"
                   class="flex h-10 items-center rounded-lg bg-slate-100 px-3 text-sm font-semibold text-slate-600 hover:bg-slate-200">Clear</a>
            @endif
        </div>
    </div>
</form>

{{-- Table --}}
<div class="mb-6 overflow-hidden rounded-xl bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-slate-100 text-xs font-semibold text-slate-500">
                    <th class="w-16 py-3.5 pl-6 pr-3 text-center" scope="col">Photo</th>
                    <th class="px-4 py-3.5" scope="col">Title</th>
                    <th class="px-4 py-3.5" scope="col">Category</th>
                    <th class="px-4 py-3.5" scope="col">Location</th>
                    <th class="px-4 py-3.5" scope="col">Completed</th>
                    <th class="px-4 py-3.5" scope="col">Photos</th>
                    <th class="py-3.5 pl-4 pr-6 text-right" scope="col">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($projects as $project)
                    <tr class="group transition-colors hover:bg-slate-50">
                        <td class="py-3.5 pl-6 pr-3">
                            @if ($project->coverImage)
                                <img src="{{ $project->cover_url }}" alt="" loading="lazy"
                                     class="mx-auto h-11 w-11 rounded-lg bg-slate-100 object-cover shadow-sm">
                            @else
                                <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-lg bg-slate-100 text-lg text-slate-400">
                                    <i class="bi bi-image"></i>
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 font-semibold text-slate-900 transition-colors group-hover:text-brand-blue">{{ $project->title }}</td>
                        <td class="px-4 py-3.5">
                            @if ($project->service?->category)
                                <span class="inline-flex whitespace-nowrap rounded-full bg-brand-tint px-2.5 py-1 text-xs font-semibold text-brand-navy">
                                    {{ $project->service->category->name }}
                                </span>
                            @else
                                <span class="text-slate-400">No service</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-slate-500">
                            @if ($project->location)
                                <span class="flex items-center gap-1.5"><i class="bi bi-geo-alt text-slate-400"></i> {{ $project->location }}</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3.5 text-slate-500">{{ $project->completed_on?->format('M Y') }}</td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1 whitespace-nowrap rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                <i class="bi bi-images"></i>
                                {{ $project->images_count }} {{ str('photo')->plural($project->images_count) }}
                            </span>
                        </td>
                        <td class="py-3.5 pl-4 pr-6 text-right">
                            <a href="{{ route('admin.projects.edit', $project) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-sm font-semibold text-brand-blue transition-colors hover:bg-brand-blue hover:text-white">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">No projects match.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $projects->links() }}

@endsection