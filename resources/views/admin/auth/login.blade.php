<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin login | {{ setting('company_name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased flex flex-col">
    <main class="flex-1 flex flex-col items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-[440px] bg-white shadow-sm rounded-xl p-6 md:p-10">
            <div class="flex flex-col items-center text-center mb-6">
                <img src="{{ asset('images/logo.png') }}" alt="{{ setting('company_name') }}" class="h-16 w-auto object-contain mb-4">
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900 mb-1">Admin login</h1>
                <p class="text-sm text-slate-500">Sign in to manage the website</p>
            </div>

            @if (session('status'))
                <div class="mb-4 rounded-lg bg-brand-tint text-brand-navy text-sm px-4 py-3">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="text-sm font-semibold">Email address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                           placeholder="you@example.com"
                           class="mt-1 w-full h-10 px-4 bg-slate-100 text-sm rounded-lg focus:ring-2 focus:ring-brand-blue focus:outline-none">
                    @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="text-sm font-semibold">Password</label>
                    <div class="relative mt-1">
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                               class="w-full h-10 pl-4 pr-12 bg-slate-100 text-sm rounded-lg focus:ring-2 focus:ring-brand-blue focus:outline-none">
                        <button type="button" data-password-toggle="#password" aria-label="Show or hide password"
                                class="absolute inset-y-0 right-0 px-4 flex items-center text-slate-500 hover:text-brand-blue">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" value="1" class="w-4 h-4 rounded accent-brand-blue" @checked(old('remember'))>
                        <span class="text-sm text-slate-600">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-semibold text-brand-blue hover:text-brand-navy">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <button type="submit"
                        class="w-full h-11 bg-brand-blue hover:bg-brand-blue-hover text-white text-sm font-semibold rounded-lg shadow-sm">
                    Sign in
                </button>
            </form>
        </div>

        <a href="{{ route('home') }}" class="mt-6 inline-flex items-center gap-2 text-sm text-slate-600 hover:text-brand-blue">
            <i class="bi bi-arrow-left"></i> Back to website
        </a>
    </main>

    <footer class="py-4 text-center text-sm text-slate-500">
        © {{ date('Y') }} {{ setting('company_name') }}
    </footer>
</body>
</html>