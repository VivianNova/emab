<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::withCount('services')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        // New categories go to the end of the list by default
        $category = new ServiceCategory([
            'sort_order' => (ServiceCategory::max('sort_order') ?? 0) + 1,
        ]);

        return view('admin.categories.form', compact('category'));
    }

    public function store(Request $request)
    {
        ServiceCategory::create($this->validated($request));

        return redirect()->route('admin.categories.index')->with('status', 'Category created.');
    }

    public function edit(ServiceCategory $category)
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, ServiceCategory $category)
    {
        $category->update($this->validated($request, $category));

        return redirect()->route('admin.categories.index')->with('status', 'Category updated.');
    }

    private function validated(Request $request, ?ServiceCategory $category = null): array
    {
        // Use the typed slug, or build one from the name
        $request->merge([
            'slug' => Str::slug($request->input('slug') ?: $request->input('name', '')),
        ]);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'slug'        => ['required', 'string', 'max:120', Rule::unique('service_categories', 'slug')->ignore($category?->id)],
            'description' => ['nullable', 'string', 'max:200'],
            'icon'        => ['nullable', 'string', 'max:50', 'regex:/^bi-[a-z0-9-]+$/'],
            'sort_order'  => ['nullable', 'integer', 'min:0', 'max:999'],
        ], [
            'icon.regex' => 'Use a Bootstrap Icons name such as bi-sun or bi-house.',
        ]);

        $data['icon'] = $data['icon'] ?? 'bi-tag';
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}