<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ServiceCategory;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::with([
            'services' => fn ($query) => $query
                ->select('id', 'service_category_id', 'name', 'image')
                ->where('is_active', true)
                ->orderBy('name'),
        ])->orderBy('sort_order')->get();

        $projects = Project::with(['service', 'coverImage'])
            ->orderByDesc('completed_on')
            ->orderByDesc('id')
            ->take(4)
            ->get();

        $testimonials = Testimonial::published()
            ->latest()
            ->take(3)
            ->get();

        $slides = [
            [
                'image' => 'images/hero.jpg',
                'alt' => 'Electrical work completed by EMAB Electrical',
                'heading' => 'Reliable electrical services for every space',
                'description' => 'Safe, professional electrical solutions for homes, shops and businesses in '.config('emab.area_text').'.',
            ],
            [
                'image' => 'images/ceilinglight.jpg',
                'alt' => 'Ceiling lighting installation',
                'heading' => 'Lighting installed with care',
                'description' => 'Professional lighting installation to make your home or business work better.',
            ],
        ];

        return view('home', compact('categories', 'projects', 'testimonials', 'slides'));
    }
}
