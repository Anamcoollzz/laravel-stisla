<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\UserSessionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserSessionController extends Controller
{
    public function __construct(
        protected UserSessionService $userSessionService
    ) {}

    /**
     * Display a listing of active user and guest sessions.
     */
    public function index(Request $request): View
    {
        Gate::authorize('user-sessions.view');

        $search = $request->string('search')->toString();
        $userId = $request->filled('user_id') ? $request->integer('user_id') : null;
        $type = $request->string('type')->toString();
        if (! in_array($type, ['all', 'authenticated', 'guest'], true)) {
            $type = 'all';
        }

        $stats = $this->userSessionService->getStats();
        $sessions = $this->userSessionService->paginateSessions($request, $search, $userId, $type);
        $users = $this->userSessionService->getUsersWithSessions();

        return view('modules.user-sessions.index', [
            'sessions' => $sessions,
            'stats' => $stats,
            'users' => $users,
            'filters' => [
                'search' => $search,
                'user_id' => $userId,
                'type' => $type,
            ],
        ]);
    }

    /**
     * Force logout / revoke a specific session.
     */
    public function destroy(Request $request, string $sessionId): RedirectResponse
    {
        Gate::authorize('user-sessions.delete');

        $currentSessionId = $request->hasSession() ? $request->session()->getId() : null;
        if ($currentSessionId !== null && $sessionId === $currentSessionId) {
            return redirect()
                ->route('user-sessions.index')
                ->with('error', __('You cannot revoke your own current active session from here.'));
        }

        $revoked = $this->userSessionService->revokeSession($sessionId);

        if (! $revoked) {
            return redirect()
                ->route('user-sessions.index')
                ->with('error', __('Session not found or already expired.'));
        }

        ActivityLog::record(
            module: 'user-sessions',
            action: 'revoked',
            description: 'Revoked session for '.($revoked->user_email ?: 'guest')." ({$revoked->ip_address})",
            subjectLabel: (string) ($revoked->user_email ?: $sessionId),
            before: [
                'session_id' => $sessionId,
                'user_id' => $revoked->user_id,
                'ip_address' => $revoked->ip_address,
            ],
            after: null,
            subjectId: $sessionId,
        );

        return redirect()
            ->route('user-sessions.index')
            ->with('success', __('Session has been revoked successfully.'));
    }

    /**
     * Force logout all sessions belonging to a specific user.
     */
    public function destroyUserSessions(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('user-sessions.delete');

        $currentSessionId = $request->hasSession() ? $request->session()->getId() : null;
        $exceptId = $request->user()?->id === $user->id ? $currentSessionId : null;

        $deletedCount = $this->userSessionService->revokeUserSessions($user->id, $exceptId);

        ActivityLog::record(
            module: 'user-sessions',
            action: 'revoked_user_sessions',
            description: "Revoked {$deletedCount} active session(s) for {$user->email}",
            subject: $user,
            subjectLabel: $user->email,
            before: ['active_sessions' => $deletedCount],
            after: ['active_sessions' => 0],
        );

        return redirect()
            ->back()
            ->with('success', __('User :user has been force-logged out (:count session(s) revoked).', [
                'count' => $deletedCount,
                'user' => $user->name,
            ]));
    }

    /**
     * Revoke all active sessions except the current admin session.
     */
    public function destroyOthers(Request $request): RedirectResponse
    {
        Gate::authorize('user-sessions.delete');

        $currentSessionId = $request->hasSession() ? $request->session()->getId() : null;
        $deletedCount = $this->userSessionService->revokeAllOtherSessions($currentSessionId, $request->user()?->id);

        ActivityLog::record(
            module: 'user-sessions',
            action: 'revoked_other_sessions',
            description: "Revoked {$deletedCount} other active session(s) across the system",
            subjectLabel: 'all-other-sessions',
            before: ['other_active_sessions' => $deletedCount],
            after: ['other_active_sessions' => 0],
            subjectId: 'all-other-sessions',
        );

        return redirect()
            ->route('user-sessions.index')
            ->with('success', __(':count other session(s) have been revoked successfully.', ['count' => $deletedCount]));
    }
}
