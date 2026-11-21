<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the user profile settings page.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load('roles');
        $sessions = $this->getActiveSessions($request, $user);

        return view('profile.edit', [
            'user' => $user,
            'sessions' => $sessions,
        ]);
    }

    /**
     * Update the user profile information and avatar.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $data = $request->safe()->only([
            'name',
            'email',
            'facebook_url',
            'twitter_url',
            'github_url',
            'instagram_url',
        ]);

        if ($request->boolean('remove_avatar') && $user->avatar) {
            if (Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = null;
        }

        if ($request->hasFile('avatar')) {
            try {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $path = $request->file('avatar')->store('avatars', 'public');
                $data['avatar'] = $path;
            } catch (\Throwable $e) {
                return redirect()
                    ->route('profile.edit')
                    ->with('error', __('Gagal menyimpan file avatar: ').$e->getMessage());
            }
        }

        $user->update($data);

        return redirect()
            ->route('profile.edit')
            ->with('success', __('Profile updated successfully.'));
    }

    /**
     * Update the user password.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', __('Password updated successfully.'));
    }

    /**
     * Log out from all other browser sessions for the authenticated user.
     */
    public function logoutOtherBrowserSessions(Request $request): RedirectResponse
    {
        $request->validate([
            'session_password' => ['required', 'string', 'current_password'],
        ]);

        /** @var User $user */
        $user = $request->user();

        Auth::logoutOtherDevices($request->input('session_password'));

        $sessionTable = (string) config('session.table', 'sessions');
        $currentSessionId = $request->hasSession() ? $request->session()->getId() : null;
        $deletedCount = 0;

        if (Schema::hasTable($sessionTable)) {
            $deletedCount = DB::table($sessionTable)
                ->where('user_id', $user->id)
                ->when($currentSessionId, fn ($query) => $query->where('id', '!=', $currentSessionId))
                ->delete();
        }

        if (Schema::hasTable('activity_logs')) {
            ActivityLog::record(
                module: 'profile',
                action: 'logout_other_sessions',
                description: "Logged out {$deletedCount} other browser session(s)",
                subject: $user,
                subjectLabel: $user->email,
                before: ['other_active_sessions' => $deletedCount],
                after: ['other_active_sessions' => 0],
            );
        }

        return redirect()
            ->route('profile.edit')
            ->with('success', __('All other browser sessions have been logged out successfully.'));
    }

    /**
     * Retrieve formatted active sessions for the given user from the sessions table.
     *
     * @return Collection<int, object>
     */
    protected function getActiveSessions(Request $request, User $user): Collection
    {
        $sessionTable = (string) config('session.table', 'sessions');
        $currentSessionId = $request->hasSession() ? $request->session()->getId() : null;

        if (! Schema::hasTable($sessionTable)) {
            return collect();
        }

        $rows = DB::table($sessionTable)
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity')
            ->get();

        $sessions = $rows->map(function (object $row) use ($currentSessionId): object {
            $userAgent = (string) ($row->user_agent ?? '');
            $lastActive = Carbon::createFromTimestamp((int) $row->last_activity);

            return (object) [
                'id' => (string) $row->id,
                'ip_address' => (string) ($row->ip_address ?: '-'),
                'user_agent' => $userAgent,
                'browser' => $this->detectBrowser($userAgent),
                'platform' => $this->detectPlatform($userAgent),
                'device' => $this->detectDevice($userAgent),
                'is_current_device' => $currentSessionId !== null && (string) $row->id === $currentSessionId,
                'last_active_at' => $lastActive->format('d M Y, H:i'),
                'last_active_human' => $lastActive->diffForHumans(),
            ];
        });

        if ($sessions->isEmpty()) {
            $ua = (string) $request->userAgent();
            $sessions->push((object) [
                'id' => (string) ($currentSessionId ?: 'current'),
                'ip_address' => (string) ($request->ip() ?: '127.0.0.1'),
                'user_agent' => $ua,
                'browser' => $this->detectBrowser($ua),
                'platform' => $this->detectPlatform($ua),
                'device' => $this->detectDevice($ua),
                'is_current_device' => true,
                'last_active_at' => now()->format('d M Y, H:i'),
                'last_active_human' => __('Just now'),
            ]);
        }

        return $sessions;
    }

    /**
     * Detect browser name from a user-agent string.
     */
    protected function detectBrowser(?string $userAgent): string
    {
        if (blank($userAgent)) {
            return __('Unknown Browser');
        }

        if (preg_match('/Edg(e)?\/([0-9.]+)/i', $userAgent)) {
            return 'Microsoft Edge';
        }
        if (preg_match('/OPR\/([0-9.]+)/i', $userAgent) || preg_match('/Opera\/([0-9.]+)/i', $userAgent)) {
            return 'Opera';
        }
        if (preg_match('/Chrome\/([0-9.]+)/i', $userAgent) && ! preg_match('/Chromium/i', $userAgent)) {
            return preg_match('/Brave/i', $userAgent) ? 'Brave' : 'Chrome';
        }
        if (preg_match('/Firefox\/([0-9.]+)/i', $userAgent)) {
            return 'Firefox';
        }
        if (preg_match('/Safari\/([0-9.]+)/i', $userAgent) && ! preg_match('/Chrome/i', $userAgent)) {
            return 'Safari';
        }

        return __('Other Browser');
    }

    /**
     * Detect OS platform from a user-agent string.
     */
    protected function detectPlatform(?string $userAgent): string
    {
        if (blank($userAgent)) {
            return __('Unknown OS');
        }

        if (preg_match('/Windows NT 10\.0/i', $userAgent)) {
            return 'Windows 10/11';
        }
        if (preg_match('/Windows/i', $userAgent)) {
            return 'Windows';
        }
        if (preg_match('/Android/i', $userAgent)) {
            return 'Android';
        }
        if (preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
            return 'iOS';
        }
        if (preg_match('/Macintosh|Mac OS X/i', $userAgent)) {
            return 'macOS';
        }
        if (preg_match('/Linux/i', $userAgent)) {
            return 'Linux';
        }

        return __('Other OS');
    }

    /**
     * Detect device category from a user-agent string.
     */
    protected function detectDevice(?string $userAgent): string
    {
        if (blank($userAgent)) {
            return 'Desktop';
        }

        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $userAgent)) {
            return 'Tablet';
        }
        if (preg_match('/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Opera M(obi|ini)/i', $userAgent)) {
            return 'Mobile';
        }

        return 'Desktop';
    }
}
