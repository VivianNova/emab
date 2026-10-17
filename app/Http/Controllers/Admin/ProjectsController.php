<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class ProjectsController extends Controller
{
    public function index(Request $request)
    {
        $categories = ServiceCategory::orderBy('sort_order')->get();

        $activeCategory = $categories->firstWhere('slug', $request->query('category'));
        $search = trim((string) $request->query('q'));

        $projects = Project::with(['service.category', 'coverImage'])
            ->withCount('images')
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
            ->paginate(10)
            ->withQueryString();

        return view('admin.projects.index', compact('projects', 'categories', 'activeCategory', 'search'));
    }

    public function create()
    {
        return view('admin.projects.form', [
            'project' => new Project(),
            'categories' => $this->categoriesWithServices(),
        ]);
    }

    public function store(Request $request)
    {
        $project = Project::create($this->validated($request));

        $this->storeImages($request, $project);

        return redirect()->route('admin.projects.index')->with('status', 'Project created.');
    }

    public function edit(Project $project)
    {
        $project->load(['images' => fn ($query) => $query->orderBy('id')]);

        return view('admin.projects.form', [
            'project' => $project,
            'categories' => $this->categoriesWithServices(),
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $project->update($this->validated($request));

        // Photos ticked for removal
        if ($ids = $request->input('remove_images')) {
            $project->images()->whereIn('id', $ids)->get()->each(function ($image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            });
        }

        $this->storeImages($request, $project);

        return redirect()->route('admin.projects.index')->with('status', 'Project updated.');
    }

    private function categoriesWithServices()
    {
        return ServiceCategory::with(['services' => fn ($query) => $query->orderBy('name')])
            ->orderBy('sort_order')
            ->get();
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title'           => ['required', 'string', 'max:150'],
            'service_id'      => ['nullable', 'exists:services,id'],
            'location'        => ['nullable', 'string', 'max:150'],
            'completed_on'    => ['nullable', 'date', 'before_or_equal:today'],
            'description'     => ['nullable', 'string', 'max:5000'],
            'images'          => ['nullable', 'array', 'max:12'],
            'images.*'        => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_images'   => ['nullable', 'array'],
            'remove_images.*' => ['integer'],
        ], [
            'completed_on.before_or_equal' => 'A completed project cannot have a date in the future.',
            'images.max'                   => 'Add up to 12 photos at a time.',
        ]);

        return Arr::only($data, ['title', 'service_id', 'location', 'completed_on', 'description']);
    }

    // The first photo ever uploaded is the cover, so keep the upload order
    private function storeImages(Request $request, Project $project): void
    {
        foreach ($request->file('images', []) as $file) {
            $project->images()->create(['image_path' => $file->store('projects', 'public')]);
        }
    }
}