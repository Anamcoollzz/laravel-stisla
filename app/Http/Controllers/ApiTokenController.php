<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ApiTokenController extends Controller
{
    /**
     * Display API tokens list and management controls.
     */
    public function index(Request $request): View
    {
        Gate::authorize('api-tokens.view');

        $search = $request->string('search')->toString();
        $userId = $request->filled('user_id') ? $request->integer('user_id') : null;
        $status = $request->string('status')->toString();

        $query = ApiToken::query()->with(['user:id,name,email', 'creator:id,name']);

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('token_prefix', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search): void {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        if ($status === 'active') {
            $query->where(function ($q): void {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
        } elseif ($status === 'expired') {
            $query->whereNotNull('expires_at')->where('expires_at', '<=', now());
        }

        $tokens = $query->orderByDesc('id')->paginate(15)->withQueryString();

        $total = ApiToken::query()->count();
        $active = ApiToken::query()
            ->where(function ($q): void {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->count();

        $stats = [
            'total' => $total,
            'active' => $active,
            'expired' => max(0, $total - $active),
            'used_today' => ApiToken::query()->whereDate('last_used_at', today())->count(),
        ];

        $users = User::query()->orderBy('name')->get(['id', 'name', 'email']);

        return view('modules.api-tokens.index', [
            'tokens' => $tokens,
            'stats' => $stats,
            'users' => $users,
            'availableAbilities' => ApiToken::AVAILABLE_ABILITIES,
            'filters' => [
                'search' => $search,
                'user_id' => $userId,
                'status' => $status,
            ],
        ]);
    }

    /**
     * Issue a new API token for a user.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('api-tokens.create');

        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'name' => ['required', 'string', 'max:120'],
            'abilities' => ['nullable', 'array'],
            'abilities.*' => ['string', Rule::in(ApiToken::AVAILABLE_ABILITIES)],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $user = User::query()->findOrFail($validated['user_id']);
        $abilities = $validated['abilities'] ?? ['*'];
        $expiresAt = ! empty($validated['expires_at']) ? Carbon::parse($validated['expires_at']) : null;

        $issued = ApiToken::issue(
            user: $user,
            name: $validated['name'],
            abilities: $abilities,
            expiresAt: $expiresAt,
            createdBy: $request->user()?->id,
        );

        ActivityLog::record(
            module: 'api-tokens',
            action: 'created',
            description: "Issued API token \"{$issued['token']->name}\" for {$user->email}",
            subject: $issued['token'],
            subjectLabel: $issued['token']->name,
            before: null,
            after: [
                'user_id' => $user->id,
                'abilities' => $issued['token']->abilities,
                'expires_at' => $issued['token']->expires_at?->toDateTimeString(),
            ],
        );

        return redirect()
            ->route('api-tokens.index')
            ->with('success', __('API token created successfully. Copy it now, as it will not be shown again.'))
            ->with('new_plain_token', $issued['plainTextToken'])
            ->with('new_token_name', $issued['token']->name);
    }

    /**
     * Update an existing API token's name, abilities, or expiration.
     */
    public function update(Request $request, ApiToken $apiToken): RedirectResponse
    {
        Gate::authorize('api-tokens.edit');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'abilities' => ['nullable', 'array'],
            'abilities.*' => ['string', Rule::in(ApiToken::AVAILABLE_ABILITIES)],
            'expires_at' => ['nullable', 'date'],
        ]);

        $before = [
            'name' => $apiToken->name,
            'abilities' => $apiToken->abilities,
            'expires_at' => $apiToken->expires_at?->toDateTimeString(),
        ];

        $apiToken->update([
            'name' => $validated['name'],
            'abilities' => $validated['abilities'] ?? ['*'],
            'expires_at' => ! empty($validated['expires_at']) ? Carbon::parse($validated['expires_at']) : null,
        ]);

        ActivityLog::record(
            module: 'api-tokens',
            action: 'updated',
            description: "Updated API token \"{$apiToken->name}\"",
            subject: $apiToken,
            subjectLabel: $apiToken->name,
            before: $before,
            after: [
                'name' => $apiToken->name,
                'abilities' => $apiToken->abilities,
                'expires_at' => $apiToken->expires_at?->toDateTimeString(),
            ],
        );

        return redirect()
            ->route('api-tokens.index')
            ->with('success', __('API token updated successfully.'));
    }

    /**
     * Revoke and delete a specific API token.
     */
    public function destroy(ApiToken $apiToken): RedirectResponse
    {
        Gate::authorize('api-tokens.delete');

        $before = [
            'id' => $apiToken->id,
            'name' => $apiToken->name,
            'user_id' => $apiToken->user_id,
            'token_prefix' => $apiToken->token_prefix,
        ];

        $apiToken->delete();

        ActivityLog::record(
            module: 'api-tokens',
            action: 'revoked',
            description: "Revoked API token \"{$before['name']}\"",
            subjectLabel: $before['name'],
            before: $before,
            after: null,
            subjectId: (string) $before['id'],
            subjectType: ApiToken::class,
        );

        return redirect()
            ->route('api-tokens.index')
            ->with('success', __('API token revoked successfully.'));
    }

    /**
     * Revoke all API tokens belonging to a specific user.
     */
    public function revokeUserTokens(User $user): RedirectResponse
    {
        Gate::authorize('api-tokens.delete');

        $count = ApiToken::query()->where('user_id', $user->id)->delete();

        ActivityLog::record(
            module: 'api-tokens',
            action: 'revoked_user_tokens',
            description: "Revoked {$count} API token(s) for {$user->email}",
            subject: $user,
            subjectLabel: $user->email,
            before: ['token_count' => $count],
            after: ['token_count' => 0],
        );

        return redirect()
            ->route('api-tokens.index')
            ->with('success', __(':count API token(s) for :user revoked.', ['count' => $count, 'user' => $user->name]));
    }
}
