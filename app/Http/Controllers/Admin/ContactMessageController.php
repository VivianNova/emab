<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $filter = in_array($request->query('status'), ['unread', 'read'], true)
            ? $request->query('status')
            : null;

        $search = trim((string) $request->query('q'));

        $messages = ContactMessage::query()
            ->when($filter === 'unread', fn ($query) => $query->where('is_read', false))
            ->when($filter === 'read', fn ($query) => $query->where('is_read', true))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($w) use ($search) {
                    $w->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('subject', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = [
            'all'    => ContactMessage::count(),
            'unread' => ContactMessage::where('is_read', false)->count(),
        ];
        $counts['read'] = $counts['all'] - $counts['unread'];

        return view('admin.messages.index', compact('messages', 'counts', 'filter', 'search'));
    }

    // Opening a message marks it as read
    public function show(ContactMessage $message)
    {
        if (! $message->is_read) {
            $message->is_read = true;
            $message->save();
        }

        return view('admin.messages.show', compact('message'));
    }

    public function markUnread(ContactMessage $message)
    {
        $message->is_read = false;
        $message->save();

        return redirect()->route('admin.messages.index')->with('status', 'Marked as unread.');
    }
}