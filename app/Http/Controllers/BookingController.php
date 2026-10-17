<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        // Active services grouped by category, for the dropdown
        $categories = ServiceCategory::orderBy('sort_order')
            ->with(['services' => fn ($query) => $query->where('is_active', true)->orderBy('name')])
            ->get()
            ->filter(fn ($category) => $category->services->isNotEmpty());

        // /book?service=slug preselects a service
        $selected = Service::where('slug', $request->query('service'))
            ->where('is_active', true)
            ->value('id');

        return view('book', compact('categories', 'selected'));
    }

    public function store(StoreBookingRequest $request)
    {
        // Honeypot: bots fill the hidden field. Pretend it worked, save nothing.
        if ($request->filled('website')) {
            return redirect(route('book') . '#booking-form')->with('booking_success', true);
        }

        $data = $request->validated();

        // Save phones as +254XXXXXXXXX (07.. and 01.. both work)
        $digits = preg_replace('/\D/', '', $data['phone']);
        $digits = str_starts_with($digits, '254') ? substr($digits, 3) : ltrim($digits, '0');

        Booking::create([
            'name'           => $data['name'],
            'phone'          => '+254' . $digits,
            'email'          => $data['email'] ?? null,
            'service_id'     => $data['service_id'] ?? null,
            'address'        => $data['address'],
            'preferred_date' => $data['preferred_date'],
            'preferred_time' => $data['preferred_time'],
            'notes'          => $data['notes'] ?? null,
            'status'         => 'new',
        ]);

        return redirect(route('book') . '#booking-form')->with('booking_success', true);
    }
}