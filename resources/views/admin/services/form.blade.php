@extends('layouts.admin')

@section('title', $service->exists ? 'Edit Service' : 'Add Service')

@section('content')

@php
    $editing = $service->exists;
    $active = $errors->any() ? (bool) old('is_active') : ($editing ? $service->is_active : true);
    $label = 'flex items-center gap-1 text-sm font-semibold text-slate-800';
    $field = 'w-full rounded-lg bg-slate-100 px-4 text-sm text-slate-800 placeholder:text-slate-400 transition-colors focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue';
    $help = 'text-xs text-slate-500';
@endphp

<x-admin.page-header :title="$editing ? 'Edit Service' : 'Add Service'"
                     :crumbs="['Services' => route('admin.services.index'), $editing ? 'Edit Service' : 'Add Service']"
                     subtitle="Choose the category, then set the name, page address, description and picture.">
    <a href="{{ route('admin.services.index') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-800 transition-colors hover:bg-slate-300">
        <i class="bi bi-arrow-left"></i> Back to Services
    </a>
</x-admin.page-header>

<form method="POST" enctype="multipart/form-data"
      action="{{ $editing ? route('admin.services.update', $service) : route('admin.services.store') }}"
      class="flex max-w-4xl flex-col gap-6 rounded-xl bg-white p-6 shadow-sm">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="flex flex-col gap-1">
            <label for="service_category_id" class="{{ $label }}">Category <span class="text-red-600">*</span></label>
            <div class="relative">
                <select id="service_category_id" name="service_category_id" required class="{{ $field }} h-10 cursor-pointer appearance-none pr-10">
                    <option value="" disabled @selected(! old('service_category_id', $service->service_category_id))>Choose a category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) old('service_category_id', $service->service_category_id) === $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <i class="bi bi-chevron-down pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            </div>
            <span class="{{ $help }}">The group this service belongs to. It decides the filter tab on the website.</span>
            @error('service_category_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex flex-col gap-1">
            <label for="name" class="{{ $label }}">Service name <span class="text-red-600">*</span></label>
            <input id="name" name="name" type="text" required value="{{ old('name', $service->name) }}"
                   data-slug-source="#slug" placeholder="e.g. Solar PV design and installation" class="{{ $field }} h-10">
            <span class="{{ $help }}">The title customers see.</span>
            @error('name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="flex flex-col gap-1">
        <label for="slug" class="{{ $label }}">Page address <span class="text-red-600">*</span></label>
        <div class="flex items-center overflow-hidden rounded-lg bg-slate-100 focus-within:ring-2 focus-within:ring-brand-blue">
            <span class="select-none bg-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600">/services/</span>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $service->slug) }}"
                   placeholder="solar-pv-design-and-installation"
                   class="h-10 flex-1 bg-transparent px-4 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none">
        </div>
        <span class="{{ $help }}">
            Filled in for you from the name. Changing it later breaks old links to this service.
        </span>
        @error('slug') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex flex-col gap-1">
        <label for="description" class="{{ $label }}">Description <span class="text-red-600">*</span></label>
        <textarea id="description" name="description" rows="6" required
                  placeholder="What the service covers, in plain words."
                  class="{{ $field }} p-4 leading-relaxed">{{ old('description', $service->description) }}</textarea>
        <span class="{{ $help }}">Leave a blank line to start a new paragraph. This is the text on the service page.</span>
        @error('description') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex flex-col gap-2">
        <span class="{{ $label }}">Picture</span>

        <div class="grid grid-cols-1 items-start gap-4 md:grid-cols-3">
            <div class="flex flex-col gap-2 md:col-span-2">
                <input id="image" name="image" type="file" accept="image/png,image/jpeg,image/webp"
                       class="block w-full cursor-pointer rounded-lg bg-slate-100 text-sm text-slate-600 file:mr-4 file:cursor-pointer file:border-0 file:bg-brand-blue file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-blue-hover">
                <span class="{{ $help }}">JPG, PNG or WEBP, up to 5 MB. A wide picture (16:9) works best.</span>
                @error('image') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            @if ($editing && $service->image)
                <div class="flex flex-col gap-2 rounded-xl bg-slate-100 p-2">
                    <span class="px-1 text-xs font-semibold text-slate-500">Current picture</span>
                    <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}" class="h-32 w-full rounded object-cover">
                    <label class="flex cursor-pointer items-center gap-2 px-1 text-xs font-semibold text-brand-red">
                        <input type="checkbox" name="remove_image" value="1" class="h-4 w-4 accent-brand-red"> Remove current picture
                    </label>
                </div>
            @endif
        </div>
    </div>

    <div class="flex items-center justify-between gap-4 rounded-xl bg-slate-100 p-4">
        <div class="flex flex-col gap-0.5">
            <span class="text-sm font-semibold text-slate-800">Show on the website</span>
            <span class="text-sm text-slate-500">Hidden services disappear from the website and the quote and booking forms.</span>
        </div>
        <label class="relative inline-flex shrink-0 cursor-pointer items-center">
            <input type="checkbox" name="is_active" value="1" class="peer sr-only" @checked($active)>
            <div class="h-6 w-11 rounded-full bg-slate-300 transition-colors peer-checked:bg-brand-blue peer-focus-visible:ring-2 peer-focus-visible:ring-brand-blue peer-focus-visible:ring-offset-2 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-5"></div>
        </label>
    </div>

    <div class="flex items-center justify-end gap-2 pt-2">
        <a href="{{ route('admin.services.index') }}"
           class="rounded-lg px-6 py-2.5 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-100">Cancel</a>
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-blue px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-blue-hover">
            <i class="bi bi-check-lg"></i> Save Service
        </button>
    </div>
</form>

@endsection