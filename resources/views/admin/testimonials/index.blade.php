@extends('layouts.admin')

@section('content')
<x-admin.page-header title="Testimonials" :crumbs="['Admin', 'Testimonials']" subtitle="Client feedback shown on the Home page">
    <a href="{{ route('admin.testimonials.create') }}"
       class="inline-flex items-center gap-2 bg-brand-blue hover:bg-brand-blue-hover text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
        <i class="bi bi-plus-lg"></i> Add testimonial
    </a>
</x-admin.page-header>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-100 text-slate-600">
                <tr>
                    <th scope="col" class="py-3 px-5 font-semibold">Client name</th>
                    <th scope="col" class="py-3 px-5 font-semibold">Message</th>
                    <th scope="col" class="py-3 px-5 font-semibold whitespace-nowrap">Rating</th>
                    <th scope="col" class="py-3 px-5 font-semibold text-center whitespace-nowrap">Published</th>
                    <th scope="col" class="py-3 px-5 font-semibold text-right">Edit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($testimonials as $testimonial)
                    <tr class="hover:bg-slate-50">
                        <td class="py-4 px-5 font-semibold text-slate-800 whitespace-nowrap">{{ $testimonial->client_name }}</td>
                        <td class="py-4 px-5 max-w-md">
                            <p class="text-slate-600 truncate" title="{{ $testimonial->message }}">{{ $testimonial->message }}</p>
                        </td>
                        <td class="py-4 px-5 whitespace-nowrap">
                            <div class="flex items-center gap-0.5" aria-label="{{ $testimonial->rating }} out of 5 stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= $testimonial->rating ? 'bi-star-fill text-amber-500' : 'bi-star text-slate-300' }}"></i>
                                @endfor
                            </div>
                        </td>
                        <td class="py-4 px-5 text-center whitespace-nowrap">
                            <form method="POST" action="{{ route('admin.testimonials.toggle', $testimonial) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" role="switch" aria-checked="{{ $testimonial->is_published ? 'true' : 'false' }}"
                                        aria-label="{{ $testimonial->is_published ? 'Hide' : 'Publish' }} testimonial from {{ $testimonial->client_name }}"
                                        class="inline-flex items-center w-11 h-6 rounded-full p-0.5 transition-colors {{ $testimonial->is_published ? 'bg-brand-blue' : 'bg-slate-300' }}">
                                    <span class="w-5 h-5 rounded-full bg-white shadow-sm transition-transform {{ $testimonial->is_published ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                </button>
                            </form>
                        </td>
                        <td class="py-4 px-5 text-right whitespace-nowrap">
                            <a href="{{ route('admin.testimonials.edit', $testimonial) }}"
                               aria-label="Edit testimonial from {{ $testimonial->client_name }}"
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-500 hover:text-brand-blue hover:bg-slate-100">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 px-5 text-center text-slate-500">No testimonials yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($testimonials->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $testimonials->links() }}
        </div>
    @endif
</div>
@endsection