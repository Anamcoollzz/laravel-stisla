<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTestimonialRequest;
use App\Http\Requests\UpdateTestimonialRequest;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    /**
     * Display a listing of testimonials.
     */
    public function index(): View
    {
        Gate::authorize('testimonials.view');

        $testimonials = Testimonial::query()->orderBy('order')->latest('id')->get();

        $stats = [
            'total' => $testimonials->count(),
            'active' => $testimonials->where('is_active', true)->count(),
            'inactive' => $testimonials->where('is_active', false)->count(),
        ];

        return view('modules.testimonials.index', compact('testimonials', 'stats'));
    }

    /**
     * Show the form for creating a new testimonial.
     */
    public function create(): View
    {
        Gate::authorize('testimonials.create');

        return view('modules.testimonials.create');
    }

    /**
     * Store a newly created testimonial in storage.
     */
    public function store(StoreTestimonialRequest $request): RedirectResponse
    {
        Gate::authorize('testimonials.create');

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('testimonials', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $data['order'] = $data['order'] ?? (((int) Testimonial::max('order')) + 1);
        $data['rating'] = $data['rating'] ?? 5;

        Testimonial::create($data);

        return redirect()->route('testimonials.index')->with('success', __('Testimonial created successfully.'));
    }

    /**
     * Display the specified testimonial.
     */
    public function show(Testimonial $testimonial): View
    {
        Gate::authorize('testimonials.view');

        return view('modules.testimonials.show', compact('testimonial'));
    }

    /**
     * Show the form for editing the specified testimonial.
     */
    public function edit(Testimonial $testimonial): View
    {
        Gate::authorize('testimonials.edit');

        return view('modules.testimonials.edit', compact('testimonial'));
    }

    /**
     * Update the specified testimonial in storage.
     */
    public function update(UpdateTestimonialRequest $request, Testimonial $testimonial): RedirectResponse
    {
        Gate::authorize('testimonials.edit');

        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($testimonial->image && Storage::disk('public')->exists($testimonial->image)) {
                Storage::disk('public')->delete($testimonial->image);
            }
            $data['image'] = $request->file('image')->store('testimonials', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($testimonial->image && Storage::disk('public')->exists($testimonial->image)) {
                Storage::disk('public')->delete($testimonial->image);
            }
            $data['image'] = null;
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $testimonial->update($data);

        return redirect()->route('testimonials.index')->with('success', __('Testimonial updated successfully.'));
    }

    /**
     * Remove the specified testimonial from storage.
     */
    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        Gate::authorize('testimonials.delete');

        if ($testimonial->image && Storage::disk('public')->exists($testimonial->image)) {
            Storage::disk('public')->delete($testimonial->image);
        }

        $testimonial->delete();

        return redirect()->route('testimonials.index')->with('success', __('Testimonial deleted successfully.'));
    }

    /**
     * Duplicate the specified testimonial.
     */
    public function duplicate(Testimonial $testimonial): RedirectResponse
    {
        Gate::authorize('testimonials.create');

        $clone = $testimonial->replicate();
        $clone->name = $clone->name.' (Copy)';
        $clone->order = ((int) Testimonial::max('order')) + 1;
        $clone->save();

        return redirect()->route('testimonials.index')->with('success', __('Testimonial duplicated successfully.'));
    }
}
