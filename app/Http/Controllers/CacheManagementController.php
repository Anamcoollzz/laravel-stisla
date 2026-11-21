<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Services\CacheManagementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CacheManagementController extends Controller
{
    public function __construct(
        protected CacheManagementService $cacheManagementService
    ) {}

    /**
     * Display the cache management dashboard and database cache entries.
     */
    public function index(Request $request): View
    {
        Gate::authorize('cache-management.view');

        $search = $request->string('search')->toString();

        return view('modules.cache-management.index', [
            'overview' => $this->cacheManagementService->getOverview(),
            'stislaCaches' => $this->cacheManagementService->getStislaCaches(),
            'entries' => $this->cacheManagementService->paginateDatabaseCacheEntries($search),
            'search' => $search,
        ]);
    }

    /**
     * Execute a cache clear or rebuild action.
     */
    public function execute(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => [
                'required',
                'string',
                'in:cache_clear,view_clear,config_clear,config_cache,route_clear,route_cache,event_clear,optimize_clear,permission_reset,stisla_flush',
            ],
        ]);

        $action = $validated['action'];

        if (in_array($action, ['config_cache', 'route_cache'], true)) {
            Gate::authorize('cache-management.edit');
        } else {
            Gate::authorize('cache-management.delete');
        }

        try {
            $message = $this->cacheManagementService->runAction($action);

            ActivityLog::record(
                module: 'cache-management',
                action: $action,
                description: "Executed cache action: {$action}",
                subjectLabel: $action,
                before: null,
                after: ['action' => $action],
                subjectId: $action,
            );

            return redirect()
                ->route('cache-management.index')
                ->with('success', $message);
        } catch (\Throwable $e) {
            return redirect()
                ->route('cache-management.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Forget a specific cache key (by logical key or raw database cache key).
     */
    public function forgetKey(Request $request): RedirectResponse
    {
        Gate::authorize('cache-management.delete');

        $validated = $request->validate([
            'key' => ['required', 'string', 'max:255'],
            'raw' => ['nullable', 'boolean'],
        ]);

        $key = $validated['key'];
        $isRaw = (bool) ($validated['raw'] ?? false);

        $deleted = $this->cacheManagementService->forgetKey($key, $isRaw);

        ActivityLog::record(
            module: 'cache-management',
            action: 'forget_key',
            description: "Forgot cache key: {$key}",
            subjectLabel: $key,
            before: ['key' => $key, 'raw' => $isRaw],
            after: ['deleted' => $deleted],
            subjectId: $key,
        );

        return redirect()
            ->route('cache-management.index')
            ->with('success', __('Cache key ":key" has been cleared.', ['key' => $key]));
    }
}
