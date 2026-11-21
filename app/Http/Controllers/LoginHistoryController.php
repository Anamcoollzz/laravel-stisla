<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LoginHistoryController extends Controller
{
    /**
     * Display a listing of login & authentication history.
     */
    public function index(Request $request): View
    {
        Gate::authorize('login-histories.view');

        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();
        $event = $request->string('event')->toString();
        $userId = $request->filled('user_id') ? $request->integer('user_id') : null;
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();

        $query = LoginHistory::query()->with('user:id,name,email,role');

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('browser', 'like', "%{$search}%")
                    ->orWhere('platform', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search): void {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($status !== '' && in_array($status, ['success', 'failed', 'blocked'], true)) {
            $query->where('status', $status);
        }

        if ($event !== '') {
            $query->where('event', $event);
        }

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        if ($dateFrom !== '') {
            $query->whereDate('logged_at', '>=', $dateFrom);
        }

        if ($dateTo !== '') {
            $query->whereDate('logged_at', '<=', $dateTo);
        }

        $histories = $query
            ->orderByDesc('logged_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => LoginHistory::query()->count(),
            'success' => LoginHistory::query()->where('status', 'success')->count(),
            'failed' => LoginHistory::query()->whereIn('status', ['failed', 'blocked'])->count(),
            'today' => LoginHistory::query()->whereDate('logged_at', today())->count(),
        ];

        $users = User::query()
            ->whereIn('id', LoginHistory::query()->whereNotNull('user_id')->distinct()->pluck('user_id'))
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $availableEvents = [
            'login_success' => __('Login Success'),
            'google_login' => __('Google Login'),
            'login_failed' => __('Login Failed'),
            'login_locked' => __('Login Locked'),
            'login_unverified' => __('Unverified Blocked'),
            'logout' => __('Logout'),
        ];

        return view('modules.login-histories.index', [
            'histories' => $histories,
            'stats' => $stats,
            'users' => $users,
            'availableEvents' => $availableEvents,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'event' => $event,
                'user_id' => $userId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    /**
     * Delete a single login history record.
     */
    public function destroy(LoginHistory $loginHistory): RedirectResponse
    {
        Gate::authorize('login-histories.delete');

        $label = (string) ($loginHistory->email ?: $loginHistory->ip_address ?: $loginHistory->id);
        $before = [
            'id' => $loginHistory->id,
            'email' => $loginHistory->email,
            'event' => $loginHistory->event,
            'ip_address' => $loginHistory->ip_address,
        ];

        $loginHistory->delete();

        ActivityLog::record(
            module: 'login-histories',
            action: 'deleted',
            description: "Deleted login history entry #{$before['id']} ({$label})",
            subjectLabel: $label,
            before: $before,
            after: null,
            subjectId: (string) $before['id'],
            subjectType: LoginHistory::class,
        );

        return redirect()
            ->route('login-histories.index')
            ->with('success', __('Login history entry deleted successfully.'));
    }

    /**
     * Clear all login history records.
     */
    public function clear(): RedirectResponse
    {
        Gate::authorize('login-histories.delete');

        $count = LoginHistory::query()->count();
        LoginHistory::query()->delete();

        ActivityLog::record(
            module: 'login-histories',
            action: 'cleared',
            description: "Cleared {$count} login history record(s)",
            subjectLabel: 'all-login-histories',
            before: ['count' => $count],
            after: ['count' => 0],
            subjectId: 'all-login-histories',
        );

        return redirect()
            ->route('login-histories.index')
            ->with('success', __(':count login history record(s) cleared successfully.', ['count' => $count]));
    }
}
