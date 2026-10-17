<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin') | {{ setting('company_name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-700 antialiased">

    @include('partials.admin.sidebar')

    <div class="flex min-h-screen flex-col lg:pl-64">

        {{-- Top bar --}}
        <header class="fixed left-0 right-0 top-0 z-40 flex h-16 items-center justify-between bg-white px-4 shadow-[0_1px_8px_rgba(0,0,0,0.06)] sm:px-6 lg:left-64">
            <div class="flex items-center gap-3">
                <button type="button" data-toggle="#admin-sidebar" aria-label="Open menu"
                        aria-controls="admin-sidebar" aria-expanded="false"
                        class="rounded-md p-2 text-slate-600 hover:bg-slate-100 lg:hidden">
                    <i class="bi bi-list text-2xl"></i>
                </button>
                <img src="{{ asset('images/logo.png') }}" alt="" class="h-8 w-auto object-contain">
                <span class="text-xl font-semibold text-brand-navy">Admin Portal</span>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-blue text-white">
                        <i class="bi bi-person-fill"></i>
                    </span>
                    <div class="hidden flex-col text-left leading-tight sm:flex">
                        <span class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</span>
                        <span class="text-xs font-semibold text-slate-500">{{ str(auth()->user()->role)->replace('_', ' ')->title() }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-semibold text-slate-500 transition-colors hover:bg-red-50 hover:text-brand-red">
                        <i class="bi bi-box-arrow-right text-lg"></i>
                        <span class="hidden md:inline">Sign out</span>
                    </button>
                </form>
            </div>
        </header>

        <main class="flex-1 px-4 pb-8 pt-24 sm:px-6">
            @if (session('status'))
                <div role="status" class="mb-4 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>