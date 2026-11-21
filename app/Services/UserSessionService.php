<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UserSessionService
{
    /**
     * Get summary statistics for active sessions.
     *
     * @return array{total: int, authenticated: int, guests: int, driver: string, table_exists: bool}
     */
    public function getStats(): array
    {
        $table = $this->sessionTable();
        $driver = (string) config('session.driver', 'file');

        if (! Schema::hasTable($table)) {
            return [
                'total' => 0,
                'authenticated' => 0,
                'guests' => 0,
                'driver' => $driver,
                'table_exists' => false,
            ];
        }

        $total = DB::table($table)->count();
        $authenticated = DB::table($table)->whereNotNull('user_id')->count();

        return [
            'total' => $total,
            'authenticated' => $authenticated,
            'guests' => max(0, $total - $authenticated),
            'driver' => $driver,
            'table_exists' => true,
        ];
    }

    /**
     * Paginate active sessions with optional search, user, and type filters.
     *
     * @return LengthAwarePaginator<int, object>
     */
    public function paginateSessions(
        Request $request,
        string $search = '',
        ?int $userId = null,
        string $type = 'all',
        int $perPage = 15
    ): LengthAwarePaginator {
        $table = $this->sessionTable();
        $currentSessionId = $request->hasSession() ? $request->session()->getId() : null;

        if (! Schema::hasTable($table)) {
            return new Paginator([], 0, $perPage);
        }

        $query = DB::table($table.' as s')
            ->leftJoin('users as u', 'u.id', '=', 's.user_id')
            ->select([
                's.id',
                's.user_id',
                's.ip_address',
                's.user_agent',
                's.last_activity',
                'u.name as user_name',
                'u.email as user_email',
                'u.avatar as user_avatar',
                'u.role as user_role',
            ]);

        if ($type === 'authenticated') {
            $query->whereNotNull('s.user_id');
        } elseif ($type === 'guest') {
            $query->whereNull('s.user_id');
        }

        if ($userId !== null) {
            $query->where('s.user_id', $userId);
        }

        $search = trim($search);
        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('u.name', 'like', "%{$search}%")
                    ->orWhere('u.email', 'like', "%{$search}%")
                    ->orWhere('s.ip_address', 'like', "%{$search}%")
                    ->orWhere('s.user_agent', 'like', "%{$search}%");
            });
        }

        $paginator = $query
            ->orderByDesc('s.last_activity')
            ->paginate($perPage)
            ->withQueryString();

        $paginator->setCollection(
            $paginator->getCollection()->map(fn (object $row): object => $this->formatSessionRow($row, $currentSessionId))
        );

        return $paginator;
    }

    /**
     * Get users who currently have active sessions for filter dropdown.
     *
     * @return Collection<int, User>
     */
    public function getUsersWithSessions(): Collection
    {
        $table = $this->sessionTable();
        if (! Schema::hasTable($table)) {
            return collect();
        }

        $userIds = DB::table($table)
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id');

        return User::query()
            ->whereIn('id', $userIds)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    /**
     * Delete a single session by ID. Returns deleted row metadata or null if not found.
     */
    public function revokeSession(string $sessionId): ?object
    {
        $table = $this->sessionTable();
        if (! Schema::hasTable($table)) {
            return null;
        }

        $session = DB::table($table.' as s')
            ->leftJoin('users as u', 'u.id', '=', 's.user_id')
            ->select(['s.id', 's.user_id', 's.ip_address', 'u.email as user_email', 'u.name as user_name'])
            ->where('s.id', $sessionId)
            ->first();

        if (! $session) {
            return null;
        }

        DB::table($table)->where('id', $sessionId)->delete();

        return $session;
    }

    /**
     * Delete all sessions for a specific user (optionally excluding current session ID)
     * and rotate their remember token so Remember Me cookies cannot re-authenticate them.
     */
    public function revokeUserSessions(int $userId, ?string $exceptSessionId = null): int
    {
        User::query()
            ->whereKey($userId)
            ->update(['remember_token' => Str::random(60)]);

        $table = $this->sessionTable();
        if (! Schema::hasTable($table)) {
            return 0;
        }

        $query = DB::table($table)->where('user_id', $userId);

        if ($exceptSessionId !== null) {
            $query->where('id', '!=', $exceptSessionId);
        }

        return $query->delete();
    }

    /**
     * Delete all sessions across the application except the current session ID.
     */
    public function revokeAllOtherSessions(?string $currentSessionId, ?int $currentUserId = null): int
    {
        $userQuery = User::query();
        if ($currentUserId !== null) {
            $userQuery->where('id', '!=', $currentUserId);
        }
        $userQuery->update(['remember_token' => Str::random(60)]);

        $table = $this->sessionTable();
        if (! Schema::hasTable($table)) {
            return 0;
        }

        $query = DB::table($table);

        if ($currentSessionId !== null) {
            $query->where('id', '!=', $currentSessionId);
        }

        return $query->delete();
    }

    private function formatSessionRow(object $row, ?string $currentSessionId): object
    {
        $userAgent = (string) ($row->user_agent ?? '');
        $lastActive = Carbon::createFromTimestamp((int) $row->last_activity);

        return (object) [
            'id' => (string) $row->id,
            'user_id' => $row->user_id,
            'user_name' => $row->user_name,
            'user_email' => $row->user_email,
            'user_avatar' => $row->user_avatar,
            'user_role' => $row->user_role,
            'ip_address' => (string) ($row->ip_address ?: '-'),
            'user_agent' => $userAgent,
            'browser' => $this->detectBrowser($userAgent),
            'platform' => $this->detectPlatform($userAgent),
            'is_mobile' => $this->isMobile($userAgent),
            'is_current' => $currentSessionId !== null && (string) $row->id === $currentSessionId,
            'last_active_at' => $lastActive->format('d M Y, H:i:s'),
            'last_active_human' => $lastActive->diffForHumans(),
        ];
    }

    private function sessionTable(): string
    {
        return (string) config('session.table', 'sessions');
    }

    public function detectBrowser(string $userAgent): string
    {
        return match (true) {
            str_contains($userAgent, 'Edg/') || str_contains($userAgent, 'Edge/') => 'Microsoft Edge',
            str_contains($userAgent, 'OPR/') || str_contains($userAgent, 'Opera') => 'Opera',
            str_contains($userAgent, 'Chrome/') => 'Google Chrome',
            str_contains($userAgent, 'Firefox/') => 'Mozilla Firefox',
            str_contains($userAgent, 'Safari/') && ! str_contains($userAgent, 'Chrome/') => 'Safari',
            $userAgent === '' => __('Unknown'),
            default => __('Other Browser'),
        };
    }

    public function detectPlatform(string $userAgent): string
    {
        return match (true) {
            str_contains($userAgent, 'Windows') => 'Windows',
            str_contains($userAgent, 'Macintosh') || str_contains($userAgent, 'Mac OS') => 'macOS',
            str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad') => 'iOS',
            str_contains($userAgent, 'Android') => 'Android',
            str_contains($userAgent, 'Linux') => 'Linux',
            $userAgent === '' => __('Unknown'),
            default => __('Other OS'),
        };
    }

    public function isMobile(string $userAgent): bool
    {
        return str_contains($userAgent, 'Mobile')
            || str_contains($userAgent, 'Android')
            || str_contains($userAgent, 'iPhone')
            || str_contains($userAgent, 'iPad');
    }
}
