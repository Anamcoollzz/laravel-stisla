<?php

namespace App\Http\Controllers\LP;

use App\Http\Controllers\Controller;
use App\Models\LP\CompanyIntroduction;
use App\Services\RecruitmentDataService;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(private RecruitmentDataService $recruitmentData) {}

    /**
     * Display the public company homepage.
     */
    public function home(): View
    {
        return view('lp.pages.home', [
            'companyIntro' => Schema::hasTable('company_introductions') ? CompanyIntroduction::query()->first() : null,
            'featuredWorkers' => $this->recruitmentData->getFeaturedWorkers(4),
            'services' => $this->recruitmentData->getServices(),
            'stats' => $this->recruitmentData->getCompanyStats(),
            'whyChooseUs' => $this->recruitmentData->getWhyChooseUs(),
            'processSteps' => $this->recruitmentData->getRecruitmentSteps(),
            'testimonials' => $this->recruitmentData->getTestimonials(),
            'faqs' => $this->recruitmentData->getFaqs(),
        ]);
    }

    /**
     * Display the About Us company profile page.
     */
    public function about(): View
    {
        return view('lp.pages.about', [
            'stats' => $this->recruitmentData->getCompanyStats(),
            'whyChooseUs' => $this->recruitmentData->getWhyChooseUs(),
            'galleryPreview' => array_slice($this->recruitmentData->getGalleryItems(), 0, 3),
            'teamValues' => [
                [
                    'title' => 'Integrity & Honesty',
                    'description' => 'Transparent screening and accurate candidate representation build lasting trust with every household.',
                    'icon' => 'shield-check',
                ],
                [
                    'title' => 'Compassionate Care',
                    'description' => 'We place helpers and caregivers who genuinely respect children, seniors, and family well-being.',
                    'icon' => 'heart-handshake',
                ],
                [
                    'title' => 'Professional Readiness',
                    'description' => 'Practical hospitality, hygiene, and safety orientation prepares candidates before deployment.',
                    'icon' => 'award',
                ],
                [
                    'title' => 'Accountable Support',
                    'description' => 'Our consultants remain reachable after placement day to support smooth long-term adaptation.',
                    'icon' => 'headphones',
                ],
            ],
            'milestones' => [
                [
                    'year' => '2016',
                    'title' => 'Agency Foundation in Bekasi',
                    'description' => 'Established to connect households in Greater Jakarta with screened, dependable domestic staff.',
                ],
                [
                    'year' => '2019',
                    'title' => 'Practical Training Program',
                    'description' => 'Launched structured training covering infant hygiene, eldercare, and modern household standards.',
                ],
                [
                    'year' => '2022',
                    'title' => 'Expanded Placement Coverage',
                    'description' => 'Extended coordinated placement services across Jabodetabek, Bandung, and major Indonesian cities.',
                ],
                [
                    'year' => '2026',
                    'title' => 'Digital Candidate Directory',
                    'description' => 'Introduced transparent online candidate profiles and streamlined WhatsApp video interview scheduling.',
                ],
            ],
        ]);
    }

    /**
     * Display the dedicated services page.
     */
    public function services(): View
    {
        return view('lp.pages.services', [
            'services' => $this->recruitmentData->getServices(),
            'processSteps' => $this->recruitmentData->getRecruitmentSteps(),
            'faqs' => $this->recruitmentData->getFaqs(),
        ]);
    }

    /**
     * Display the visual gallery page.
     */
    public function gallery(): View
    {
        $galleryItems = $this->recruitmentData->getGalleryItems();
        $galleryCategories = [
            'Training Activities',
            'Recruitment Activities',
            'Worker Preparation',
            'Company Activities',
            'Events',
            'Documentation',
        ];

        return view('lp.pages.gallery', [
            'galleryItems' => $galleryItems,
            'galleryCategories' => $galleryCategories,
        ]);
    }

    /**
     * Display the company contact page.
     */
    public function contact(): View
    {
        return view('lp.pages.contact', [
            'company' => config('company'),
        ]);
    }
}
