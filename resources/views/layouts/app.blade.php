<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>@yield('title', setting('company_name') . ' | Delivering results, Exceeding Expectations')</title>
    <meta name="description" content="@yield('description', 'Wiring, solar, lighting, security systems and repairs for homes, shops and businesses in ' . config('emab.area_text') . '.')">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-700 antialiased selection:bg-brand-blue selection:text-white">

    @include('partials.navbar')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.whatsapp')

</body>
</html>