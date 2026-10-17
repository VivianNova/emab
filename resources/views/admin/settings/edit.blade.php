@extends('layouts.admin')

@section('content')
<x-admin.page-header title="Settings" :crumbs="['Admin', 'Settings']" subtitle="Contact details shown across the public website" />

<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6 max-w-5xl">
    @csrf
    @method('PUT')

    @foreach ($sections as $section)
        <section class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-brand-tint text-brand-navy flex items-center justify-center text-xl">
                    <i class="bi {{ $section['icon'] }}"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">{{ $section['title'] }}</h2>
                    <p class="text-sm text-slate-500">{{ $section['subtitle'] }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($section['fields'] as $field)
                    @php $key = $field['key']; @endphp
                    <div class="{{ !empty($field['wide']) ? 'md:col-span-2' : '' }}">
                        <label for="{{ $key }}" class="text-sm font-semibold text-slate-700">{{ $field['label'] }}</label>
                        <div class="relative mt-1">
                            @if ($field['type'] === 'textarea')
                                <i class="bi {{ $field['icon'] }} absolute left-3 top-3 text-slate-400"></i>
                                <textarea id="{{ $key }}" name="{{ $key }}" rows="2" placeholder="{{ $field['placeholder'] ?? '' }}"
                                          class="w-full pl-10 pr-4 py-2 bg-slate-100 text-sm rounded-lg resize-none focus:ring-2 focus:ring-brand-blue focus:outline-none">{{ old($key, $values[$key]) }}</textarea>
                            @else
                                <i class="bi {{ $field['icon'] }} absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="{{ $field['type'] }}" id="{{ $key }}" name="{{ $key }}"
                                       value="{{ old($key, $values[$key]) }}" placeholder="{{ $field['placeholder'] ?? '' }}"
                                       class="w-full pl-10 pr-4 py-2 bg-slate-100 text-sm rounded-lg focus:ring-2 focus:ring-brand-blue focus:outline-none">
                            @endif
                        </div>
                        @isset($field['help'])
                            <p class="text-xs text-slate-500 mt-1">{{ $field['help'] }}</p>
                        @endisset
                        @error($key)
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>
        </section>
    @endforeach

    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pb-8">
        <a href="{{ route('admin.settings.edit') }}" class="w-full sm:w-auto text-center text-sm font-semibold text-slate-600 hover:text-brand-navy px-5 py-2.5">
            Cancel
        </a>
        <button type="submit"
                class="w-full sm:w-auto bg-brand-blue hover:bg-brand-blue-hover text-white text-sm font-semibold px-8 py-2.5 rounded-lg inline-flex items-center justify-center gap-2">
            <i class="bi bi-check-lg"></i> Save settings
        </button>
    </div>
</form>
@endsection