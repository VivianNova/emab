@props(['title', 'crumbs' => [], 'subtitle' => null])

<div class="mb-6 flex flex-col justify-between gap-3 md:flex-row md:items-center">
    <div>
        <nav aria-label="Breadcrumb" class="mb-1 flex flex-wrap items-center gap-1 text-xs font-semibold text-slate-500">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-brand-blue">Admin</a>
            @foreach ($crumbs as $key => $value)
                <i class="bi bi-chevron-right text-[10px]"></i>
                @if (is_int($key))
                    <span class="text-slate-800">{{ $value }}</span>
                @else
                    <a href="{{ $value }}" class="hover:text-brand-blue">{{ $key }}</a>
                @endif
            @endforeach
        </nav>

        <h1 class="text-2xl font-semibold text-brand-navy sm:text-3xl">{{ $title }}</h1>

        @if ($subtitle)
            <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
        @endif
    </div>

    @if ($slot->isNotEmpty())
        <div class="flex flex-wrap items-center gap-2">{{ $slot }}</div>
    @endif
</div>