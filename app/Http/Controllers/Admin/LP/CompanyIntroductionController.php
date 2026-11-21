<?php

namespace App\Http\Controllers\Admin\LP;

use App\Http\Controllers\Controller;
use App\Models\LP\CompanyIntroduction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompanyIntroductionController extends Controller
{
    /**
     * Show the form for editing the homepage Company Introduction section.
     */
    public function edit(): View
    {
        Gate::authorize('lp-company-introduction.edit');

        $intro = CompanyIntroduction::query()->first() ?? new CompanyIntroduction([
            'image' => 'lp-assets/images/general/about-training.jpg',
            'image_alt' => 'Candidates attending practical orientation at PT Permata Kasih Anugerah',
            'mission_label' => 'Our Mission',
            'mission_text' => 'Delivering dependable, carefully screened household and care professionals with transparent communication.',
            'eyebrow' => 'Company Introduction',
            'title' => 'Your Trusted Partner in Manpower Solutions',
            'paragraph_1' => 'Welcoming someone into your home or workplace requires confidence and clarity. At PT Permata Kasih Anugerah, we manage structured candidate screening, practical readiness orientation, and responsive placement coordination so you can hire with peace of mind.',
            'paragraph_2' => 'Our mission is to bridge responsible workers with households and businesses through honest candidate profiles, attentive consultation, and follow-up support after placement.',
            'feature_1_icon' => 'shield-check',
            'feature_1_title' => 'Verified Profiles',
            'feature_1_description' => 'Identity, reference, and readiness screening prior to listing.',
            'feature_2_icon' => 'handshake',
            'feature_2_title' => 'Guided Placement',
            'feature_2_description' => 'Personal consultant support from interview to onboarding.',
            'primary_button_label' => 'Learn More',
            'whatsapp_button_label' => 'Consult Our Team',
        ]);

        return view('modules.lp-company-introduction.edit', compact('intro'));
    }

    /**
     * Update the homepage Company Introduction section in storage.
     */
    public function update(Request $request): RedirectResponse
    {
        Gate::authorize('lp-company-introduction.edit');

        $validated = $request->validate([
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'max:2048'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'mission_label' => ['required', 'string', 'max:100'],
            'mission_text' => ['required', 'string', 'max:1000'],
            'eyebrow' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:200'],
            'paragraph_1' => ['required', 'string', 'max:3000'],
            'paragraph_2' => ['nullable', 'string', 'max:3000'],
            'feature_1_icon' => ['required', 'string', 'max:100'],
            'feature_1_title' => ['required', 'string', 'max:150'],
            'feature_1_description' => ['required', 'string', 'max:1000'],
            'feature_2_icon' => ['required', 'string', 'max:100'],
            'feature_2_title' => ['required', 'string', 'max:150'],
            'feature_2_description' => ['required', 'string', 'max:1000'],
            'primary_button_label' => ['required', 'string', 'max:100'],
            'primary_button_url' => ['nullable', 'string', 'max:255'],
            'whatsapp_button_label' => ['required', 'string', 'max:100'],
            'whatsapp_button_message' => ['nullable', 'string', 'max:1000'],
        ]);

        $intro = CompanyIntroduction::query()->first();

        if ($request->hasFile('image_file')) {
            if ($intro?->image && Storage::disk('public')->exists($intro->image)) {
                Storage::disk('public')->delete($intro->image);
            }
            $validated['image'] = $request->file('image_file')->store('lp-company-intro', 'public');
        }

        unset($validated['image_file']);

        if ($intro) {
            $intro->update($validated);
        } else {
            CompanyIntroduction::query()->create($validated);
        }

        return redirect()
            ->route('lp-company-introduction.edit')
            ->with('success', __('Company Introduction berhasil diperbarui.'));
    }
}
