@extends('layouts.admin')

@section('title', $category->exists ? 'Edit Category' : 'Add Category')

@section('content')

@php
    $editing = $category->exists;
    $label = 'flex items-center gap-1 text-sm font-semibold text-slate-800';
    $field = 'w-full rounded-lg bg-slate-100 px-4 text-sm text-slate-800 placeholder:text-slate-400 transition-colors focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue';
    $help = 'text-xs text-slate-500';
@endphp

<x-admin.page-header :title="$editing ? 'Edit Category' : 'Add Category'"
                     :crumbs="['Categories' => route('admin.categories.index'), $editing ? 'Edit Category' : 'Add Category']"
                     subtitle="Name, page address, short description, icon and position.">
    <a href="{{ route('admin.categories.index') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-800 transition-colors hover:bg-slate-300">
        <i class="bi bi-arrow-left"></i> Back to Categories
    </a>
</x-admin.page-header>

<form method="POST"
      action="{{ $editing ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
      class="flex max-w-4xl flex-col gap-6 rounded-xl bg-white p-6 shadow-sm md:p-8">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div class="flex flex-col gap-1">
        <label for="name" class="{{ $label }}">Name <span class="text-red-600">*</span></label>
        <input id="name" name="name" type="text" required value="{{ old('name', $category->name) }}"
               data-slug-source="#slug" placeholder="e.g. Solar & Renewable Energy" class="{{ $field }} h-10">
        <span class="{{ $help }}">Shown on the Home page and as the heading above the services.</span>
        @error('name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex flex-col gap-1">
        <label for="slug" class="{{ $label }}">Slug <span class="text-red-600">*</span></label>
        <div class="flex h-10 items-center overflow-hidden rounded-lg bg-slate-100 focus-within:ring-2 focus-within:ring-brand-blue">
            <span class="flex h-full select-none items-center whitespace-nowrap bg-slate-200 px-4 text-sm font-semibold text-slate-600">/services?category=</span>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $category->slug) }}"
                   placeholder="solar-renewable-energy"
                   class="h-full w-full bg-transparent px-4 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none">
        </div>
        <span class="{{ $help }}">Filled in from the name. Changing it later breaks old links to this category's filter.</span>
        @error('slug') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex flex-col gap-1">
        <label for="description" class="{{ $label }}">Description</label>
        <textarea id="description" name="description" rows="3" maxlength="200"
                  placeholder="One short sentence about this group of services."
                  class="{{ $field }} resize-y p-4">{{ old('description', $category->description) }}</textarea>
        <span class="{{ $help }}">Shown on the Home page card. Keep it to one short sentence (200 characters at most).</span>
        @error('description') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 items-start gap-6 md:grid-cols-2">
        <div class="flex flex-col gap-1">
            <label for="icon" class="{{ $label }}">Icon</label>
            <div class="flex items-center gap-3">
                <input id="icon" name="icon" type="text" value="{{ old('icon', $category->icon) }}"
                       data-icon-source="#icon-preview" placeholder="e.g. bi-sun, bi-house"
                       class="{{ $field }} h-10 flex-1">
                <span id="icon-preview" title="Icon preview"
                      class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-tint text-xl text-brand-navy shadow-sm">
                    <i class="bi {{ old('icon', $category->icon ?: 'bi-tag') }}"></i>
                </span>
            </div>
            <span class="{{ $help }}">
                A Bootstrap Icons name. Find one at
                <a href="https://icons.getbootstrap.com" target="_blank" rel="noopener noreferrer" class="font-semibold text-brand-blue hover:underline">icons.getbootstrap.com</a>.
                Left empty, it uses a plain tag icon.
            </span>
            @error('icon') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex flex-col gap-1">
            <label for="sort_order" class="{{ $label }}">Sort order</label>
            <input id="sort_order" name="sort_order" type="number" min="0" max="999" step="1"
                   value="{{ old('sort_order', $category->sort_order) }}" class="{{ $field }} h-10">
            <span class="{{ $help }}">Position on the website. Lower numbers come first.</span>
            @error('sort_order') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="flex items-center justify-end gap-2 pt-2">
        <a href="{{ route('admin.categories.index') }}"
           class="rounded-lg px-6 py-2.5 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-100">Cancel</a>
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-blue px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-blue-hover">
            <i class="bi bi-check-lg"></i> Save Category
        </button>
    </div>
</form>

@endsection