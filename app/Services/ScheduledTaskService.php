<?php

namespace App\Services;

use App\Models\ScheduledTaskRun;
use Cron\CronExpression;
use Illuminate\Console\Scheduling\CallbackEvent;
use Illuminate\Console\Scheduling\Event as ScheduleEvent;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class ScheduledTaskService
{
    /**
     * Ensure console schedule is loaded and return all registered scheduled events.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getRegisteredTasks(): Collection
    {
        app(ConsoleKernel::class);
        $schedule = app(Schedule::class);

        $latestRuns = collect();
        if (Schema::hasTable('scheduled_task_runs')) {
            $latestRuns = ScheduledTaskRun::query()
                ->orderByDesc('id')
                ->get()
                ->unique('task_key')
                ->keyBy('task_key');
        }

        return collect($schedule->events())->map(function (ScheduleEvent $event) use ($latestRuns): array {
            $command = $this->formatCommand($event);
            $taskKey = $this->taskKey($event);
            $expression = (string) $event->expression;
            $timezone = $event->timezone ?: config('app.timezone', 'UTC');
            $nextRun = $this->nextRunDate($expression, (string) $timezone);

            /** @var ScheduledTaskRun|null $lastRun */
            $lastRun = $latestRuns->get($taskKey);

            return [
                'task_key' => $taskKey,
                'command' => $command,
                'artisan_command' => $this->extractArtisanCommand($event),
                'is_closure' => $event instanceof CallbackEvent,
                'description' => (string) ($event->description ?: '-'),
                'expression' => $expression,
                'timezone' => (string) $timezone,
                'next_run_at' => $nextRun?->format('d M Y, H:i:s') ?? '-',
                'next_run_human' => $nextRun?->diffForHumans() ?? '-',
                'without_overlapping' => (bool) $event->withoutOverlapping,
                'run_in_background' => (bool) $event->runInBackground,
                'on_one_server' => (bool) $event->onOneServer,
                'last_run' => $lastRun,
            ];
        })->values();
    }

    /**
     * Paginate recorded task execution history.
     *
     * @return LengthAwarePaginator<int, ScheduledTaskRun>
     */
    public function paginateHistory(string $status = '', string $search = '', string $triggeredBy = '', int $perPage = 15): LengthAwarePaginator
    {
        $query = ScheduledTaskRun::query()->with('user:id,name,email');

        if ($status !== '' && in_array($status, ['running', 'success', 'failed'], true)) {
            $query->where('status', $status);
        }

        if ($triggeredBy !== '' && in_array($triggeredBy, ['scheduler', 'manual'], true)) {
            $query->where('triggered_by', $triggeredBy);
        }

        if (trim($search) !== '') {
            $term = trim($search);
            $query->where(function ($q) use ($term): void {
                $q->where('command', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhere('output', 'like', "%{$term}%");
            });
        }

        return $query
            ->orderByDesc('started_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get dashboard summary stats for scheduled tasks and runs.
     *
     * @param  Collection<int, array<string, mixed>>  $tasks
     * @return array{registered: int, total_runs: int, success_runs: int, failed_runs: int}
     */
    public function getStats(Collection $tasks): array
    {
        if (! Schema::hasTable('scheduled_task_runs')) {
            return [
                'registered' => $tasks->count(),
                'total_runs' => 0,
                'success_runs' => 0,
                'failed_runs' => 0,
            ];
        }

        return [
            'registered' => $tasks->count(),
            'total_runs' => ScheduledTaskRun::query()->count(),
            'success_runs' => ScheduledTaskRun::query()->where('status', 'success')->count(),
            'failed_runs' => ScheduledTaskRun::query()->where('status', 'failed')->count(),
        ];
    }

    /**
     * Manually execute a registered scheduled Artisan command task.
     */
    public function runTaskNow(string $taskKey, ?int $userId = null): ScheduledTaskRun
    {
        $task = $this->getRegisteredTasks()->firstWhere('task_key', $taskKey);

        if (! $task) {
            throw new InvalidArgumentException(__('Scheduled task was not found.'));
        }

        if ($task['is_closure'] || empty($task['artisan_command'])) {
            throw new InvalidArgumentException(__('Closure-based tasks cannot be executed manually from the web UI.'));
        }

        $startedAt = now();
        $startMicro = microtime(true);

        $run = ScheduledTaskRun::query()->create([
            'task_key' => $task['task_key'],
            'command' => $task['command'],
            'description' => $task['description'] !== '-' ? $task['description'] : null,
            'expression' => $task['expression'],
            'status' => 'running',
            'triggered_by' => 'manual',
            'user_id' => $userId,
            'started_at' => $startedAt,
        ]);

        try {
            $exitCode = Artisan::call($task['artisan_command']);
            $output = trim(Artisan::output());
            $durationMs = (int) round((microtime(true) - $startMicro) * 1000);

            $run->update([
                'status' => $exitCode === 0 ? 'success' : 'failed',
                'exit_code' => $exitCode,
                'duration_ms' => $durationMs,
                'output' => $output !== '' ? $output : null,
                'finished_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $durationMs = (int) round((microtime(true) - $startMicro) * 1000);

            $run->update([
                'status' => 'failed',
                'exit_code' => 1,
                'duration_ms' => $durationMs,
                'output' => $e->getMessage(),
                'finished_at' => now(),
            ]);
        }

        return $run->fresh();
    }

    /**
     * Trigger Laravel's `schedule:run` command on demand.
     */
    public function runDueSchedule(): string
    {
        Artisan::call('schedule:run');

        return trim(Artisan::output());
    }

    /**
     * Build a stable identifier key for a ScheduleEvent.
     */
    public function taskKey(ScheduleEvent $event): string
    {
        return sha1($this->formatCommand($event).'|'.$event->expression);
    }

    /**
     * Format a ScheduleEvent command into a readable string.
     */
    public function formatCommand(ScheduleEvent $event): string
    {
        if ($event instanceof CallbackEvent) {
            return $event->description ? 'Closure: '.$event->description : 'Closure';
        }

        $command = (string) $event->command;

        if (preg_match('/artisan[\'"]?\s+(.+)$/', $command, $matches) === 1) {
            return 'php artisan '.trim($matches[1]);
        }

        return trim($command);
    }

    private function extractArtisanCommand(ScheduleEvent $event): ?string
    {
        if ($event instanceof CallbackEvent) {
            return null;
        }

        $command = (string) $event->command;
        if (preg_match('/artisan[\'"]?\s+(.+)$/', $command, $matches) === 1) {
            return trim($matches[1]);
        }

        return null;
    }

    private function nextRunDate(string $expression, string $timezone): ?Carbon
    {
        try {
            $cron = new CronExpression($expression);
            $next = $cron->getNextRunDate('now', 0, false, $timezone);

            return Carbon::instance($next)->setTimezone(config('app.timezone', 'UTC'));
        } catch (\Throwable) {
            return null;
        }
    }
}
