@extends('layouts.admin')

@section('title', 'Categories')

@section('content')

<x-admin.page-header title="Categories" :crumbs="['Categories']" subtitle="These also appear as the filter tabs on the Services page">
    <a href="{{ route('admin.categories.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-brand-blue px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-blue-hover">
        <i class="bi bi-plus-lg"></i> Add Category
    </a>
</x-admin.page-header>

<div class="overflow-hidden rounded-xl bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left text-sm">
            <thead>
                <tr class="bg-slate-100 text-xs font-semibold text-slate-500">
                    <th class="w-20 px-4 py-3.5 text-center" scope="col">Icon</th>
                    <th class="min-w-[240px] px-4 py-3.5" scope="col">Name</th>
                    <th class="min-w-[220px] px-4 py-3.5" scope="col">Slug</th>
                    <th class="w-28 px-4 py-3.5 text-center" scope="col">Services</th>
                    <th class="w-24 px-4 py-3.5 text-center" scope="col">Order</th>
                    <th class="w-24 px-4 py-3.5 text-right" scope="col">Edit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-800">
                @forelse ($categories as $category)
                    <tr class="group transition-colors hover:bg-slate-50">
                        <td class="px-4 py-4">
                            <span class="mx-auto flex h-10 w-10 items-center justify-center rounded-lg bg-brand-tint text-xl text-brand-navy transition-colors group-hover:bg-brand-blue group-hover:text-white">
                                <i class="bi {{ $category->icon }}"></i>
                            </span>
                        </td>
                        <td class="px-4 py-4 font-semibold text-slate-900">{{ $category->name }}</td>
                        <td class="px-4 py-4">
                            <code class="rounded bg-slate-100 px-2 py-1 font-mono text-[13px] text-slate-600">{{ $category->slug }}</code>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <a href="{{ route('admin.services.index', ['q' => $category->name]) }}"
                               class="inline-flex h-7 min-w-[28px] items-center justify-center rounded-full bg-brand-tint px-2 text-xs font-semibold text-brand-navy transition-colors hover:bg-brand-blue hover:text-white"
                               title="See these services">{{ $category->services_count }}</a>
                        </td>
                        <td class="px-4 py-4 text-center font-semibold text-slate-500">{{ $category->sort_order }}</td>
                        <td class="px-4 py-4 text-right">
                            <a href="{{ route('admin.categories.edit', $category) }}" aria-label="Edit {{ $category->name }}"
                               class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-brand-tint hover:text-brand-navy">
                                <i class="bi bi-pencil text-lg"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">No categories yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-slate-100 px-4 py-3 text-xs font-semibold text-slate-500">
        {{ $categories->count() }} {{ str('category')->plural($categories->count()) }} in total. Lower numbers come first.
    </div>
</div>

@endsection