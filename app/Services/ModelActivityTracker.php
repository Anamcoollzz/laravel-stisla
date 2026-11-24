<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\LoginHistory;
use App\Models\LogRequest;
use App\Models\QueueMonitor;
use App\Models\ScheduledTaskRun;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ModelActivityTracker
{
    private static ?bool $hasTable = null;

    private static bool $recording = false;

    /**
     * Models excluded from automatic CRUD tracking to prevent recursion or log noise.
     *
     * @var list<class-string<Model>>
     */
    private const EXCLUDED_MODELS = [
        ActivityLog::class,
        LogRequest::class,
        LoginHistory::class,
        QueueMonitor::class,
        ScheduledTaskRun::class,
    ];

    /**
     * Record an Eloquent model lifecycle event (created, updated, deleted).
     */
    public static function track(string $event, Model $model): void
    {
        if (self::$recording) {
            return;
        }

        if (app()->runningInConsole() && ! app()->runningUnitTests()) {
            return;
        }

        if (in_array($model::class, self::EXCLUDED_MODELS, true)) {
            return;
        }

        self::$hasTable ??= Schema::hasTable('activity_logs');
        if (! self::$hasTable) {
            return;
        }

        $action = match ($event) {
            'created' => 'create',
            'updated' => 'update',
            'deleted' => 'delete',
            default => null,
        };

        if ($action === null) {
            return;
        }

        $before = null;
        $after = null;

        if ($action === 'create') {
            $after = Arr::except($model->getAttributes(), ['updated_at']);
        } elseif ($action === 'update') {
            $changes = Arr::except($model->getChanges(), ['updated_at']);
            if ($changes === []) {
                return;
            }

            $after = $changes;
            $before = Arr::only($model->getOriginal(), array_keys($changes));
        } else {
            $before = Arr::except($model->getOriginal(), ['updated_at']);
        }

        $module = str_replace('_', '-', $model->getTable());
        $modelName = class_basename($model);
        $subjectLabel = self::resolveSubjectLabel($model);
        $description = sprintf('%s %s (%s)', Str::ucfirst($action), $modelName, $subjectLabel);

        self::$recording = true;

        try {
            ActivityLog::record(
                module: $module,
                action: $action,
                description: $description,
                subject: $model,
                subjectLabel: $subjectLabel,
                before: $before,
                after: $after,
            );
        } catch (\Throwable) {
            // Never interrupt model persistence if activity logging fails.
        } finally {
            self::$recording = false;
        }
    }

    /**
     * Resolve a human-readable label for the affected model.
     */
    private static function resolveSubjectLabel(Model $model): string
    {
        foreach (['name', 'title', 'email', 'code', 'slug'] as $attribute) {
            $value = $model->getAttribute($attribute);
            if (filled($value)) {
                return Str::limit((string) $value, 120);
            }
        }

        return '#'.(string) $model->getKey();
    }
}
