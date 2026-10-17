@extends('layouts.admin')

@section('content')
<x-admin.page-header title="Users" :crumbs="['Admin', 'Users']" subtitle="People who can sign in to this portal">
    <a href="{{ route('admin.users.create') }}"
       class="inline-flex items-center gap-2 bg-brand-blue hover:bg-brand-blue-hover text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
        <i class="bi bi-person-plus"></i> Add user
    </a>
</x-admin.page-header>

@if (session('error'))
    <div class="mb-4 rounded-lg bg-red-50 text-red-700 text-sm px-4 py-3">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-100 text-slate-600">
                <tr>
                    <th scope="col" class="py-3 px-4 font-semibold">Name</th>
                    <th scope="col" class="py-3 px-4 font-semibold">Email</th>
                    <th scope="col" class="py-3 px-4 font-semibold">Phone</th>
                    <th scope="col" class="py-3 px-4 font-semibold">Role</th>
                    <th scope="col" class="py-3 px-4 font-semibold">Active</th>
                    <th scope="col" class="py-3 px-4 font-semibold whitespace-nowrap">Last login</th>
                    <th scope="col" class="py-3 px-4 font-semibold text-right">Edit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($users as $user)
                    @php
                        $badge = match ($user->role) {
                            'super_admin' => 'bg-red-100 text-brand-red',
                            'admin'       => 'bg-brand-tint text-brand-navy',
                            default       => 'bg-slate-100 text-slate-700',
                        };
                        $isSelf = $user->id === auth()->id();
                    @endphp
                    <tr class="hover:bg-slate-50 {{ $user->is_active ? '' : 'opacity-60' }}">
                        <td class="py-4 px-4 font-semibold text-slate-800 whitespace-nowrap">{{ $user->name }}</td>
                        <td class="py-4 px-4 text-slate-600 whitespace-nowrap">{{ $user->email }}</td>
                        <td class="py-4 px-4 text-slate-600 whitespace-nowrap">{{ $user->phone ?? '—' }}</td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badge }}">
                                {{ \Illuminate\Support\Str::headline($user->role) }}
                            </span>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" role="switch" aria-checked="{{ $user->is_active ? 'true' : 'false' }}"
                                        aria-label="{{ $user->is_active ? 'Deactivate' : 'Activate' }} {{ $user->name }}"
                                        @disabled($isSelf) @if ($isSelf) title="You cannot deactivate your own account" @endif
                                        class="inline-flex items-center w-10 h-5 rounded-full p-0.5 transition-colors disabled:cursor-not-allowed {{ $user->is_active ? 'bg-brand-blue' : 'bg-slate-300' }}">
                                    <span class="w-4 h-4 rounded-full bg-white shadow-sm transition-transform {{ $user->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                </button>
                            </form>
                        </td>
                        <td class="py-4 px-4 text-slate-600 whitespace-nowrap">
                            {{ $user->last_login_at ? friendly_date($user->last_login_at) : 'Never' }}
                        </td>
                        <td class="py-4 px-4 text-right whitespace-nowrap">
                            <a href="{{ route('admin.users.edit', $user) }}" aria-label="Edit {{ $user->name }}"
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-500 hover:text-brand-blue hover:bg-slate-100">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-10 px-4 text-center text-slate-500">No users yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($users->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection