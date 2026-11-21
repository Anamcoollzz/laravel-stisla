<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs with filters and pagination.
     */
    public function index(Request $request): View
    {
        Gate::authorize('activity-logs.view');

        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'module' => trim((string) $request->query('module', '')),
            'action' => trim((string) $request->query('action', '')),
            'date_from' => trim((string) $request->query('date_from', '')),
            'date_to' => trim((string) $request->query('date_to', '')),
        ];

        $activityLogs = ActivityLog::query()
            ->with('user')
            ->when($filters['search'] !== '', function ($query) use ($filters) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                        ->orWhere('subject_label', 'like', "%{$search}%")
                        ->orWhere('subject_id', 'like', "%{$search}%")
                        ->orWhere('ip', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
                });
            })
            ->when($filters['module'] !== '', fn ($q) => $q->where('module', $filters['module']))
            ->when($filters['action'] !== '', fn ($q) => $q->where('action', $filters['action']))
            ->when($filters['date_from'] !== '', fn ($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
            ->when($filters['date_to'] !== '', fn ($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $stats = [
            'total' => ActivityLog::query()->count(),
            'today' => ActivityLog::query()->whereDate('created_at', today())->count(),
            'orders' => ActivityLog::query()->where('module', 'orders')->count(),
            'rbac_settings' => ActivityLog::query()->whereIn('module', ['roles', 'permissions', 'settings'])->count(),
        ];

        $availableModules = ActivityLog::query()->select('module')->distinct()->orderBy('module')->pluck('module')->filter()->values();
        $availableActions = ActivityLog::query()->select('action')->distinct()->orderBy('action')->pluck('action')->filter()->values();

        return view('modules.activity-logs.index', compact(
            'activityLogs',
            'filters',
            'stats',
            'availableModules',
            'availableActions'
        ));
    }

    /**
     * Display the specified activity log detail.
     */
    public function show(ActivityLog $activityLog): View
    {
        Gate::authorize('activity-logs.view');

        $activityLog->load('user');

        return view('modules.activity-logs.show', compact('activityLog'));
    }

    /**
     * Remove the specified activity log from database.
     */
    public function destroy(ActivityLog $activityLog): RedirectResponse
    {
        Gate::authorize('activity-logs.delete');

        $activityLog->delete();

        return redirect()->route('activity-logs.index')
            ->with('success', __('Activity log #:id deleted successfully.', ['id' => $activityLog->id]));
    }

    /**
     * Clear all or older activity logs from database.
     */
    public function clear(Request $request): RedirectResponse
    {
        Gate::authorize('activity-logs.delete');

        $scope = $request->input('scope', '30_days');

        if ($scope === 'all') {
            ActivityLog::query()->truncate();
            $message = __('All activity logs cleared successfully.');
        } elseif ($scope === '7_days') {
            $deleted = ActivityLog::query()->where('created_at', '<', now()->subDays(7))->delete();
            $message = __(':count activity log(s) older than 7 days deleted successfully.', ['count' => $deleted]);
        } else {
            $deleted = ActivityLog::query()->where('created_at', '<', now()->subDays(30))->delete();
            $message = __(':count activity log(s) older than 30 days deleted successfully.', ['count' => $deleted]);
        }

        return redirect()->route('activity-logs.index')->with('success', $message);
    }
}
