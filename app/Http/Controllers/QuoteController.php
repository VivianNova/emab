<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteRequest;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function create(Request $request)
    {
        // Categories with their active services, for the grouped select
        $categories = ServiceCategory::with([
                'services' => fn ($q) => $q->where('is_active', true)->orderBy('name'),
            ])
            ->orderBy('sort_order')
            ->get()
            ->filter(fn ($category) => $category->services->isNotEmpty());

        // /quote?service=slug preselects a service. old() wins after a failed submit.
        $selectedServiceId = old('service_id');

        if ($selectedServiceId === null && $request->filled('service')) {
            $selectedServiceId = Service::where('slug', $request->query('service'))
                ->where('is_active', true)
                ->value('id');
        }

        return view('quote', compact('categories', 'selectedServiceId'));
    }

    public function store(StoreQuoteRequest $request)
    {
        // Honeypot: bots fill the hidden field. Pretend it worked, save nothing.
        if ($request->filled('website')) {
            return back()->with('success', true);
        }

        $data = $request->validated();

        // Uploads go to the private disk (outside public/), never directly web-accessible
        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('quote-uploads', 'local')
            : null;

        QuoteRequest::create([
            'name'        => $data['name'],
            'phone'       => $request->normalizedPhone(),
            'email'       => $data['email'] ?? null,
            'service_id'  => $data['service_id'] ?? null,
            'location'    => $data['location'],
            'description' => $data['description'],
            'photo'       => $photoPath,
            'status'      => 'new',
        ]);

        return back()->with('success', true);
    }
}