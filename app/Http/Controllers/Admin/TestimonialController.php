<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestimonialRequest;
use App\Models\ActivityLog;
use App\Models\Testimonial;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::latest('id')->paginate(15)->withQueryString();

        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        $testimonial = new Testimonial(['rating' => 5, 'is_published' => true]);

        return view('admin.testimonials.form', compact('testimonial'));
    }

    public function store(TestimonialRequest $request)
    {
        Testimonial::create($request->validated());

        return redirect()->route('admin.testimonials.index')->with('status', 'Testimonial added.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.form', compact('testimonial'));
    }

    public function update(TestimonialRequest $request, Testimonial $testimonial)
    {
        $wasPublished = $testimonial->is_published;

        $testimonial->update($request->validated());

        if ($wasPublished !== $testimonial->is_published) {
            $this->logPublish($testimonial);
        }

        return redirect()->route('admin.testimonials.index')->with('status', 'Testimonial saved.');
    }

    public function toggle(Testimonial $testimonial)
    {
        $testimonial->update(['is_published' => ! $testimonial->is_published]);

        $this->logPublish($testimonial);

        return back()->with('status', $testimonial->is_published
            ? 'Testimonial is now published.'
            : 'Testimonial is now hidden.');
    }

    private function logPublish(Testimonial $testimonial): void
    {
        ActivityLog::record(
            'status_change',
            'Testimonial from ' . $testimonial->client_name . ' was ' . ($testimonial->is_published ? 'published' : 'unpublished')
        );
    }
}