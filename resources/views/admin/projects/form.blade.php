@extends('layouts.admin')

@section('title', $project->exists ? 'Edit Project' : 'Add Project')

@section('content')

@php
    $editing = $project->exists;
    $label = 'flex items-center gap-1 text-sm font-semibold text-slate-800';
    $field = 'w-full rounded-lg bg-slate-100 text-sm text-slate-800 placeholder:text-slate-400 transition-colors focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue';
    $help = 'text-xs text-slate-500';
    $cardIcon = 'flex h-8 w-8 items-center justify-center rounded-lg bg-brand-tint text-brand-navy';
@endphp

<x-admin.page-header :title="$editing ? 'Edit Project' : 'Add Project'"
                     :crumbs="['Projects' => route('admin.projects.index'), $editing ? 'Edit Project' : 'Add Project']"
                     subtitle="Project details, the service it belongs to, and photos.">
    <a href="{{ route('admin.projects.index') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-800 transition-colors hover:bg-slate-300">
        <i class="bi bi-arrow-left"></i> Back to Projects
    </a>
</x-admin.page-header>

<form method="POST" enctype="multipart/form-data"
      action="{{ $editing ? route('admin.projects.update', $project) : route('admin.projects.store') }}"
      class="flex flex-col gap-6">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    {{-- Details --}}
    <section class="flex flex-col gap-4 rounded-xl bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="{{ $cardIcon }}"><i class="bi bi-clipboard2-check"></i></span>
                <h2 class="text-lg font-semibold text-slate-900">Project details</h2>
            </div>
            <span class="text-xs text-slate-500">Fields marked <span class="text-red-600">*</span> are required</span>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="flex flex-col gap-1.5 md:col-span-2">
                <label for="title" class="{{ $label }}">Title <span class="text-red-600">*</span></label>
                <input id="title" name="title" type="text" required value="{{ old('title', $project->title) }}"
                       placeholder="e.g. Rooftop solar and battery backup" class="{{ $field }} h-11 px-4">
                @error('title') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="service_id" class="{{ $label }}">Service</label>
                <div class="relative">
                    <select id="service_id" name="service_id" class="{{ $field }} h-11 cursor-pointer appearance-none pl-4 pr-10">
                        <option value="">No service</option>
                        @foreach ($categories as $category)
                            @if ($category->services->isNotEmpty())
                                <optgroup label="{{ $category->name }}">
                                    @foreach ($category->services as $service)
                                        <option value="{{ $service->id }}" @selected((int) old('service_id', $project->service_id) === $service->id)>
                                            {{ $service->name }}{{ $service->is_active ? '' : ' (hidden)' }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                    <i class="bi bi-chevron-down pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                </div>
                <span class="{{ $help }}">Its category becomes the filter tab this project appears under.</span>
                @error('service_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="location" class="{{ $label }}">Location</label>
                <div class="relative">
                    <i class="bi bi-geo-alt pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input id="location" name="location" type="text" value="{{ old('location', $project->location) }}"
                           placeholder="e.g. {{ config('emab.base') }}" class="{{ $field }} h-11 pl-10 pr-4">
                </div>
                @error('location') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="completed_on" class="{{ $label }}">Completion date</label>
                <div class="relative">
                    <i class="bi bi-calendar3 pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input id="completed_on" name="completed_on" type="date" max="{{ now()->toDateString() }}"
                           value="{{ old('completed_on', $project->completed_on?->toDateString()) }}"
                           class="{{ $field }} h-11 pl-10 pr-4">
                </div>
                @error('completed_on') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-col gap-1.5 md:col-span-2">
                <label for="description" class="{{ $label }}">Description</label>
                <textarea id="description" name="description" rows="5"
                          placeholder="What was done and the result, in plain words."
                          class="{{ $field }} resize-y p-4 leading-relaxed">{{ old('description', $project->description) }}</textarea>
                <span class="{{ $help }}">Leave a blank line to start a new paragraph.</span>
                @error('description') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>

    {{-- Photos --}}
    <section class="flex flex-col gap-4 rounded-xl bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="{{ $cardIcon }}"><i class="bi bi-images"></i></span>
                <h2 class="text-lg font-semibold text-slate-900">Photos</h2>
            </div>
            @if ($editing)
                <span class="text-xs text-slate-500">{{ $project->images->count() }} {{ str('photo')->plural($project->images->count()) }} attached</span>
            @endif
        </div>

        <div class="flex flex-col gap-2 rounded-xl bg-slate-100 p-5">
            <label for="images" class="text-sm font-semibold text-slate-800">Add photos</label>
            <input id="images" name="images[]" type="file" multiple accept="image/png,image/jpeg,image/webp"
                   class="block w-full cursor-pointer rounded-lg bg-white text-sm text-slate-600 file:mr-4 file:cursor-pointer file:border-0 file:bg-brand-blue file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-blue-hover">
            <span class="{{ $help }}">JPG, PNG or WEBP, up to 5 MB each, several at once. The first photo ever uploaded is the cover.</span>
            @error('images') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            @if ($errors->has('images.*'))
                <p class="text-sm text-red-600">{{ $errors->first('images.*') }}</p>
            @endif
        </div>

        @if ($editing && $project->images->isNotEmpty())
            <div>
                <p class="mb-2 text-sm font-semibold text-slate-800">Current gallery</p>

                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                    @foreach ($project->images as $image)
                        <div class="group relative aspect-square overflow-hidden rounded-lg bg-slate-100 shadow-sm">
                            <input id="remove-{{ $image->id }}" type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="peer sr-only">

                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Photo {{ $loop->iteration }} of {{ $project->title }}" loading="lazy"
                                 class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105 peer-checked:opacity-30">

                            @if ($loop->first)
                                <span class="absolute left-2.5 top-2.5 flex items-center gap-1 rounded bg-brand-blue px-2 py-0.5 text-xs font-semibold text-white shadow-sm">
                                    <i class="bi bi-star-fill text-[10px]"></i> Cover
                                </span>
                            @endif

                            <label for="remove-{{ $image->id }}" title="Remove photo" aria-label="Remove photo {{ $loop->iteration }}"
                                   class="absolute right-2.5 top-2.5 flex h-7 w-7 cursor-pointer items-center justify-center rounded-full bg-brand-navy/80 text-white shadow-sm transition-colors hover:bg-brand-red peer-checked:bg-brand-red peer-focus-visible:ring-2 peer-focus-visible:ring-brand-blue">
                                <i class="bi bi-x-lg text-xs"></i>
                            </label>

                            <span class="pointer-events-none absolute inset-x-0 bottom-0 hidden bg-brand-red py-1 text-center text-xs font-semibold text-white peer-checked:block">
                                Removed when you save
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    {{-- Actions --}}
    <div class="flex items-center justify-between rounded-xl bg-white p-4 shadow-sm">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            @if ($editing)
                <i class="bi bi-clock text-brand-blue"></i> Last saved {{ $project->updated_at->format('j M Y') }}
            @endif
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.projects.index') }}"
               class="rounded-lg px-6 py-2.5 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-100">Cancel</a>
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-blue px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-blue-hover">
                <i class="bi bi-save"></i> Save Project
            </button>
        </div>
    </div>
</form>

@endsection