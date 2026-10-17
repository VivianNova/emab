@php
    $user = auth()->user();
    $admins = ['super_admin', 'admin'];

    // [label, path, icon, roles allowed, count key]
    $menu = [
        ['Dashboard', 'admin', 'bi-grid', $admins, null],
        ['Quotes', 'admin/quotes', 'bi-file-earmark-text', $admins, 'quotes'],
        ['Bookings', 'admin/bookings', 'bi-calendar-event', [...$admins, 'staff'], 'bookings'],
        ['Categories', 'admin/categories', 'bi-tags', $admins, null],
        ['Services', 'admin/services', 'bi-lightning-charge', $admins, null],
        ['Projects', 'admin/projects', 'bi-building', $admins, null],
        ['Testimonials', 'admin/testimonials', 'bi-chat-quote', $admins, null],
        ['Messages', 'admin/messages', 'bi-chat-left-text', $admins, 'messages'],
        ['Users', 'admin/users', 'bi-people', ['super_admin'], null],
        ['Settings', 'admin/settings', 'bi-gear', $admins, null, 'admin.settings.edit'],
        ['Activity Log', 'admin/activity-log', 'bi-clock-history', ['super_admin'], null],
    ];
@endphp

<aside id="admin-sidebar"
       class="fixed inset-y-0 left-0 z-50 hidden w-64 overflow-y-auto bg-white shadow-[0_1px_8px_rgba(0,0,0,0.08)] lg:block">

    <div class="flex h-16 items-center justify-between gap-3 px-4">
        <a href="{{ route('admin.dashboard') }}" class="flex min-w-0 items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="{{ setting('company_name') }} logo" class="h-8 w-auto object-contain">
            <div class="flex min-w-0 flex-col leading-tight">
                <span class="truncate font-semibold text-slate-800">{{ setting('company_name') }}</span>
                <span class="truncate text-xs font-semibold text-slate-500">Admin Portal</span>
            </div>
        </a>

        {{-- Close button, phones only --}}
        <button type="button" data-toggle="#admin-sidebar" aria-label="Close menu"
                class="rounded-md p-1.5 text-slate-500 hover:bg-slate-100 lg:hidden">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <nav class="space-y-1 px-2 py-2" aria-label="Admin">
        @foreach ($menu as $item)
            @php
                [$label, $path, $icon, $roles, $countKey] = $item;
                $routeName = $item[5] ?? null;
            @endphp
            @continue(! in_array($user->role, $roles, true))

            @php
                $active = $path === 'admin' ? request()->is('admin') : request()->is($path . '*');
                $count = $countKey ? ($menuCounts[$countKey] ?? 0) : 0;
            @endphp

            <a href="{{ $routeName ? route($routeName) : url($path) }}" @if ($active) aria-current="page" @endif
               @class([
                   'flex items-center justify-between rounded-lg px-4 py-2 text-sm transition-colors',
                   'bg-brand-tint font-bold text-brand-navy' => $active,
                   'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! $active,
               ])>
                <span class="flex items-center gap-3">
                    <i class="bi {{ $icon }} text-lg"></i> {{ $label }}
                </span>

                @if ($count > 0)
                    <span class="rounded-full bg-brand-tint px-2 py-0.5 text-xs font-semibold text-brand-navy">{{ $count }}</span>
                @endif
            </a>
        @endforeach
    </nav>
</aside>