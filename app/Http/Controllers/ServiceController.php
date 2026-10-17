<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;

class ServiceController extends Controller
{
    public function index()
    {
        // One block per category on the page. Each block lists its active services (the view shows the first 4).
        $categories = ServiceCategory::with([
            'services' => fn ($query) => $query->where('is_active', true)->orderBy('name'),
        ])->orderBy('sort_order')->get();

        return view('services', compact('categories'));
    }

    public function show(Service $service)
    {
        // Inactive services are hidden from the public
        abort_unless($service->is_active, 404);

        $service->load('category');

        // Up to 3 other active services from the same category
        $related = Service::with('category')
            ->where('is_active', true)
            ->where('service_category_id', $service->service_category_id)
            ->where('id', '!=', $service->id)
            ->orderBy('name')
            ->take(3)
            ->get();

        return view('service', compact('service', 'related'));
    }
}