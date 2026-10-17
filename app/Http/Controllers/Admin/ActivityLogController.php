<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q'      => ['nullable', 'string', 'max:100'],
            'action' => ['nullable', 'string', 'max:50'],
            'date'   => ['nullable', 'date'],
        ]);

        $logs = ActivityLog::with('user:id,name,role')
            ->when($filters['q'] ?? null, function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('description', 'like', "%{$q}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%"));
                });
            })
            ->when($filters['action'] ?? null, fn ($query, $a) => $query->where('action', $a))
            ->when($filters['date'] ?? null, fn ($query, $d) => $query->whereDate('created_at', $d))
            ->latest('created_at')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $actions = ActivityLog::query()->distinct()->orderBy('action')->pluck('action');

        return view('admin.activity-log.index', compact('logs', 'actions'));
    }
}