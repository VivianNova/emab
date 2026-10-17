{{-- Project card. Needs $project. Used by the Projects page and "More projects" on the project page. --}}
@php
    $category = $project->service?->category;

    // Works whether the project route is keyed by id or slug. Falls back to /projects/{key} if the route name differs.
    $href = \Illuminate\Support\Facades\Route::has('projects.show')
        ? route('projects.show', $project)
        : url('/projects/' . $project->getRouteKey());
@endphp

<article class="group flex h-full flex-col overflow-hidden rounded-2xl bg-white shadow-lg shadow-slate-200/70">
    <a href="{{ $href }}" tabindex="-1" aria-hidden="true" class="block aspect-[4/3] overflow-hidden bg-slate-100">
        @if ($project->coverImage)
            <img src="{{ $project->cover_url }}" alt="" loading="lazy"
                 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
        @else
            <div class="flex h-full w-full items-center justify-center bg-brand-navy text-6xl text-white/20">
                <i class="bi bi-lightning-charge-fill"></i>
            </div>
        @endif
    </a>

    <div class="flex flex-1 flex-col p-6">
        @if ($category)
            <span class="mb-3 inline-flex w-fit items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-brand-navy">
                <span class="h-1.5 w-1.5 rounded-full bg-brand-red"></span> {{ $category->name }}
            </span>
        @endif

        <h3 class="text-xl font-bold text-brand-navy">
            <a href="{{ $href }}" class="transition-colors hover:text-brand-blue">{{ $project->title }}</a>
        </h3>

        @if ($project->location)
            <p class="mt-1 flex items-center gap-1.5 text-sm text-slate-500">
                <i class="bi bi-geo-alt"></i> {{ $project->location }}
            </p>
        @endif

        <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ str($project->description)->limit(110) }}</p>

        <div class="mt-auto flex items-center justify-between gap-3 pt-6">
            <span class="text-sm text-slate-500">
                @if ($project->completed_on) Completed {{ $project->completed_on->format('M Y') }} @endif
            </span>
            <a href="{{ $href }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-brand-navy transition-colors hover:text-brand-blue">
                View project
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-red text-white"><i class="bi bi-arrow-right"></i></span>
            </a>
        </div>
    </div>
</article>