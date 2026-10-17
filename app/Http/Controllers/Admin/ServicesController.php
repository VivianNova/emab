<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServicesController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));

        $services = Service::with('category')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($w) use ($search) {
                    $w->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('service_category_id')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.services.index', compact('services', 'search'));
    }

    public function create()
    {
        return view('admin.services.form', [
            'service' => new Service(),
            'categories' => ServiceCategory::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        Service::create($data);

        return redirect()->route('admin.services.index')->with('status', 'Service created.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.form', [
            'service' => $service,
            'categories' => ServiceCategory::orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $data = $this->validated($request, $service);

        // A new upload or "remove" clears the old file first
        if ($request->boolean('remove_image') || $request->hasFile('image')) {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            $data['image'] = null;
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);

        return redirect()->route('admin.services.index')->with('status', 'Service updated.');
    }

    public function destroy(Service $service)
    {
        $name = $service->name;
        $image = $service->image;

        try {
            $service->delete();
        } catch (QueryException $e) {
            return back()->with('status', "“{$name}” is linked to existing quotes or bookings and can't be deleted. Hide it with the Active switch instead.");
        }

        if ($image) {
            Storage::disk('public')->delete($image);
        }

        return redirect()->route('admin.services.index')->with('status', "“{$name}” deleted.");
    }

    // The switch in the list: show or hide a service on the public site
    public function toggle(Service $service)
    {
        $service->is_active = ! $service->is_active;
        $service->save();

        return back()->with('status', $service->is_active
            ? 'Service is now visible on the website.'
            : 'Service hidden from the website.');
    }

    private function validated(Request $request, ?Service $service = null): array
    {
        // Use the typed slug, or build one from the name
        $request->merge([
            'slug' => Str::slug($request->input('slug') ?: $request->input('name', '')),
        ]);

        $data = $request->validate([
            'service_category_id' => ['required', 'exists:service_categories,id'],
            'name'                => ['required', 'string', 'max:150'],
            'slug'                => ['required', 'string', 'max:160', Rule::unique('services', 'slug')->ignore($service?->id)],
            'description'         => ['required', 'string', 'max:5000'],
            'image'               => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        unset($data['image']); // the caller stores the file

        return $data;
    }
}