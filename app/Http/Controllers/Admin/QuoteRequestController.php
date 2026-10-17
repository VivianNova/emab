<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\QuoteRequest;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class QuoteRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = in_array($request->query('status'), QuoteRequest::STATUSES, true)
            ? $request->query('status')
            : null;

        $search = trim((string) $request->query('q'));
        $serviceId = $request->integer('service') ?: null;
        $date = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $request->query('date'))
            ? $request->query('date')
            : null;

        $quotes = QuoteRequest::with('service')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($serviceId, fn ($query) => $query->where('service_id', $serviceId))
            ->when($date, fn ($query) => $query->whereDate('created_at', $date))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($w) use ($search) {
                    $w->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%");

                    // 0712... matches +254712...
                    $digits = ltrim(preg_replace('/\D/', '', $search), '0');
                    if (strlen($digits) >= 4) {
                        $w->orWhere('phone', 'like', "%{$digits}%");
                    }

                    // Q-0042, #Q-0042 or 42 matches the reference
                    if (preg_match('/^#?q?-?0*(\d+)$/i', $search, $m)) {
                        $w->orWhere('id', (int) $m[1]);
                    }
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = QuoteRequest::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $categories = ServiceCategory::with(['services' => fn ($query) => $query->orderBy('name')])
            ->orderBy('sort_order')
            ->get();

        return view('admin.quotes.index', compact(
            'quotes', 'counts', 'categories', 'status', 'search', 'serviceId', 'date'
        ));
    }

    public function show(QuoteRequest $quote)
    {
        $quote->load('service.category');

        $file = null;
        if ($quote->photo && Storage::disk('local')->exists($quote->photo)) {
            $bytes = Storage::disk('local')->size($quote->photo);
            $file = [
                'type' => strtoupper(pathinfo($quote->photo, PATHINFO_EXTENSION)),
                'size' => $bytes >= 1048576
                    ? number_format($bytes / 1048576, 1) . ' MB'
                    : max(1, round($bytes / 1024)) . ' KB',
            ];
        }

        return view('admin.quotes.show', compact('quote', 'file'));
    }

    public function updateStatus(Request $request, QuoteRequest $quote)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(QuoteRequest::STATUSES)],
        ]);

        if ($quote->status !== $data['status']) {
            $old = $quote->status;

            $quote->status = $data['status'];
            $quote->save();

            ActivityLog::record('status_change', "Quote {$quote->ref} changed from {$old} to {$data['status']}");
        }

        return back()->with('status', 'Status updated.');
    }

    public function updateNotes(Request $request, QuoteRequest $quote)
    {
        $data = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $quote->admin_notes = $data['admin_notes'] ?? null;
        $quote->save();

        return back()->with('status', 'Notes saved.');
    }

    // Quote attachments sit on the private disk, so they only open through this route
    public function file(QuoteRequest $quote)
    {
        abort_unless($quote->photo && Storage::disk('local')->exists($quote->photo), 404);

        $name = str_replace('#', '', $quote->ref) . '-attachment.' . pathinfo($quote->photo, PATHINFO_EXTENSION);

        return Storage::disk('local')->download($quote->photo, $name);
    }
}