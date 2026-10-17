<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookingsController extends Controller
{
    // Staff only move their own jobs along. Admins can set any status.
    private function allowedStatuses(User $user): array
    {
        return $user->isStaff() ? ['in_progress', 'completed'] : Booking::STATUSES;
    }

    // Staff can only open bookings assigned to them
    private function authorizeBooking(Request $request, Booking $booking): void
    {
        $user = $request->user();

        abort_if($user->isStaff() && $booking->technician_id !== $user->id, 403);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $status = in_array($request->query('status'), Booking::STATUSES, true)
            ? $request->query('status')
            : null;

        $serviceId = $request->integer('service') ?: null;
        $technician = $user->isStaff() ? null : $request->query('technician'); // 'unassigned' or a user id
        $date = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $request->query('date'))
            ? $request->query('date')
            : null;

        $base = Booking::query()
            ->when($user->isStaff(), fn ($query) => $query->where('technician_id', $user->id));

        $bookings = (clone $base)->with(['service', 'technician'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($serviceId, fn ($query) => $query->where('service_id', $serviceId))
            ->when($date, fn ($query) => $query->whereDate('preferred_date', $date))
            ->when($technician === 'unassigned', fn ($query) => $query->whereNull('technician_id'))
            ->when(is_numeric($technician), fn ($query) => $query->where('technician_id', (int) $technician))
            ->orderByDesc('preferred_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $counts = (clone $base)->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $categories = ServiceCategory::with(['services' => fn ($query) => $query->orderBy('name')])
            ->orderBy('sort_order')
            ->get();

        $technicians = $user->isStaff()
            ? collect()
            : User::where('role', 'staff')->where('is_active', true)->orderBy('name')->get();

        return view('admin.bookings.index', compact(
            'bookings', 'counts', 'categories', 'technicians', 'status', 'serviceId', 'technician', 'date'
        ));
    }

    public function show(Request $request, Booking $booking)
    {
        $this->authorizeBooking($request, $booking);

        $user = $request->user();
        $booking->load('service.category', 'technician');

        $statusOptions = $this->allowedStatuses($user);
        $canUpdateStatus = ! $user->isStaff() || in_array($booking->status, ['confirmed', 'in_progress'], true);

        // Active staff, plus whoever is already assigned
        $technicians = $user->isStaff()
            ? collect()
            : User::where('role', 'staff')
                ->where(fn ($query) => $query->where('is_active', true)->orWhere('id', $booking->technician_id))
                ->orderBy('name')
                ->get();

        return view('admin.bookings.show', compact('booking', 'statusOptions', 'canUpdateStatus', 'technicians'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $this->authorizeBooking($request, $booking);

        $user = $request->user();

        $data = $request->validate([
            'status' => ['required', Rule::in($this->allowedStatuses($user))],
        ]);

        // Staff can only update once the booking is confirmed
        abort_if($user->isStaff() && ! in_array($booking->status, ['confirmed', 'in_progress'], true), 403);

        if ($booking->status !== $data['status']) {
            $old = $booking->status;

            $booking->status = $data['status'];
            $booking->save();

            ActivityLog::record('status_change', "Booking {$booking->ref} changed from {$old} to {$data['status']}");
        }

        return back()->with('status', 'Status updated.');
    }

    // Admin and Super Admin only (see routes)
    public function assign(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'technician_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'staff')->where('is_active', true)],
        ]);

        $booking->technician_id = $data['technician_id'] ?? null;

        if ($booking->isDirty('technician_id')) {
            $booking->save();

            $name = $booking->technician_id ? User::find($booking->technician_id)->name : 'nobody';
            ActivityLog::record('technician_assigned', "Booking {$booking->ref} assigned to {$name}");
        }

        return back()->with('status', 'Technician saved.');
    }

    // Admin and Super Admin only (see routes)
    public function updateNotes(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $booking->admin_notes = $data['admin_notes'] ?? null;
        $booking->save();

        return back()->with('status', 'Notes saved.');
    }
}