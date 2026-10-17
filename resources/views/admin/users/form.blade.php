@extends('layouts.admin')

@section('content')
@php
    $editing = $user->exists;
    $isSelf  = $editing && $user->id === auth()->id();
    $roles   = ['super_admin' => 'Super Admin', 'admin' => 'Admin', 'staff' => 'Staff'];
    $input   = 'mt-1 w-full pl-10 pr-4 py-2.5 bg-slate-100 text-sm rounded-lg focus:ring-2 focus:ring-brand-blue focus:outline-none';
@endphp

<x-admin.page-header :title="$editing ? 'Edit user' : 'Add user'"
                     :crumbs="['Admin', 'Users', $editing ? 'Edit' : 'Add']"
                     :subtitle="$editing ? 'Update this person\'s details and access' : 'Create a login for a team member'">
    <a href="{{ route('admin.users.index') }}"
       class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-semibold px-4 py-2 rounded-lg">
        <i class="bi bi-arrow-left"></i> Back to users
    </a>
</x-admin.page-header>

<form method="POST" action="{{ $editing ? route('admin.users.update', $user) : route('admin.users.store') }}"
      class="bg-white rounded-xl shadow-sm p-6 md:p-8 max-w-4xl space-y-6">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="name" class="text-sm font-semibold text-slate-700">Full name <span class="text-red-600">*</span></label>
            <div class="relative">
                <i class="bi bi-person-badge absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 mt-0.5"></i>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="{{ $input }}">
            </div>
            @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="text-sm font-semibold text-slate-700">Email address <span class="text-red-600">*</span></label>
            <div class="relative">
                <i class="bi bi-envelope absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 mt-0.5"></i>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="{{ $input }}">
            </div>
            @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="phone" class="text-sm font-semibold text-slate-700">Phone number</label>
            <div class="relative">
                <i class="bi bi-telephone absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 mt-0.5"></i>
                <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="0712 345 678" class="{{ $input }}">
            </div>
            <p class="text-xs text-slate-500 mt-1">Kenyan mobile number.</p>
            @error('phone') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="role" class="text-sm font-semibold text-slate-700">Role <span class="text-red-600">*</span></label>
            <div class="relative">
                <i class="bi bi-shield-lock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 mt-0.5"></i>
                <select id="role" name="role" required @disabled($isSelf) class="{{ $input }} disabled:opacity-60">
                    @foreach ($roles as $value => $label)
                        <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @if ($isSelf)
                    <input type="hidden" name="role" value="{{ $user->role }}">
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1">
                {{ $isSelf ? 'You cannot change your own role.' : 'Staff see only the bookings assigned to them.' }}
            </p>
            @error('role') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="bg-slate-100 rounded-lg p-4 space-y-4">
        <p class="text-sm font-semibold text-slate-800 flex items-center gap-2">
            <i class="bi bi-key text-brand-blue"></i> {{ $editing ? 'Change password' : 'Password' }}
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="password" class="text-sm font-semibold text-slate-700">
                    {{ $editing ? 'New password' : 'Password' }} @unless ($editing) <span class="text-red-600">*</span> @endunless
                </label>
                <div class="relative">
                    <i class="bi bi-lock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 mt-0.5"></i>
                    <input type="password" id="password" name="password" autocomplete="new-password" @required(! $editing)
                           class="mt-1 w-full pl-10 pr-10 py-2.5 bg-white text-sm rounded-lg focus:ring-2 focus:ring-brand-blue focus:outline-none">
                    <button type="button" data-password-toggle="#password" aria-label="Show or hide password"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-800 mt-0.5">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <p class="text-xs text-slate-500 mt-1">At least 8 characters.</p>
                @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="text-sm font-semibold text-slate-700">Confirm password</label>
                <div class="relative">
                    <i class="bi bi-lock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 mt-0.5"></i>
                    <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                           class="mt-1 w-full pl-10 pr-10 py-2.5 bg-white text-sm rounded-lg focus:ring-2 focus:ring-brand-blue focus:outline-none">
                    <button type="button" data-password-toggle="#password_confirmation" aria-label="Show or hide password"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-800 mt-0.5">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
        </div>

        @if ($editing)
            <p class="text-xs text-slate-500 flex items-center gap-1.5"><i class="bi bi-info-circle"></i> Leave blank to keep the current password.</p>
        @endif
    </div>

    <div class="flex items-center justify-between gap-4 bg-slate-100 p-4 rounded-lg">
        <div>
            <p class="text-sm font-semibold text-slate-800">Active</p>
            <p class="text-sm text-slate-500">
                {{ $isSelf ? 'You cannot deactivate your own account.' : 'Inactive users cannot sign in.' }}
            </p>
        </div>
        <label class="relative inline-flex items-center {{ $isSelf ? 'opacity-60' : 'cursor-pointer' }} select-none">
            <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                   @checked(old('is_active', $user->is_active)) @disabled($isSelf)>
            <div class="relative w-12 h-6 bg-slate-300 rounded-full peer-checked:bg-brand-blue peer-focus-visible:ring-2 peer-focus-visible:ring-brand-blue
                        after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:w-5 after:h-5 after:bg-white after:rounded-full after:transition-all
                        peer-checked:after:translate-x-6"></div>
        </label>
        @if ($isSelf)
            <input type="hidden" name="is_active" value="1">
        @endif
    </div>

    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-2">
        <a href="{{ route('admin.users.index') }}"
           class="w-full sm:w-auto text-center bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-semibold px-6 py-2.5 rounded-lg">
            Cancel
        </a>
        <button type="submit"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-brand-blue hover:bg-brand-blue-hover text-white text-sm font-semibold px-6 py-2.5 rounded-lg">
            <i class="bi bi-check-lg"></i> Save user
        </button>
    </div>
</form>
@endsection