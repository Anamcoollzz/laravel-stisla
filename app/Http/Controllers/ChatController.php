<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChatMessageRequest;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ChatController extends Controller
{
    /**
     * Display the chat box interface.
     */
    public function index(Request $request): View
    {
        Gate::authorize('chat.view');

        $authId = (int) $request->user()->id;

        $users = User::query()
            ->where('id', '!=', $authId)
            ->withCount([
                'sentMessages as unread_count' => function ($query) use ($authId) {
                    $query->where('receiver_id', $authId)->where('is_read', false);
                },
            ])
            ->orderByDesc('last_logged_in')
            ->orderBy('name')
            ->get();

        $selectedUserId = $request->query('user');
        $activeUser = $selectedUserId
            ? $users->firstWhere('id', (int) $selectedUserId) ?? $users->first()
            : $users->first();

        $messages = collect();

        if ($activeUser) {
            ChatMessage::query()
                ->where('sender_id', $activeUser->id)
                ->where('receiver_id', $authId)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

            $messages = ChatMessage::query()
                ->betweenUsers($authId, $activeUser->id)
                ->with(['sender', 'receiver'])
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return view('modules.chat.index', [
            'users' => $users,
            'activeUser' => $activeUser,
            'messages' => $messages,
        ]);
    }

    /**
     * Send a new chat message.
     */
    public function send(StoreChatMessageRequest $request, User $user): JsonResponse|RedirectResponse
    {
        Gate::authorize('chat.create');

        $authId = (int) $request->user()->id;

        $chatMessage = ChatMessage::query()->create([
            'sender_id' => $authId,
            'receiver_id' => $user->id,
            'message' => $request->validated('message'),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $chatMessage->id,
                    'text' => e($chatMessage->message),
                    'time' => $chatMessage->created_at?->format('H:i') ?? date('H:i'),
                    'position' => 'chat-right',
                    'picture' => $request->user()->avatar_url,
                ],
            ]);
        }

        return redirect()->route('chat.index', ['user' => $user->id]);
    }

    /**
     * Fetch messages via AJAX (polling or refresh).
     */
    public function fetchMessages(Request $request, User $user): JsonResponse
    {
        Gate::authorize('chat.view');

        $authId = (int) $request->user()->id;
        $afterId = (int) $request->query('after_id', 0);

        $query = ChatMessage::query()->betweenUsers($authId, $user->id);

        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        }

        $newMessages = $query->with('sender')->orderBy('created_at', 'asc')->get();

        ChatMessage::query()
            ->where('sender_id', $user->id)
            ->where('receiver_id', $authId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        $mapped = $newMessages->map(function ($msg) use ($authId) {
            return [
                'id' => $msg->id,
                'text' => e($msg->message),
                'time' => $msg->created_at?->format('H:i') ?? date('H:i'),
                'position' => $msg->sender_id === $authId ? 'chat-right' : 'chat-left',
                'picture' => $msg->sender?->avatar_url ?? asset('assets/img/avatar/avatar-1.png'),
            ];
        });

        return response()->json([
            'success' => true,
            'messages' => $mapped,
        ]);
    }

    /**
     * Mark all received messages as read.
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        Gate::authorize('chat.view');

        ChatMessage::query()
            ->where('receiver_id', $request->user()->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back();
    }

    /**
     * Clear conversation with a user.
     */
    public function clear(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('chat.delete');

        $authId = (int) $request->user()->id;

        ChatMessage::query()
            ->betweenUsers($authId, $user->id)
            ->delete();

        return redirect()
            ->route('chat.index', ['user' => $user->id])
            ->with('success', __('Conversation cleared successfully.'));
    }
}
