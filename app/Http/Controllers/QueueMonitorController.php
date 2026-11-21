<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\QueueMonitor;
use App\Services\QueueMonitorService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class QueueMonitorController extends Controller
{
    public function __construct(
        protected QueueMonitorService $queueMonitorService
    ) {}

    /**
     * Display queue statistics, pending jobs, failed jobs, batches, and history.
     */
    public function index(Request $request): View
    {
        Gate::authorize('queue-monitor.view');

        $tab = $request->string('tab')->toString();
        if (! in_array($tab, ['pending', 'failed', 'batches', 'history'], true)) {
            $tab = 'pending';
        }

        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();

        return view('modules.queue-monitor.index', [
            'tab' => $tab,
            'stats' => $this->queueMonitorService->getStats(),
            'pendingJobs' => $this->queueMonitorService->paginatePendingJobs($search),
            'failedJobs' => $this->queueMonitorService->paginateFailedJobs($search),
            'batches' => $this->queueMonitorService->paginateBatches(),
            'histories' => $this->queueMonitorService->paginateHistory($search, $status),
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
        ]);
    }

    /**
     * Process pending queue jobs synchronously until empty.
     */
    public function work(): RedirectResponse
    {
        Gate::authorize('queue-monitor.create');

        $output = $this->queueMonitorService->workPendingQueue();

        ActivityLog::record(
            module: 'queue-monitor',
            action: 'worked_queue',
            description: 'Processed pending queue jobs via queue:work --stop-when-empty',
            subjectLabel: 'queue:work',
            before: null,
            after: ['output' => $output],
            subjectId: 'queue:work',
        );

        return redirect()
            ->route('queue-monitor.index', ['tab' => 'history'])
            ->with('success', $output !== '' ? __('Queue worker finished processing pending jobs.') : __('No pending jobs to process.'));
    }

    /**
     * Retry a single failed job by UUID.
     */
    public function retryFailed(string $uuid): RedirectResponse
    {
        Gate::authorize('queue-monitor.create');

        $this->queueMonitorService->retryFailedJob($uuid);

        ActivityLog::record(
            module: 'queue-monitor',
            action: 'retried_job',
            description: "Retried failed job {$uuid}",
            subjectLabel: $uuid,
            before: null,
            after: ['uuid' => $uuid],
            subjectId: $uuid,
        );

        return redirect()
            ->route('queue-monitor.index', ['tab' => 'failed'])
            ->with('success', __('Failed job has been pushed back onto the queue.'));
    }

    /**
     * Retry all failed jobs.
     */
    public function retryAllFailed(): RedirectResponse
    {
        Gate::authorize('queue-monitor.create');

        $this->queueMonitorService->retryAllFailedJobs();

        ActivityLog::record(
            module: 'queue-monitor',
            action: 'retried_all_jobs',
            description: 'Retried all failed queue jobs',
            subjectLabel: 'all-failed-jobs',
            before: null,
            after: ['retried' => 'all'],
            subjectId: 'all-failed-jobs',
        );

        return redirect()
            ->route('queue-monitor.index', ['tab' => 'failed'])
            ->with('success', __('All failed jobs have been pushed back onto the queue.'));
    }

    /**
     * Delete a single failed job by UUID.
     */
    public function forgetFailed(string $uuid): RedirectResponse
    {
        Gate::authorize('queue-monitor.delete');

        $this->queueMonitorService->forgetFailedJob($uuid);

        ActivityLog::record(
            module: 'queue-monitor',
            action: 'deleted_failed_job',
            description: "Deleted failed job {$uuid}",
            subjectLabel: $uuid,
            before: ['uuid' => $uuid],
            after: null,
            subjectId: $uuid,
        );

        return redirect()
            ->route('queue-monitor.index', ['tab' => 'failed'])
            ->with('success', __('Failed job deleted successfully.'));
    }

    /**
     * Flush all failed jobs.
     */
    public function flushFailed(): RedirectResponse
    {
        Gate::authorize('queue-monitor.delete');

        $this->queueMonitorService->flushFailedJobs();

        ActivityLog::record(
            module: 'queue-monitor',
            action: 'flushed_failed_jobs',
            description: 'Flushed all failed queue jobs',
            subjectLabel: 'all-failed-jobs',
            before: null,
            after: ['flushed' => true],
            subjectId: 'all-failed-jobs',
        );

        return redirect()
            ->route('queue-monitor.index', ['tab' => 'failed'])
            ->with('success', __('All failed jobs flushed successfully.'));
    }

    /**
     * Delete a pending job by ID.
     */
    public function destroyPending(int $id): RedirectResponse
    {
        Gate::authorize('queue-monitor.delete');

        $this->queueMonitorService->deletePendingJob($id);

        ActivityLog::record(
            module: 'queue-monitor',
            action: 'deleted_pending_job',
            description: "Deleted pending job #{$id}",
            subjectLabel: (string) $id,
            before: ['id' => $id],
            after: null,
            subjectId: (string) $id,
        );

        return redirect()
            ->route('queue-monitor.index', ['tab' => 'pending'])
            ->with('success', __('Pending job deleted successfully.'));
    }

    /**
     * Clear all pending jobs.
     */
    public function clearPending(): RedirectResponse
    {
        Gate::authorize('queue-monitor.delete');

        $count = $this->queueMonitorService->clearPendingJobs();

        ActivityLog::record(
            module: 'queue-monitor',
            action: 'cleared_pending_jobs',
            description: "Cleared {$count} pending job(s)",
            subjectLabel: 'all-pending-jobs',
            before: ['count' => $count],
            after: ['count' => 0],
            subjectId: 'all-pending-jobs',
        );

        return redirect()
            ->route('queue-monitor.index', ['tab' => 'pending'])
            ->with('success', __(':count pending job(s) cleared.', ['count' => $count]));
    }

    /**
     * Clear queue execution monitor history.
     */
    public function clearHistory(): RedirectResponse
    {
        Gate::authorize('queue-monitor.delete');

        $count = QueueMonitor::query()->count();
        QueueMonitor::query()->delete();

        ActivityLog::record(
            module: 'queue-monitor',
            action: 'cleared_history',
            description: "Cleared {$count} queue monitor history record(s)",
            subjectLabel: 'queue-monitor-history',
            before: ['count' => $count],
            after: ['count' => 0],
            subjectId: 'queue-monitor-history',
        );

        return redirect()
            ->route('queue-monitor.index', ['tab' => 'history'])
            ->with('success', __(':count history record(s) cleared.', ['count' => $count]));
    }
}
