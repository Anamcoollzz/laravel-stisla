<?php

namespace App\Http\Controllers;

use App\Models\LogRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LogRequestController extends Controller
{
    /**
     * Display a listing of request logs with filters and pagination.
     */
    public function index(Request $request): View
    {
        Gate::authorize('log-requests.view');

        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'method' => trim((string) $request->query('method', '')),
            'device' => trim((string) $request->query('device', '')),
            'is_ajax' => trim((string) $request->query('is_ajax', '')),
            'date_from' => trim((string) $request->query('date_from', '')),
            'date_to' => trim((string) $request->query('date_to', '')),
        ];

        $logRequests = LogRequest::query()
            ->with('user')
            ->when($filters['search'] !== '', function ($query) use ($filters) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('uri', 'like', "%{$search}%")
                        ->orWhere('ip', 'like', "%{$search}%")
                        ->orWhere('user_agent', 'like', "%{$search}%")
                        ->orWhere('query_string', 'like', "%{$search}%")
                        ->orWhere('browser', 'like', "%{$search}%")
                        ->orWhere('platform', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
                });
            })
            ->when($filters['method'] !== '', fn ($q) => $q->where('method', $filters['method']))
            ->when($filters['device'] !== '', fn ($q) => $q->where('device', $filters['device']))
            ->when($filters['is_ajax'] === '1', fn ($q) => $q->where('is_ajax', true))
            ->when($filters['is_ajax'] === '0', fn ($q) => $q->where('is_ajax', false))
            ->when($filters['date_from'] !== '', fn ($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
            ->when($filters['date_to'] !== '', fn ($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $stats = [
            'total' => LogRequest::count(),
            'today' => LogRequest::whereDate('created_at', today())->count(),
            'write' => LogRequest::whereIn('method', ['POST', 'PUT', 'PATCH', 'DELETE'])->count(),
            'ajax' => LogRequest::where('is_ajax', true)->count(),
        ];

        $availableMethods = LogRequest::query()->select('method')->distinct()->pluck('method')->filter()->values();
        $availableDevices = LogRequest::query()->select('device')->distinct()->pluck('device')->filter()->values();

        return view('modules.log-requests.index', compact('logRequests', 'filters', 'stats', 'availableMethods', 'availableDevices'));
    }

    /**
     * Display the specified request log detail.
     */
    public function show(LogRequest $logRequest): View
    {
        Gate::authorize('log-requests.view');

        $logRequest->load('user');

        return view('modules.log-requests.show', compact('logRequest'));
    }

    /**
     * Remove the specified request log from database.
     */
    public function destroy(LogRequest $logRequest): RedirectResponse
    {
        Gate::authorize('log-requests.delete');

        $logRequest->delete();

        return redirect()->route('log-requests.index')
            ->with('success', __('Log request #').$logRequest->id.__(' berhasil dihapus.'));
    }

    /**
     * Clear all or older logs from database.
     */
    public function clear(Request $request): RedirectResponse
    {
        Gate::authorize('log-requests.delete');

        $scope = $request->input('scope', '30_days');

        if ($scope === 'all') {
            LogRequest::truncate();
            $message = __('Semua log request berhasil dibersihkan.');
        } elseif ($scope === '7_days') {
            $deleted = LogRequest::where('created_at', '<', now()->subDays(7))->delete();
            $message = __(':count log request yang lebih lama dari 7 hari berhasil dihapus.', ['count' => $deleted]);
        } else {
            $deleted = LogRequest::where('created_at', '<', now()->subDays(30))->delete();
            $message = __(':count log request yang lebih lama dari 30 hari berhasil dihapus.', ['count' => $deleted]);
        }

        return redirect()->route('log-requests.index')->with('success', $message);
    }
}
