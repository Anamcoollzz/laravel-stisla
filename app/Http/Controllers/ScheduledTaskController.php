<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ScheduledTaskRun;
use App\Services\ScheduledTaskService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ScheduledTaskController extends Controller
{
    public function __construct(
        protected ScheduledTaskService $scheduledTaskService
    ) {}

    /**
     * Display registered scheduled tasks and execution history.
     */
    public function index(Request $request): View
    {
        Gate::authorize('scheduled-tasks.view');

        $status = $request->string('status')->toString();
        $search = $request->string('search')->toString();
        $triggeredBy = $request->string('triggered_by')->toString();

        $tasks = $this->scheduledTaskService->getRegisteredTasks();
        $stats = $this->scheduledTaskService->getStats($tasks);
        $history = $this->scheduledTaskService->paginateHistory($status, $search, $triggeredBy);

        return view('modules.scheduled-tasks.index', [
            'tasks' => $tasks,
            'stats' => $stats,
            'history' => $history,
            'filters' => [
                'status' => $status,
                'search' => $search,
                'triggered_by' => $triggeredBy,
            ],
        ]);
    }

    /**
     * Manually execute a registered scheduled task.
     */
    public function runNow(Request $request): RedirectResponse
    {
        Gate::authorize('scheduled-tasks.create');

        $validated = $request->validate([
            'task_key' => ['required', 'string', 'max:120'],
        ]);

        try {
            $run = $this->scheduledTaskService->runTaskNow($validated['task_key'], $request->user()?->id);

            ActivityLog::record(
                module: 'scheduled-tasks',
                action: 'ran_manual',
                description: "Manually executed scheduled task {$run->command} ({$run->status})",
                subject: $run,
                subjectLabel: $run->command,
                before: null,
                after: [
                    'status' => $run->status,
                    'exit_code' => $run->exit_code,
                    'duration_ms' => $run->duration_ms,
                ],
            );

            $flashType = $run->status === 'success' ? 'success' : 'error';

            return redirect()
                ->route('scheduled-tasks.index')
                ->with($flashType, __('Task ":command" finished with status: :status (:ms ms).', [
                    'command' => $run->command,
                    'status' => $run->status,
                    'ms' => $run->duration_ms ?? 0,
                ]));
        } catch (\Throwable $e) {
            return redirect()
                ->route('scheduled-tasks.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Run `php artisan schedule:run` for due tasks.
     */
    public function runDue(): RedirectResponse
    {
        Gate::authorize('scheduled-tasks.create');

        $output = $this->scheduledTaskService->runDueSchedule();

        ActivityLog::record(
            module: 'scheduled-tasks',
            action: 'ran_schedule',
            description: 'Triggered php artisan schedule:run manually',
            subjectLabel: 'schedule:run',
            before: null,
            after: ['output' => $output],
            subjectId: 'schedule:run',
        );

        return redirect()
            ->route('scheduled-tasks.index')
            ->with('success', $output !== '' ? $output : __('Schedule run completed.'));
    }

    /**
     * Delete a single execution history entry.
     */
    public function destroy(ScheduledTaskRun $scheduledTaskRun): RedirectResponse
    {
        Gate::authorize('scheduled-tasks.delete');

        $command = $scheduledTaskRun->command;
        $id = $scheduledTaskRun->id;
        $scheduledTaskRun->delete();

        ActivityLog::record(
            module: 'scheduled-tasks',
            action: 'deleted_run',
            description: "Deleted scheduled task run #{$id} ({$command})",
            subjectLabel: $command,
            before: ['id' => $id, 'command' => $command],
            after: null,
            subjectId: (string) $id,
            subjectType: ScheduledTaskRun::class,
        );

        return redirect()
            ->route('scheduled-tasks.index')
            ->with('success', __('Execution history entry deleted successfully.'));
    }

    /**
     * Clear all execution history entries.
     */
    public function clear(): RedirectResponse
    {
        Gate::authorize('scheduled-tasks.delete');

        $count = ScheduledTaskRun::query()->count();
        ScheduledTaskRun::query()->delete();

        ActivityLog::record(
            module: 'scheduled-tasks',
            action: 'cleared_history',
            description: "Cleared {$count} scheduled task execution history record(s)",
            subjectLabel: 'all-scheduled-task-runs',
            before: ['count' => $count],
            after: ['count' => 0],
            subjectId: 'all-scheduled-task-runs',
        );

        return redirect()
            ->route('scheduled-tasks.index')
            ->with('success', __(':count execution history record(s) cleared.', ['count' => $count]));
    }
}
