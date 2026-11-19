<?php

namespace App\Http\Controllers\Admin\LP;

use App\Http\Controllers\Controller;
use App\Models\LP\VisionMission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class VisionMissionController extends Controller
{
    /**
     * Show the form for editing Vision & Mission.
     */
    public function edit(): View
    {
        Gate::authorize('lp-vision-mission.edit');

        $visionMission = VisionMission::query()->first() ?? new VisionMission([
            'vision_eyebrow' => 'Our Vision',
            'vision_title' => 'Setting a Dependable Standard in Manpower Placement',
            'vision_description' => 'To be recognized as a trusted, people-centered manpower recruitment partner—delivering qualified workers and fostering respectful, harmonious working relationships in every home.',
            'mission_eyebrow' => 'Our Mission',
            'mission_title' => 'How We Serve Families & Candidates',
            'mission_points' => [
                'Apply disciplined multi-step candidate screening, reference verification, and readiness checks before placement.',
                'Provide practical orientation in residential hygiene, infant care, senior assistance, and communication etiquette.',
                'Communicate candidate qualifications transparently while protecting sensitive personal privacy.',
                'Maintain responsive consultation and structured follow-up assistance after placement day.',
            ],
        ]);

        return view('modules.lp-vision-mission.edit', compact('visionMission'));
    }

    /**
     * Update Vision & Mission in storage.
     */
    public function update(Request $request): RedirectResponse
    {
        Gate::authorize('lp-vision-mission.edit');

        $validated = $request->validate([
            'vision_eyebrow' => ['required', 'string', 'max:100'],
            'vision_title' => ['required', 'string', 'max:200'],
            'vision_description' => ['required', 'string', 'max:3000'],
            'mission_eyebrow' => ['required', 'string', 'max:100'],
            'mission_title' => ['required', 'string', 'max:200'],
            'mission_points_text' => ['nullable', 'string'],
        ]);

        $lines = preg_split('/\r\n|\r|\n/', (string) ($request->input('mission_points_text') ?? '')) ?: [];
        $validated['mission_points'] = array_values(array_filter(array_map('trim', $lines), fn (string $line): bool => $line !== ''));
        unset($validated['mission_points_text']);

        $visionMission = VisionMission::query()->first();

        if ($visionMission) {
            $visionMission->update($validated);
        } else {
            VisionMission::query()->create($validated);
        }

        return redirect()
            ->route('lp-vision-mission.edit')
            ->with('success', __('Visi & Misi berhasil diperbarui.'));
    }
}
