<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppNotificationRequest;
use App\Http\Requests\UpdateAppNotificationRequest;
use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AppNotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index(): View
    {
        Gate::authorize('notifications.view');

        $notifications = AppNotification::query()
            ->with(['user', 'creator'])
            ->latest('id')
            ->get();

        return view('modules.notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * Show the form for creating a new notification or broadcast.
     */
    public function create(): View
    {
        Gate::authorize('notifications.create');

        return view('modules.notifications.create', [
            'types' => AppNotification::types(),
            'users' => User::query()->orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    /**
     * Store a newly created notification or broadcast in storage.
     */
    public function store(StoreAppNotificationRequest $request): RedirectResponse
    {
        Gate::authorize('notifications.create');

        $validated = $request->validated();
        $isBroadcast = $validated['target_type'] === 'all';

        AppNotification::query()->create([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'type' => $validated['type'],
            'icon' => $validated['icon'] ?: 'fas fa-bell',
            'action_url' => $validated['action_url'] ?? null,
            'is_broadcast' => $isBroadcast,
            'user_id' => $isBroadcast ? null : $validated['user_id'],
            'created_by' => $request->user()?->id,
        ]);

        return redirect()
            ->route('notifications.index')
            ->with('success', $isBroadcast ? __('Broadcast notification sent successfully.') : __('Notification created successfully.'));
    }

    /**
     * Display the specified notification.
     */
    public function show(AppNotification $notification): View
    {
        $userId = (int) auth()->id();
        $canViewResource = Gate::allows('notifications.view');
        $isRecipient = $notification->is_broadcast || (int) $notification->user_id === $userId;

        abort_unless($canViewResource || $isRecipient, 403);

        if ($isRecipient) {
            $notification->markAsReadFor($userId);
        }

        $notification->load(['user', 'creator']);

        return view('modules.notifications.show', [
            'notification' => $notification,
        ]);
    }

    /**
     * Show the form for editing the specified notification.
     */
    public function edit(AppNotification $notification): View
    {
        Gate::authorize('notifications.edit');

        return view('modules.notifications.edit', [
            'notification' => $notification,
            'types' => AppNotification::types(),
            'users' => User::query()->orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    /**
     * Update the specified notification in storage.
     */
    public function update(UpdateAppNotificationRequest $request, AppNotification $notification): RedirectResponse
    {
        Gate::authorize('notifications.edit');

        $validated = $request->validated();
        $isBroadcast = $validated['target_type'] === 'all';

        $notification->update([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'type' => $validated['type'],
            'icon' => $validated['icon'] ?: 'fas fa-bell',
            'action_url' => $validated['action_url'] ?? null,
            'is_broadcast' => $isBroadcast,
            'user_id' => $isBroadcast ? null : $validated['user_id'],
        ]);

        return redirect()
            ->route('notifications.index')
            ->with('success', __('Notification updated successfully.'));
    }

    /**
     * Remove the specified notification from storage.
     */
    public function destroy(AppNotification $notification): RedirectResponse
    {
        Gate::authorize('notifications.delete');

        $notification->delete();

        return redirect()
            ->route('notifications.index')
            ->with('success', __('Notification deleted successfully.'));
    }

    /**
     * Mark a single notification as read for the authenticated user and redirect.
     */
    public function markAsRead(AppNotification $notification): RedirectResponse
    {
        $userId = (int) auth()->id();
        abort_unless($notification->is_broadcast || (int) $notification->user_id === $userId, 403);

        $notification->markAsReadFor($userId);

        if (filled($notification->action_url)) {
            return redirect()->to($notification->action_url);
        }

        return redirect()->route('notifications.show', $notification);
    }

    /**
     * Mark all visible notifications as read for the authenticated user.
     */
    public function markAllAsRead(): RedirectResponse
    {
        $userId = (int) auth()->id();
        $ids = AppNotification::query()->forUser($userId)->pluck('id');

        $now = now();
        foreach ($ids as $id) {
            DB::table('app_notification_reads')->updateOrInsert(
                [
                    'app_notification_id' => $id,
                    'user_id' => $userId,
                ],
                [
                    'read_at' => $now,
                ]
            );
        }

        AppNotification::flushNavbarCache($userId);

        return back();
    }
}
