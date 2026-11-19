<?php

namespace App\Http\Controllers\Admin\LP;

use App\Http\Controllers\Controller;
use App\Models\LP\CompanyProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CompanyProfileController extends Controller
{
    /**
     * Show the form for editing the company & contact information.
     */
    public function edit(): View
    {
        Gate::authorize('lp-company-profile.edit');

        $profile = CompanyProfile::query()->first() ?? new CompanyProfile([
            'name' => config('company.name'),
            'legal_name' => config('company.legal_name'),
            'tagline' => config('company.tagline'),
            'license_number' => config('company.license_number'),
            'whatsapp_number' => config('company.whatsapp_number'),
            'phone_display' => config('company.phone_display'),
            'secondary_phone' => config('company.secondary_phone'),
            'email' => config('company.email'),
            'address' => config('company.address'),
            'short_address' => config('company.short_address'),
            'operating_hours_weekday' => config('company.operating_hours.weekday'),
            'operating_hours_saturday' => config('company.operating_hours.saturday'),
            'operating_hours_sunday' => config('company.operating_hours.sunday'),
            'map_embed_url' => config('company.map_embed_url'),
            'instagram_url' => config('company.social_links.instagram'),
            'facebook_url' => config('company.social_links.facebook'),
            'youtube_url' => config('company.social_links.youtube'),
            'default_whatsapp_message' => config('company.default_whatsapp_message'),
        ]);

        return view('modules.lp-company-profile.edit', compact('profile'));
    }

    /**
     * Update the company & contact information in storage.
     */
    public function update(Request $request): RedirectResponse
    {
        Gate::authorize('lp-company-profile.edit');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'legal_name' => ['required', 'string', 'max:150'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'license_number' => ['nullable', 'string', 'max:150'],
            'whatsapp_number' => ['required', 'string', 'max:30'],
            'phone_display' => ['required', 'string', 'max:50'],
            'secondary_phone' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:150'],
            'address' => ['required', 'string', 'max:2000'],
            'short_address' => ['nullable', 'string', 'max:150'],
            'operating_hours_weekday' => ['nullable', 'string', 'max:150'],
            'operating_hours_saturday' => ['nullable', 'string', 'max:150'],
            'operating_hours_sunday' => ['nullable', 'string', 'max:150'],
            'map_embed_url' => ['nullable', 'string', 'max:2000'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'default_whatsapp_message' => ['nullable', 'string', 'max:1000'],
        ]);

        $profile = CompanyProfile::query()->first();

        if ($profile) {
            $profile->update($validated);
        } else {
            CompanyProfile::query()->create($validated);
        }

        return redirect()
            ->route('lp-company-profile.edit')
            ->with('success', __('Informasi kontak & profil perusahaan berhasil diperbarui.'));
    }
}
