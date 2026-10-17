@extends('layouts.app')

@section('title', 'Our Projects | ' . setting('company_name'))
@section('description', 'Recent electrical, solar and lighting installations completed by ' . setting('company_name') . ' in ' . config('emab.area_text') . '.')

@section('content')

{{-- Page header --}}
<section class="bg-white">
    <div class="mx-auto max-w-7xl px-4 pb-8 pt-12 sm:px-6 lg:px-8">
        <nav aria-label="Breadcrumb" class="mb-4 text-sm text-slate-500">
            <ol class="flex items-center gap-2">
                <li><a href="{{ url('/') }}" class="hover:text-brand-blue">Home</a></li>
                <li aria-hidden="true"><i class="bi bi-chevron-right text-xs"></i></li>
                <li aria-current="page" class="font-semibold text-slate-800">Projects</li>
            </ol>
        </nav>
        <h1 class="mb-2 text-3xl font-semibold text-brand-navy sm:text-4xl">Our Projects</h1>
        <p class="max-w-3xl text-lg text-slate-600">Explore our recent electrical, solar, and lighting installations in {{ config('emab.area_text') }}.</p>
    </div>
</section>

{{-- Filters --}}
<section class="border-y border-slate-200 bg-slate-100">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            {{-- Category tabs: scroll sideways on phones --}}
            <nav aria-label="Project categories"
                 class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1 lg:mx-0 lg:flex-wrap lg:overflow-visible lg:px-0 lg:pb-0">
                <a href="{{ route('projects', array_filter(['q' => $search])) }}"
                   @class([
                       'shrink-0 rounded-md px-4 py-2 text-sm font-semibold transition-colors',
                       'bg-brand-blue text-white' => ! $activeCategory,
                       'bg-white text-slate-600 hover:bg-slate-200' => $activeCategory,
                   ])>All ({{ $totalCount }})</a>

                @foreach ($categories as $category)
                    <a href="{{ route('projects', array_filter(['category' => $category->slug, 'q' => $search])) }}"
                       @class([
                           'shrink-0 rounded-md px-4 py-2 text-sm font-semibold transition-colors',
                           'bg-brand-blue text-white' => $activeCategory?->id === $category->id,
                           'bg-white text-slate-600 hover:bg-slate-200' => $activeCategory?->id !== $category->id,
                       ])>{{ $category->name }} ({{ $category->projects_count }})</a>
                @endforeach
            </nav>

            {{-- Search (keeps the active category) --}}
            <form method="GET" action="{{ route('projects') }}" class="relative w-full lg:w-80">
                @if ($activeCategory)
                    <input type="hidden" name="category" value="{{ $activeCategory->slug }}">
                @endif
                <i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="q" value="{{ $search }}" aria-label="Search projects"
                       placeholder="Search by project or location"
                       class="w-full rounded-md border border-slate-300 bg-white py-2 pl-10 pr-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-brand-blue focus:outline-none focus:ring-1 focus:ring-brand-blue">
            </form>
        </div>
    </div>
</section>

{{-- Grid --}}
<section class="bg-slate-50 py-12 lg:py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @if ($projects->isNotEmpty())
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    @include('partials.project-card')
                @endforeach
            </div>

            <div class="mt-12">{{ $projects->links() }}</div>
        @else
            <div class="rounded-lg border border-slate-200 bg-white p-10 text-center">
                <p class="mb-4 text-slate-600">No projects match your search.</p>
                <a href="{{ route('projects') }}" class="font-semibold text-brand-blue hover:text-brand-blue-hover">View all projects</a>
            </div>
        @endif
    </div>
</section>

@include('partials.cta', ['heading' => 'Have a project in mind?'])

@endsection