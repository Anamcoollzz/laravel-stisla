<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingRequest;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display the settings overview.
     */
    public function index(): View
    {
        Gate::authorize('settings.view');

        return view('modules.settings.index', [
            'groups' => config('settings.groups'),
        ]);
    }

    /**
     * Display the settings of a single group.
     */
    public function show(string $group): View
    {
        Gate::authorize('settings.view');

        $definition = $this->findGroup($group);

        return view('modules.settings.show', [
            'groups' => config('settings.groups'),
            'currentGroup' => $group,
            'definition' => $definition,
            'values' => Setting::valuesForGroup($group),
        ]);
    }

    /**
     * Update the settings of a single group.
     */
    public function update(UpdateSettingRequest $request, string $group): RedirectResponse
    {
        $definition = $this->findGroup($group);
        $stored = Setting::valuesForGroup($group);

        foreach ($definition['fields'] as $key => $field) {
            if ($field['type'] === 'image') {
                $this->storeImage($request, $group, $key, $stored[$key] ?? null);

                continue;
            }

            if ($field['type'] === 'password' && blank($request->validated($key))) {
                continue;
            }

            $value = $field['type'] === 'switch'
                ? ($request->boolean($key) ? '1' : '0')
                : $request->validated($key);

            Setting::setValue($group, $key, $value === null ? null : (string) $value);
        }

        return redirect()
            ->route('settings.show', $group)
            ->with('success', __('Settings updated successfully.'));
    }

    /**
     * Reset a group back to its default values.
     */
    public function reset(string $group): RedirectResponse
    {
        Gate::authorize('settings.edit');

        $this->findGroup($group);

        $paths = Setting::query()->where('group', $group)->pluck('value', 'key');

        foreach ($this->findGroup($group)['fields'] as $key => $field) {
            if ($field['type'] === 'image' && $paths->get($key)) {
                Storage::disk('public')->delete($paths->get($key));
            }
        }

        Setting::query()->where('group', $group)->delete();

        return redirect()
            ->route('settings.show', $group)
            ->with('success', __('Settings reset to default.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function findGroup(string $group): array
    {
        return config('settings.groups.'.$group) ?? abort(404);
    }

    /**
     * Store, replace or remove an uploaded image setting.
     */
    private function storeImage(UpdateSettingRequest $request, string $group, string $key, ?string $current): void
    {
        if ($request->hasFile($key)) {
            if ($current) {
                Storage::disk('public')->delete($current);
            }

            Setting::setValue($group, $key, $request->file($key)->store('settings', 'public'));

            return;
        }

        if ($request->boolean('remove_'.$key) && $current) {
            Storage::disk('public')->delete($current);
            Setting::setValue($group, $key, null);
        }
    }
}
