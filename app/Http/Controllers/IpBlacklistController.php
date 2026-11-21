<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\IpBlacklist;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class IpBlacklistController extends Controller
{
    /**
     * Display a listing of blacklisted IP addresses.
     */
    public function index(Request $request): View
    {
        Gate::authorize('ip-blacklists.view');

        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();

        $query = IpBlacklist::query()->with('creator:id,name,email');

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('ip_address', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true)
                ->where(function ($q): void {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                });
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        } elseif ($status === 'expired') {
            $query->where('is_active', true)
                ->whereNotNull('expires_at')
                ->where('expires_at', '<=', now());
        }

        $blacklists = $query
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $total = IpBlacklist::query()->count();
        $activeCount = IpBlacklist::query()
            ->where('is_active', true)
            ->where(function ($q): void {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->count();

        $stats = [
            'total' => $total,
            'active' => $activeCount,
            'inactive' => max(0, $total - $activeCount),
            'hits' => (int) IpBlacklist::query()->sum('blocked_hits'),
        ];

        return view('modules.ip-blacklists.index', [
            'blacklists' => $blacklists,
            'stats' => $stats,
            'currentIp' => $request->ip(),
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
        ]);
    }

    /**
     * Store a newly blacklisted IP address.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('ip-blacklists.create');

        $validated = $request->validate([
            'ip_address' => ['required', 'ip', 'unique:ip_blacklists,ip_address'],
            'reason' => ['nullable', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($validated['ip_address'] === $request->ip()) {
            return redirect()
                ->back()
                ->with('error', __('You cannot blacklist your own current IP address.'));
        }

        $entry = IpBlacklist::query()->create([
            'ip_address' => $validated['ip_address'],
            'reason' => $validated['reason'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => $request->user()?->id,
        ]);

        ActivityLog::record(
            module: 'ip-blacklists',
            action: 'created',
            description: "Blacklisted IP {$entry->ip_address}",
            subject: $entry,
            subjectLabel: $entry->ip_address,
            before: null,
            after: [
                'ip_address' => $entry->ip_address,
                'reason' => $entry->reason,
                'is_active' => $entry->is_active,
                'expires_at' => $entry->expires_at?->toDateTimeString(),
            ],
        );

        return redirect()
            ->back()
            ->with('success', __('IP address :ip has been blacklisted.', ['ip' => $entry->ip_address]));
    }

    /**
     * Update an existing IP blacklist entry.
     */
    public function update(Request $request, IpBlacklist $ipBlacklist): RedirectResponse
    {
        Gate::authorize('ip-blacklists.edit');

        $validated = $request->validate([
            'ip_address' => ['required', 'ip', Rule::unique('ip_blacklists', 'ip_address')->ignore($ipBlacklist->id)],
            'reason' => ['nullable', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($validated['ip_address'] === $request->ip() && $request->boolean('is_active', true)) {
            return redirect()
                ->route('ip-blacklists.index')
                ->with('error', __('You cannot blacklist your own current IP address.'));
        }

        $before = [
            'ip_address' => $ipBlacklist->ip_address,
            'reason' => $ipBlacklist->reason,
            'is_active' => $ipBlacklist->is_active,
            'expires_at' => $ipBlacklist->expires_at?->toDateTimeString(),
        ];

        $ipBlacklist->update([
            'ip_address' => $validated['ip_address'],
            'reason' => $validated['reason'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        ActivityLog::record(
            module: 'ip-blacklists',
            action: 'updated',
            description: "Updated IP blacklist entry {$ipBlacklist->ip_address}",
            subject: $ipBlacklist,
            subjectLabel: $ipBlacklist->ip_address,
            before: $before,
            after: [
                'ip_address' => $ipBlacklist->ip_address,
                'reason' => $ipBlacklist->reason,
                'is_active' => $ipBlacklist->is_active,
                'expires_at' => $ipBlacklist->expires_at?->toDateTimeString(),
            ],
        );

        return redirect()
            ->route('ip-blacklists.index')
            ->with('success', __('IP blacklist entry updated successfully.'));
    }

    /**
     * Toggle active status of a blacklisted IP.
     */
    public function toggleActive(IpBlacklist $ipBlacklist): RedirectResponse
    {
        Gate::authorize('ip-blacklists.edit');

        $before = ['is_active' => $ipBlacklist->is_active];
        $ipBlacklist->update(['is_active' => ! $ipBlacklist->is_active]);

        ActivityLog::record(
            module: 'ip-blacklists',
            action: 'toggled',
            description: ($ipBlacklist->is_active ? 'Activated' : 'Deactivated')." IP blacklist {$ipBlacklist->ip_address}",
            subject: $ipBlacklist,
            subjectLabel: $ipBlacklist->ip_address,
            before: $before,
            after: ['is_active' => $ipBlacklist->is_active],
        );

        return redirect()
            ->route('ip-blacklists.index')
            ->with('success', __('IP blacklist status updated successfully.'));
    }

    /**
     * Remove an IP address from the blacklist.
     */
    public function destroy(IpBlacklist $ipBlacklist): RedirectResponse
    {
        Gate::authorize('ip-blacklists.delete');

        $before = [
            'id' => $ipBlacklist->id,
            'ip_address' => $ipBlacklist->ip_address,
            'reason' => $ipBlacklist->reason,
        ];

        $ipBlacklist->delete();

        ActivityLog::record(
            module: 'ip-blacklists',
            action: 'deleted',
            description: "Removed IP {$before['ip_address']} from blacklist",
            subjectLabel: $before['ip_address'],
            before: $before,
            after: null,
            subjectId: (string) $before['id'],
            subjectType: IpBlacklist::class,
        );

        return redirect()
            ->route('ip-blacklists.index')
            ->with('success', __('IP address removed from blacklist.'));
    }
}
