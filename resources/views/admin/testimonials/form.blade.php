@extends('layouts.admin')

@section('content')
@php
    $editing = $testimonial->exists;
    $currentRating = (int) old('rating', $testimonial->rating ?? 5);
@endphp

<x-admin.page-header :title="$editing ? 'Edit testimonial' : 'Add testimonial'"
                     :crumbs="['Admin', 'Testimonials', $editing ? 'Edit' : 'Add']"
                     subtitle="Client feedback shown on the Home page">
    <a href="{{ route('admin.testimonials.index') }}"
       class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-semibold px-4 py-2 rounded-lg">
        <i class="bi bi-arrow-left"></i> Back to testimonials
    </a>
</x-admin.page-header>

<form method="POST"
      action="{{ $editing ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}"
      class="bg-white rounded-xl shadow-sm p-6 md:p-8 max-w-4xl space-y-6">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div>
        <label for="client_name" class="text-sm font-semibold text-slate-700">Client name <span class="text-red-600">*</span></label>
        <input type="text" id="client_name" name="client_name" value="{{ old('client_name', $testimonial->client_name) }}"
               placeholder="Client's name" required
               class="mt-1 w-full h-11 px-4 bg-slate-100 text-sm rounded-lg focus:ring-2 focus:ring-brand-blue focus:outline-none">
        @error('client_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="message" class="text-sm font-semibold text-slate-700">Message <span class="text-red-600">*</span></label>
        <textarea id="message" name="message" rows="5" required placeholder="What the client said about your work"
                  class="mt-1 w-full p-4 bg-slate-100 text-sm rounded-lg resize-y min-h-[120px] focus:ring-2 focus:ring-brand-blue focus:outline-none">{{ old('message', $testimonial->message) }}</textarea>
        @error('message') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <fieldset>
        <legend class="text-sm font-semibold text-slate-700">Rating</legend>
        <div class="star-rating relative mt-2 flex flex-row-reverse justify-end w-fit gap-1">
            @for ($i = 5; $i >= 1; $i--)
                <input type="radio" name="rating" id="rating-{{ $i }}" value="{{ $i }}" @checked($currentRating === $i)>
                <label for="rating-{{ $i }}" title="{{ $i }} {{ \Illuminate\Support\Str::plural('star', $i) }}">
                    <i class="bi bi-star-fill"></i>
                    <span class="sr-only">{{ $i }} {{ \Illuminate\Support\Str::plural('star', $i) }}</span>
                </label>
            @endfor
        </div>
        @error('rating') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </fieldset>

    <div class="flex items-center justify-between gap-4 bg-slate-100 p-4 rounded-lg">
        <div>
            <p class="text-sm font-semibold text-slate-800">Published</p>
            <p class="text-sm text-slate-500">Show on the public website</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer select-none">
            <input type="checkbox" name="is_published" value="1" class="sr-only peer"
                   @checked(old('is_published', $testimonial->is_published))>
            <div class="relative w-12 h-6 bg-slate-300 rounded-full peer-checked:bg-brand-blue peer-focus-visible:ring-2 peer-focus-visible:ring-brand-blue
                        after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:w-5 after:h-5 after:bg-white after:rounded-full after:transition-all
                        peer-checked:after:translate-x-6"></div>
        </label>
    </div>

    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-2">
        <a href="{{ route('admin.testimonials.index') }}"
           class="w-full sm:w-auto text-center bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-semibold px-6 py-2.5 rounded-lg">
            Cancel
        </a>
        <button type="submit"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-brand-blue hover:bg-brand-blue-hover text-white text-sm font-semibold px-6 py-2.5 rounded-lg">
            <i class="bi bi-check-lg"></i> Save testimonial
        </button>
    </div>
</form>
@endsectiona