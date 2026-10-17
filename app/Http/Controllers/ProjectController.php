<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        // Only categories that have projects get a tab
        $categories = ServiceCategory::has('projects')
            ->withCount('projects')
            ->orderBy('sort_order')
            ->get();

        $activeCategory = $categories->firstWhere('slug', $request->query('category'));
        $search = trim((string) $request->query('q'));

        $projects = Project::with(['service.category', 'coverImage'])
            ->when($activeCategory, fn ($query) => $query->whereHas(
                'service',
                fn ($service) => $service->where('service_category_id', $activeCategory->id)
            ))
            ->when($search !== '', fn ($query) => $query->where(
                fn ($w) => $w->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
            ))
            ->orderByDesc('completed_on')
            ->orderByDesc('id')
            ->paginate(9)
            ->withQueryString();

        $totalCount = Project::count();

        return view('projects', compact('categories', 'activeCategory', 'projects', 'search', 'totalCount'));
    }

    public function show(Project $project)
    {
        $project->load(['service.category', 'images']);

        $categoryId = $project->service?->service_category_id;

        $related = Project::with(['service.category', 'coverImage'])
            ->where('id', '!=', $project->id)
            ->when($categoryId, fn ($query) => $query->whereHas(
                'service',
                fn ($service) => $service->where('service_category_id', $categoryId)
            ))
            ->orderByDesc('completed_on')
            ->take(3)
            ->get();

        return view('project', compact('project', 'related'));
    }
}