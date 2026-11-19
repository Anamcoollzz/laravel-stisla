<?php

namespace App\Http\Controllers\LP;

use App\Http\Controllers\Controller;
use App\Services\RecruitmentDataService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class WorkerCatalogController extends Controller
{
    public function __construct(private RecruitmentDataService $recruitmentData) {}

    /**
     * Display the searchable, filterable, paginated worker catalog.
     */
    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->string('search')->trim()->toString(),
            'category' => $request->string('category')->trim()->toString(),
            'experience' => $request->string('experience')->trim()->toString(),
            'age_range' => $request->string('age_range')->trim()->toString(),
            'origin' => $request->string('origin')->trim()->toString(),
            'status' => $request->string('status')->trim()->toString(),
            'sort' => $request->string('sort', 'newest')->trim()->toString(),
        ];

        $allWorkers = $this->recruitmentData->getWorkers();
        $filteredWorkers = $this->recruitmentData->getWorkers($filters);
        $perPage = 6;
        $currentPage = max(1, $request->integer('page', 1));
        $paginatedItems = $filteredWorkers->forPage($currentPage, $perPage)->values();

        $workers = new LengthAwarePaginator(
            $paginatedItems,
            $filteredWorkers->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('lp.pages.catalog', [
            'workers' => $workers,
            'allWorkers' => $allWorkers,
            'totalCatalogCount' => $allWorkers->count(),
            'filters' => $filters,
            'categories' => [
                'ART' => 'Domestic Helper (ART)',
                'Babysitter' => 'Babysitter',
                'Nanny' => 'Nanny',
                'Caregiver' => 'Caregiver',
                'Elderly Care' => 'Elderly Care',
                'Other' => 'Other Manpower Services',
            ],
            'experienceOptions' => [
                'entry' => 'Under 1 Year (Trained Entry)',
                '1-3' => '1 – 3 Years',
                '3-5' => '3 – 5 Years',
                '5+' => '5+ Years',
            ],
            'ageRangeOptions' => [
                '18-25' => '18 – 25 Years',
                '26-35' => '26 – 35 Years',
                '36-45' => '36 – 45 Years',
            ],
            'originOptions' => $this->recruitmentData->getOriginOptions(),
            'statusOptions' => [
                'Available' => 'Available',
                'In Training' => 'In Training',
                'Reserved' => 'Reserved',
            ],
        ]);
    }

    /**
     * Display a specific worker profile detail page.
     */
    public function show(string $slug): View
    {
        $worker = $this->recruitmentData->findWorkerBySlug($slug);

        if ($worker === null) {
            abort(404);
        }

        $currentUrl = route('worker.detail', $worker['slug']);

        return view('lp.pages.worker-detail', [
            'worker' => $worker,
            'whatsappUrl' => $this->recruitmentData->buildCandidateWhatsAppUrl($worker, $currentUrl),
            'relatedWorkers' => $this->recruitmentData->getRelatedWorkers($slug, $worker['category'], 3),
        ]);
    }
}
