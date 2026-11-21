<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingRequest;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
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
        $beforeChanges = [];
        $afterChanges = [];

        foreach ($definition['fields'] as $key => $field) {
            if ($field['type'] === 'image') {
                $oldImage = $stored[$key] ?? null;
                $this->storeImage($request, $group, $key, $oldImage);
                $newImage = Setting::valuesForGroup($group)[$key] ?? null;
                if ($oldImage !== $newImage) {
                    $beforeChanges[$key] = $oldImage;
                    $afterChanges[$key] = $newImage;
                }

                continue;
            }

            if ($field['type'] === 'password' && blank($request->validated($key))) {
                continue;
            }

            $value = $field['type'] === 'switch'
                ? ($request->boolean($key) ? '1' : '0')
                : $request->validated($key);

            $newVal = $value === null ? null : (string) $value;
            $oldVal = $stored[$key] ?? null;

            Setting::setValue($group, $key, $newVal);

            if ($oldVal !== $newVal) {
                $beforeChanges[$key] = $oldVal;
                $afterChanges[$key] = $newVal;
            }
        }

        Setting::flushCache();

        if (! empty($beforeChanges) || ! empty($afterChanges)) {
            ActivityLog::record(
                module: 'settings',
                action: 'updated',
                description: "Updated {$group} settings",
                subjectLabel: $group,
                before: $beforeChanges,
                after: $afterChanges,
                subjectId: $group,
                subjectType: Setting::class,
            );
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
        $beforeValues = $paths->toArray();

        foreach ($this->findGroup($group)['fields'] as $key => $field) {
            if ($field['type'] === 'image' && $paths->get($key)) {
                Storage::disk('public')->delete($paths->get($key));
            }
        }

        Setting::query()->where('group', $group)->delete();
        Setting::flushCache();

        if (! empty($beforeValues)) {
            ActivityLog::record(
                module: 'settings',
                action: 'reset',
                description: "Reset {$group} settings to default",
                subjectLabel: $group,
                before: $beforeValues,
                after: null,
                subjectId: $group,
                subjectType: Setting::class,
            );
        }

        return redirect()
            ->route('settings.show', $group)
            ->with('success', __('Settings reset to default.'));
    }

    /**
     * Send a test email using the active SMTP or Email API configuration.
     */
    public function sendTestEmail(Request $request): RedirectResponse
    {
        Gate::authorize('settings.edit');

        $validated = $request->validate([
            'test_email' => ['required', 'email', 'max:255'],
        ]);

        $recipient = $validated['test_email'];
        $mailer = (string) config('mail.default', 'log');

        try {
            Mail::purge($mailer);
            Mail::raw(
                __('This is a test email sent from :app using the :mailer mail driver at :time.', [
                    'app' => config('app.name', 'Laravel Stisla'),
                    'mailer' => strtoupper($mailer),
                    'time' => now()->format('d M Y, H:i:s'),
                ]),
                static function ($message) use ($recipient): void {
                    $message->to($recipient)
                        ->subject(__('Test Email Configuration - :app', ['app' => config('app.name', 'Laravel Stisla')]));
                }
            );

            ActivityLog::record(
                module: 'settings',
                action: 'test_email',
                description: "Sent test email to {$recipient} via {$mailer}",
                subjectLabel: $recipient,
                before: null,
                after: ['recipient' => $recipient, 'mailer' => $mailer],
                subjectId: 'email',
            );

            return redirect()
                ->route('settings.show', 'email')
                ->with('success', __('Test email sent to :email via :mailer!', [
                    'email' => $recipient,
                    'mailer' => strtoupper($mailer),
                ]));
        } catch (\Throwable $e) {
            return redirect()
                ->route('settings.show', 'email')
                ->with('error', __('Failed to send test email: ').$e->getMessage());
        }
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
