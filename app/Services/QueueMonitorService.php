<?php

namespace App\Services;

use App\Models\QueueMonitor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class QueueMonitorService
{
    /**
     * Gather summary statistics across pending jobs, failed jobs, batches, and monitor history.
     *
     * @return array<string, mixed>
     */
    public function getStats(): array
    {
        $connection = (string) config('queue.default', 'database');

        $pendingCount = Schema::hasTable('jobs') ? DB::table('jobs')->count() : 0;
        $failedCount = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;
        $batchesCount = Schema::hasTable('job_batches') ? DB::table('job_batches')->count() : 0;
        $processedCount = Schema::hasTable('queue_monitors') ? QueueMonitor::query()->where('status', 'succeeded')->count() : 0;

        return [
            'connection' => $connection,
            'pending' => $pendingCount,
            'failed' => $failedCount,
            'batches' => $batchesCount,
            'processed' => $processedCount,
        ];
    }

    /**
     * Paginate pending jobs from the `jobs` table.
     *
     * @return LengthAwarePaginator<int, object>
     */
    public function paginatePendingJobs(string $search = '', int $perPage = 15): LengthAwarePaginator
    {
        if (! Schema::hasTable('jobs')) {
            return new Paginator([], 0, $perPage);
        }

        $query = DB::table('jobs')->select(['id', 'queue', 'payload', 'attempts', 'reserved_at', 'available_at', 'created_at']);

        if (trim($search) !== '') {
            $term = trim($search);
            $query->where(function ($q) use ($term): void {
                $q->where('queue', 'like', "%{$term}%")
                    ->orWhere('payload', 'like', "%{$term}%");
            });
        }

        $paginator = $query->orderByDesc('id')->paginate($perPage, ['*'], 'pending_page')->withQueryString();

        $paginator->setCollection(
            $paginator->getCollection()->map(function (object $row): object {
                $payload = json_decode((string) $row->payload, true);
                $displayName = is_array($payload) ? (string) ($payload['displayName'] ?? __('Unknown Job')) : __('Unknown Job');

                return (object) [
                    'id' => (int) $row->id,
                    'queue' => (string) $row->queue,
                    'name' => $displayName,
                    'attempts' => (int) $row->attempts,
                    'is_reserved' => $row->reserved_at !== null,
                    'available_at' => Carbon::createFromTimestamp((int) $row->available_at)->format('d M Y, H:i:s'),
                    'created_at' => Carbon::createFromTimestamp((int) $row->created_at)->diffForHumans(),
                ];
            })
        );

        return $paginator;
    }

    /**
     * Paginate failed jobs from the `failed_jobs` table.
     *
     * @return LengthAwarePaginator<int, object>
     */
    public function paginateFailedJobs(string $search = '', int $perPage = 15): LengthAwarePaginator
    {
        if (! Schema::hasTable('failed_jobs')) {
            return new Paginator([], 0, $perPage);
        }

        $query = DB::table('failed_jobs')->select(['id', 'uuid', 'connection', 'queue', 'payload', 'exception', 'failed_at']);

        if (trim($search) !== '') {
            $term = trim($search);
            $query->where(function ($q) use ($term): void {
                $q->where('queue', 'like', "%{$term}%")
                    ->orWhere('payload', 'like', "%{$term}%")
                    ->orWhere('exception', 'like', "%{$term}%");
            });
        }

        $paginator = $query->orderByDesc('id')->paginate($perPage, ['*'], 'failed_page')->withQueryString();

        $paginator->setCollection(
            $paginator->getCollection()->map(function (object $row): object {
                $payload = json_decode((string) $row->payload, true);
                $displayName = is_array($payload) ? (string) ($payload['displayName'] ?? __('Unknown Job')) : __('Unknown Job');

                return (object) [
                    'id' => (int) $row->id,
                    'uuid' => (string) $row->uuid,
                    'connection' => (string) $row->connection,
                    'queue' => (string) $row->queue,
                    'name' => $displayName,
                    'exception' => (string) $row->exception,
                    'exception_summary' => str((string) $row->exception)->before("\n")->limit(140)->toString(),
                    'failed_at' => Carbon::parse((string) $row->failed_at)->format('d M Y, H:i:s'),
                    'failed_human' => Carbon::parse((string) $row->failed_at)->diffForHumans(),
                ];
            })
        );

        return $paginator;
    }

    /**
     * Paginate job batches from the `job_batches` table.
     *
     * @return LengthAwarePaginator<int, object>
     */
    public function paginateBatches(int $perPage = 15): LengthAwarePaginator
    {
        if (! Schema::hasTable('job_batches')) {
            return new Paginator([], 0, $perPage);
        }

        $paginator = DB::table('job_batches')
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'batch_page')
            ->withQueryString();

        $paginator->setCollection(
            $paginator->getCollection()->map(function (object $row): object {
                $total = max(0, (int) $row->total_jobs);
                $pending = max(0, (int) $row->pending_jobs);
                $processed = max(0, $total - $pending);
                $progress = $total > 0 ? (int) round(($processed / $total) * 100) : 100;

                return (object) [
                    'id' => (string) $row->id,
                    'name' => (string) ($row->name ?: __('Unnamed Batch')),
                    'total_jobs' => $total,
                    'pending_jobs' => $pending,
                    'failed_jobs' => (int) $row->failed_jobs,
                    'progress' => $progress,
                    'cancelled' => $row->cancelled_at !== null,
                    'finished' => $row->finished_at !== null,
                    'created_at' => Carbon::createFromTimestamp((int) $row->created_at)->diffForHumans(),
                ];
            })
        );

        return $paginator;
    }

    /**
     * Paginate processed/failed job history from `queue_monitors`.
     *
     * @return LengthAwarePaginator<int, QueueMonitor>
     */
    public function paginateHistory(string $search = '', string $status = '', int $perPage = 15): LengthAwarePaginator
    {
        if (! Schema::hasTable('queue_monitors')) {
            return new Paginator([], 0, $perPage);
        }

        $query = QueueMonitor::query();

        if ($status !== '' && in_array($status, ['running', 'succeeded', 'failed'], true)) {
            $query->where('status', $status);
        }

        if (trim($search) !== '') {
            $term = trim($search);
            $query->where(function ($q) use ($term): void {
                $q->where('job_name', 'like', "%{$term}%")
                    ->orWhere('queue', 'like', "%{$term}%")
                    ->orWhere('exception', 'like', "%{$term}%");
            });
        }

        return $query
            ->orderByDesc('started_at')
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'history_page')
            ->withQueryString();
    }

    public function workPendingQueue(): string
    {
        Artisan::call('queue:work', [
            '--stop-when-empty' => true,
            '--tries' => 3,
        ]);

        return trim(Artisan::output());
    }

    public function retryFailedJob(string $uuid): void
    {
        Artisan::call('queue:retry', ['id' => [$uuid]]);
    }

    public function retryAllFailedJobs(): void
    {
        Artisan::call('queue:retry', ['id' => ['all']]);
    }

    public function forgetFailedJob(string $uuid): void
    {
        Artisan::call('queue:forget', ['id' => $uuid]);
    }

    public function flushFailedJobs(): void
    {
        Artisan::call('queue:flush');
    }

    public function deletePendingJob(int $id): bool
    {
        if (! Schema::hasTable('jobs')) {
            return false;
        }

        return DB::table('jobs')->where('id', $id)->delete() > 0;
    }

    public function clearPendingJobs(): int
    {
        if (! Schema::hasTable('jobs')) {
            return 0;
        }

        return DB::table('jobs')->delete();
    }
}
