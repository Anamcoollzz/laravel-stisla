<?php

namespace App\Http\Controllers;

use App\Services\SystemHealthService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SystemHealthController extends Controller
{
    public function __construct(
        protected SystemHealthService $healthService
    ) {}

    /**
     * Display the system health diagnostics page (or JSON payload).
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('system-health.view');

        $report = $this->healthService->runChecks();

        if ($request->expectsJson() || $request->query('format') === 'json') {
            $statusCode = $report['overall_status'] === 'critical' ? 503 : 200;

            return response()->json($report, $statusCode);
        }

        return view('modules.system-health.index', [
            'report' => $report,
        ]);
    }
}
