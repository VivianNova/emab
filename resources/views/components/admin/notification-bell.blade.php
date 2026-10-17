@php
    $user = auth()->user();
    $unread = $user->unreadNotifications()->count();
    $items = $user->notifications()->latest()->limit(8)->get();
@endphp
<details class="relative">
    <summary class="relative cursor-pointer list-none p-2" aria-label="Notifications">
        {{-- your bell icon --}}
        @if($unread)
            <span class="absolute -right-0.5 -top-0.5 rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $unread }}</span>
        @endif
    </summary>
    <div class="absolute right-0 z-50 mt-2 w-80 rounded-lg border bg-white shadow-lg">
        @forelse($items as $n)
            <a href="{{ route('admin.notifications.open', $n->id) }}"
               class="block border-b px-4 py-3 text-sm hover:bg-slate-50 {{ $n->read_at ? 'text-slate-500' : 'font-medium' }}">
                {{ $n->data['title'] }}
                <span class="block text-xs font-normal text-slate-500">{{ $n->data['body'] }} · {{ $n->created_at->diffForHumans() }}</span>
            </a>
        @empty
            <p class="px-4 py-3 text-sm text-slate-500">No notifications.</p>
        @endforelse
        @if($unread)
            <form method="POST" action="{{ route('admin.notifications.read-all') }}" class="p-2">
                @csrf
                <button class="text-xs underline">Mark all as read</button>
            </form>
        @endif
    </div>
</details>