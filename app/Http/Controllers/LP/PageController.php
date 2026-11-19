<?php

namespace App\Http\Controllers\LP;

use App\Http\Controllers\Controller;
use App\Models\LP\CompanyIntroduction;
use App\Models\LP\CompanyMilestone;
use App\Models\LP\CompanyValue;
use App\Models\LP\ContactInquiry;
use App\Models\LP\VisionMission;
use App\Services\RecruitmentDataService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'teamValues' => $this->resolveCompanyValues(),
            'milestones' => $this->resolveMilestones(),
            'visionMission' => Schema::hasTable('vision_missions') ? VisionMission::query()->first() : null,
        ]);
    }

    /**
     * Get active company values from database with fallback defaults.
     *
     * @return array<int, array<string, string>>
     */
    private function resolveCompanyValues(): array
    {
        if (Schema::hasTable('company_values')) {
            $dbValues = CompanyValue::query()
                ->active()
                ->orderBy('order')
                ->orderBy('id')
                ->get();

            if ($dbValues->isNotEmpty()) {
                return $dbValues->map(fn (CompanyValue $item): array => [
                    'title' => $item->title,
                    'description' => (string) ($item->description ?? ''),
                    'icon' => $item->icon,
                ])->all();
            }
        }

        return [
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
        ];
    }

    /**
     * Get active milestones from database with fallback defaults.
     *
     * @return array<int, array<string, string>>
     */
    private function resolveMilestones(): array
    {
        if (Schema::hasTable('company_milestones')) {
            $dbMilestones = CompanyMilestone::query()
                ->active()
                ->orderBy('order')
                ->orderBy('id')
                ->get();

            if ($dbMilestones->isNotEmpty()) {
                return $dbMilestones->map(fn (CompanyMilestone $item): array => [
                    'year' => $item->year,
                    'title' => $item->title,
                    'description' => (string) ($item->description ?? ''),
                ])->all();
            }
        }

        return [
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
        ];
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

    /**
     * Store a new public contact inquiry with captcha verification.
     */
    public function storeContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:5000'],
            'captcha' => ['required', 'captcha'],
        ], [
            'captcha.captcha' => __('Kode captcha tidak sesuai. Silakan coba lagi.'),
        ]);

        ContactInquiry::query()->create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
            'is_read' => false,
        ]);

        return redirect()
            ->route('contact')
            ->with('success', __('Pesan Anda berhasil dikirim! Tim kami akan segera menghubungi Anda.'));
    }
}
