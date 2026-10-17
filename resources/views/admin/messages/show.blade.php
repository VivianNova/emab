@extends('layouts.admin')

@section('title', 'Message from ' . $message->name)

@section('content')

<x-admin.page-header :title="$message->name"
                     :crumbs="['Messages' => route('admin.messages.index'), $message->name]"
                     :subtitle="$message->ref . ' - received on ' . $message->created_at->format('l, j F Y \a\t h:i A')">
    <a href="{{ route('admin.messages.index') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-800 transition-colors hover:bg-slate-300">
        <i class="bi bi-arrow-left"></i> Back to Messages
    </a>
</x-admin.page-header>

<div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">

    {{-- Message --}}
    <article class="overflow-hidden rounded-xl bg-white shadow-sm lg:col-span-8">
        <div class="flex flex-col gap-1 px-6 pb-4 pt-6">
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-brand-blue">
                <i class="bi bi-envelope text-base"></i> Message
            </div>
            <h2 class="mt-1 text-xl font-semibold leading-snug text-slate-900">{{ $message->subject }}</h2>
            <p class="flex items-center gap-2 pt-1 text-sm text-slate-500">
                <i class="bi bi-clock"></i>
                Received on {{ $message->created_at->format('l, j F Y') }} at {{ $message->created_at->format('h:i A') }}
            </p>
        </div>

        <div class="h-px w-full bg-slate-100"></div>

        <div class="p-6">
            <p class="whitespace-pre-line leading-relaxed text-slate-700">{{ $message->message }}</p>
        </div>
    </article>

    {{-- Sender --}}
    <aside class="lg:col-span-4">
        <div class="flex flex-col gap-5 rounded-xl bg-white p-6 shadow-sm">
            <h2 class="flex items-center gap-2 text-lg font-semibold text-slate-900">
                <i class="bi bi-person-badge text-brand-blue"></i> Sender
            </h2>

            <div class="flex flex-col gap-4 rounded-lg bg-slate-100 p-4">
                <div class="flex items-center gap-4 border-b border-slate-200 pb-3">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand-tint text-lg font-bold text-brand-navy">
                        {{ $message->initials }}
                    </span>
                    <span class="min-w-0 truncate text-lg font-semibold text-slate-900">{{ $message->name }}</span>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Email</span>
                    <a href="mailto:{{ $message->email }}" class="flex items-center gap-2 break-all text-sm text-slate-800 transition-colors hover:text-brand-blue">
                        <i class="bi bi-envelope text-lg text-brand-blue"></i> {{ $message->email }}
                    </a>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Phone</span>
                    @if ($message->phone)
                        <a href="tel:{{ $message->phone }}" class="flex items-center gap-2 text-sm font-semibold text-slate-800 transition-colors hover:text-brand-blue">
                            <i class="bi bi-telephone text-lg text-brand-blue"></i> {{ $message->phone }}
                        </a>
                    @else
                        <span class="text-sm text-slate-500">Not given</span>
                    @endif
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: ' . $message->subject) }}"
                   class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-brand-blue px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-blue-hover">
                    <i class="bi bi-reply"></i> Reply by email
                </a>

                @if ($message->phone)
                    <a href="tel:{{ $message->phone }}"
                       class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-800 transition-colors hover:bg-slate-200">
                        <i class="bi bi-telephone text-brand-blue"></i> Call
                    </a>
                @endif

                <form method="POST" action="{{ route('admin.messages.unread', $message) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-600 transition-colors hover:bg-slate-200 hover:text-slate-900">
                        <i class="bi bi-envelope-exclamation"></i> Mark as unread
                    </button>
                </form>
            </div>
        </div>
    </aside>
</div>

@endsection