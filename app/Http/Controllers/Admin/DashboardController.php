<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ContactMessage;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->isStaff()) {
            return redirect()->route('admin.bookings.index');
        }

        $status = in_array($request->query('status'), QuoteRequest::STATUSES, true)
            ? $request->query('status')
            : null;

        $stats = [
            'new_quotes' => QuoteRequest::where('status', 'new')->count(),
            'upcoming_bookings' => Booking::whereDate('preferred_date', '>=', today())
                ->whereIn('status', ['new', 'confirmed', 'in_progress'])
                ->count(),
            'unread_messages' => ContactMessage::where('is_read', false)->count(),
        ];

        // Today's bookings, morning first
        $todaysBookings = Booking::with(['service', 'technician'])
            ->whereDate('preferred_date', today())
            ->get()
            ->sortBy(fn ($booking) => array_search($booking->preferred_time, array_keys(Booking::SLOTS)))
            ->values();

        $quotes = QuoteRequest::with('service')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('admin.dashboard', compact('stats', 'todaysBookings', 'quotes', 'status'));
    }
}
