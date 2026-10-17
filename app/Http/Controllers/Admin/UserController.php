<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderByDesc('is_active')->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $user = new User(['role' => 'staff', 'is_active' => true]);

        return view('admin.users.form', compact('user'));
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        ActivityLog::record('user_changed', "User {$user->name} was added as " . Str::headline($user->role));

        return redirect()->route('admin.users.index')->with('status', 'User added.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        if ($error = $this->guard($user, $data['role'], (bool) $data['is_active'])) {
            return back()->withInput()->withErrors(['role' => $error]);
        }

        $passwordChanged = filled($data['password'] ?? null);

        if ($passwordChanged) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $oldRole   = $user->role;
        $oldActive = $user->is_active;

        $user->update($data);

        $notes = [];
        if ($oldRole !== $user->role) {
            $notes[] = 'role changed from ' . Str::headline($oldRole) . ' to ' . Str::headline($user->role);
        }
        if ($oldActive !== $user->is_active) {
            $notes[] = $user->is_active ? 'activated' : 'deactivated';
        }
        if ($passwordChanged) {
            $notes[] = 'password reset';
        }

        if ($notes) {
            ActivityLog::record('user_changed', "User {$user->name}: " . implode(', ', $notes));
        }

        return redirect()->route('admin.users.index')->with('status', 'User saved.');
    }

    public function toggle(User $user)
    {
        $newActive = ! $user->is_active;

        if ($error = $this->guard($user, $user->role, $newActive)) {
            return back()->with('error', $error);
        }

        $user->update(['is_active' => $newActive]);

        ActivityLog::record('user_changed', "User {$user->name}: " . ($newActive ? 'activated' : 'deactivated'));

        return back()->with('status', $newActive ? 'User activated.' : 'User deactivated.');
    }

    /** Returns an error message if the change would lock the portal out, otherwise null. */
    private function guard(User $user, string $newRole, bool $newActive): ?string
    {
        if ($user->id === auth()->id() && ($newRole !== $user->role || ! $newActive)) {
            return 'You cannot change your own role or deactivate your own account.';
        }

        if ($user->role === 'super_admin' && $user->is_active && ($newRole !== 'super_admin' || ! $newActive)) {
            $others = User::where('role', 'super_admin')->where('is_active', true)->where('id', '!=', $user->id)->count();

            if ($others === 0) {
                return 'There must be at least one active Super Admin.';
            }
        }

        return null;
    }
}