<?php

namespace App\Services;

use App\Models\LP\CompanyStat;
use App\Models\LP\LpFaq;
use App\Models\LP\LpGallery;
use App\Models\LP\RecruitmentService;
use App\Models\LP\RecruitmentStep;
use App\Models\LP\RecruitmentTestimonial;
use App\Models\LP\WhyChooseUs;
use App\Models\LP\Worker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RecruitmentDataService
{
    /**
     * Get trust and company statistics for Homepage and About Us.
     *
     * @return array<int, array<string, string>>
     */
    public function getCompanyStats(): array
    {
        if (Schema::hasTable('company_stats')) {
            $dbStats = CompanyStat::query()
                ->active()
                ->orderBy('order')
                ->orderBy('id')
                ->get();

            if ($dbStats->isNotEmpty()) {
                return $dbStats->map(fn (CompanyStat $stat): array => [
                    'value' => $stat->value,
                    'label' => $stat->label,
                    'description' => (string) ($stat->description ?? ''),
                    'icon' => $stat->icon,
                ])->all();
            }
        }

        return [
            [
                'value' => '10+',
                'label' => 'Years of Experience',
                'description' => 'Sample benchmark representing dedicated domestic & care recruitment experience.',
                'icon' => 'award',
            ],
            [
                'value' => '850+',
                'label' => 'Workers Available',
                'description' => 'Pre-screened candidates across childcare, eldercare, and household support.',
                'icon' => 'users',
            ],
            [
                'value' => '2,100+',
                'label' => 'Successful Placements',
                'description' => 'Families and households matched through structured screening processes.',
                'icon' => 'check-circle-2',
            ],
            [
                'value' => '1,450+',
                'label' => 'Satisfied Clients',
                'description' => 'Returning households served across Jabodetabek and major Indonesian cities.',
                'icon' => 'heart-handshake',
            ],
        ];
    }

    /**
     * Get all six manpower recruitment service categories.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getServices(): array
    {
        if (Schema::hasTable('recruitment_services')) {
            $dbServices = RecruitmentService::query()
                ->active()
                ->orderBy('order')
                ->orderBy('id')
                ->get();

            if ($dbServices->isNotEmpty()) {
                return $dbServices->map(fn (RecruitmentService $service): array => [
                    'slug' => $service->slug,
                    'category_key' => $service->category_key,
                    'title' => $service->title,
                    'subtitle' => (string) ($service->subtitle ?? ''),
                    'icon' => $service->icon,
                    'image' => (string) ($service->image ?? ''),
                    'image_url' => $service->image_url,
                    'short_description' => (string) ($service->short_description ?? ''),
                    'description' => (string) ($service->description ?? ''),
                    'responsibilities' => $service->responsibilities ?? [],
                    'skills' => $service->skills ?? [],
                    'benefits' => $service->benefits ?? [],
                ])->all();
            }
        }

        return [
            [
                'slug' => 'domestic-helper',
                'category_key' => 'ART',
                'title' => 'Domestic Helper (ART)',
                'subtitle' => 'Household Upkeep & Culinary Support',
                'icon' => 'home',
                'image' => 'lp-assets/images/services/art.jpg',
                'short_description' => 'Dependable domestic helpers trained in daily housekeeping, laundry care, and healthy family meal preparation.',
                'description' => 'Our Domestic Helpers (Asisten Rumah Tangga) are trained in structured residential cleaning, appliance operation, fabric care, and hygienic daily cooking to keep busy households running smoothly.',
                'responsibilities' => [
                    'Daily sweeping, mopping, dusting, and room sanitization',
                    'Laundry sorting, washing, steam ironing, and wardrobe care',
                    'Preparing hygienic everyday Indonesian home meals',
                    'Managing kitchen cleanliness and household pantry routines',
                ],
                'skills' => [
                    'Residential Housekeeping',
                    'Home Cooking',
                    'Modern Appliances',
                    'Laundry & Ironing',
                ],
                'benefits' => [
                    'Verified identity and family reference checks',
                    'Structured pre-placement housekeeping orientation',
                    'Dedicated consultant follow-up after placement',
                ],
            ],
            [
                'slug' => 'babysitter',
                'category_key' => 'Babysitter',
                'title' => 'Babysitter',
                'subtitle' => 'Newborn & Infant Care Specialists',
                'icon' => 'baby',
                'image' => 'lp-assets/images/services/babysitter.jpg',
                'short_description' => 'Attentive babysitters skilled in newborn hygiene, bottle sterilization, sleep schedules, and infant feeding routines.',
                'description' => 'Prepared specifically for newborns and infants (0–24 months), our babysitters are carefully selected for warmth, patience, nursery hygiene, and calm attentiveness.',
                'responsibilities' => [
                    'Safe infant bathing, dressing, and daily hygiene care',
                    'Sterilizing feeding bottles and preparing infant formula or MPASI',
                    'Tracking feeding, nap, and diaper routines accurately',
                    'Maintaining clean nursery spaces and baby laundry',
                ],
                'skills' => [
                    'Infant Bathing & Hygiene',
                    'Bottle Sterilization',
                    'MPASI Prep',
                    'Sleep Routine Support',
                ],
                'benefits' => [
                    'Health readiness and medical clearance on file',
                    'Trained in infant safety and soothing methods',
                    'Live video or office interview before placement',
                ],
            ],
            [
                'slug' => 'nanny',
                'category_key' => 'Nanny',
                'title' => 'Nanny',
                'subtitle' => 'Toddler & Childhood Companion',
                'icon' => 'book-open',
                'image' => 'lp-assets/images/services/nanny.jpg',
                'short_description' => 'Caring nannies focused on active toddlers, school routines, creative play, and healthy daily habits.',
                'description' => 'Tailored for toddlers and school-aged children, our nannies balance safe supervision with structured routines, age-appropriate play, nutritious snacks, and supportive communication.',
                'responsibilities' => [
                    'Preparing nutritious child meals, snacks, and school lunchboxes',
                    'Guiding positive hygiene, tidy-up, and bedtime habits',
                    'Supervising creative play, reading, and outdoor walks',
                    'Coordinating daily schedules smoothly with parents',
                ],
                'skills' => [
                    'Early Childhood Routines',
                    'Creative Play',
                    'Child Nutrition',
                    'Patient Communication',
                ],
                'benefits' => [
                    'Candidates screened for patience and child-friendly demeanor',
                    'Optional bilingual or PAUD-educated profiles available',
                    'Ongoing consultant assistance during adaptation',
                ],
            ],
            [
                'slug' => 'caregiver',
                'category_key' => 'Caregiver',
                'title' => 'Caregiver',
                'subtitle' => 'Home Health & Recovery Assistance',
                'icon' => 'heart-pulse',
                'image' => 'lp-assets/images/services/caregiver.jpg',
                'short_description' => 'Trained caregivers supporting post-hospital recovery, vital sign observation, and daily mobility assistance.',
                'description' => 'Our Caregivers assist clients needing attentive day-to-day home support following hospital discharge or ongoing physical recovery, working respectfully alongside family members.',
                'responsibilities' => [
                    'Monitoring blood pressure, temperature, and daily wellness logs',
                    'Assisting with safe wheelchair transfer and gentle movement',
                    'Reminding prescribed medication schedules accurately',
                    'Supporting personal grooming, bathing, and hydration routines',
                ],
                'skills' => [
                    'Vital Monitoring',
                    'Mobility Transfer',
                    'Medication Reminders',
                    'Recovery Support',
                ],
                'benefits' => [
                    'Many candidates hold SMK Kesehatan or D3 Nursing backgrounds',
                    'Screened for composure, empathy, and physical readiness',
                    'Clear placement terms and responsive support desk',
                ],
            ],
            [
                'slug' => 'elderly-care',
                'category_key' => 'Elderly Care',
                'title' => 'Elderly Care',
                'subtitle' => 'Compassionate Senior Companionship',
                'icon' => 'heart-handshake',
                'image' => 'lp-assets/images/services/elderly-care.jpg',
                'short_description' => 'Respectful senior companions dedicated to comfort, nutrition support, fall prevention, and warm daily engagement.',
                'description' => 'Designed specifically for aging parents and grandparents, our Elderly Care specialists provide respectful companionship, balanced senior meal prep, and gentle daily living assistance.',
                'responsibilities' => [
                    'Providing warm conversation and attentive daily companionship',
                    'Preparing low-sodium, diabetic-friendly, or soft senior meals',
                    'Assisting with safe walking, seating, and fall prevention',
                    'Keeping bedrooms fresh, comfortable, and clutter-free',
                ],
                'skills' => [
                    'Senior Companionship',
                    'Special Diet Prep',
                    'Fall Prevention',
                    'Dementia Patience',
                ],
                'benefits' => [
                    'Calm, mature candidates matched for senior comfort',
                    'Verified reference checks and medical screening',
                    'Regular follow-up check-ins from our care team',
                ],
            ],
            [
                'slug' => 'other-manpower',
                'category_key' => 'Other',
                'title' => 'Other Manpower Services',
                'subtitle' => 'Private Drivers & Family Cooks',
                'icon' => 'briefcase',
                'image' => 'lp-assets/images/services/other-manpower.jpg',
                'short_description' => 'Vetted private household chauffeurs, family cooks, and specialized residential support staff.',
                'description' => 'For households requiring broader domestic support, we recruit dependable private drivers, culinary household staff, and estate caretakers selected for discretion and reliability.',
                'responsibilities' => [
                    'Safe, punctual family commutes, school runs, and errands',
                    'Maintaining clean, serviced family vehicles and route planning',
                    'Planning weekly family menus and maintaining food hygiene',
                    'Upholding strict household privacy and professional etiquette',
                ],
                'skills' => [
                    'Defensive Driving (SIM A)',
                    'Culinary Planning',
                    'Route Familiarity',
                    'Discreet Etiquette',
                ],
                'benefits' => [
                    'Verified license and employment history checks',
                    'Clear job scope matching before deployment',
                    'Flexible live-in or daily commute arrangements',
                ],
            ],
        ];
    }

    /**
     * Get competitive advantages for Why Choose Us sections.
     *
     * @return array<int, array<string, string>>
     */
    public function getWhyChooseUs(): array
    {
        if (Schema::hasTable('why_choose_us')) {
            $dbItems = WhyChooseUs::query()
                ->active()
                ->orderBy('order')
                ->orderBy('id')
                ->get();

            if ($dbItems->isNotEmpty()) {
                return $dbItems->map(fn (WhyChooseUs $item): array => [
                    'title' => $item->title,
                    'description' => (string) ($item->description ?? ''),
                    'icon' => $item->icon,
                ])->all();
            }
        }

        return [
            [
                'title' => 'Carefully Selected Candidates',
                'description' => 'Applicants undergo identity validation, family reference confirmation, and behavioral interviews before profile listing.',
                'icon' => 'shield-check',
            ],
            [
                'title' => 'Professional Recruitment Process',
                'description' => 'Structured matching ensures families interview candidates aligned with their exact household needs and schedule.',
                'icon' => 'clipboard-check',
            ],
            [
                'title' => 'Qualified and Trained Workers',
                'description' => 'Candidates receive practical orientation in residential hygiene, childcare safety, eldercare etiquette, and home appliances.',
                'icon' => 'graduation-cap',
            ],
            [
                'title' => 'Transparent Communication',
                'description' => 'Clear candidate backgrounds, explicit preference summaries, and straightforward service terms from day one.',
                'icon' => 'file-check',
            ],
            [
                'title' => 'Responsive Customer Support',
                'description' => 'Dedicated placement consultants ready via WhatsApp and office appointments to guide selection and onboarding.',
                'icon' => 'headphones',
            ],
            [
                'title' => 'Placement Assistance',
                'description' => 'Post-placement check-ins and practical mediation help both families and workers adapt smoothly together.',
                'icon' => 'handshake',
            ],
        ];
    }

    /**
     * Get the four-step recruitment timeline.
     *
     * @return array<int, array<string, string>>
     */
    public function getRecruitmentSteps(): array
    {
        if (Schema::hasTable('recruitment_steps')) {
            $dbSteps = RecruitmentStep::query()
                ->active()
                ->orderBy('order')
                ->orderBy('id')
                ->get();

            if ($dbSteps->isNotEmpty()) {
                return $dbSteps->map(fn (RecruitmentStep $step): array => [
                    'step' => $step->step,
                    'title' => $step->title,
                    'description' => (string) ($step->description ?? ''),
                    'icon' => $step->icon,
                ])->all();
            }
        }

        return [
            [
                'step' => '01',
                'title' => 'Consultation',
                'description' => "Understand the client's needs, household dynamics, preferred skills, and placement schedule.",
                'icon' => 'message-square',
            ],
            [
                'step' => '02',
                'title' => 'Candidate Selection',
                'description' => 'Present suitable worker candidates and coordinate video or face-to-face interviews.',
                'icon' => 'user-check',
            ],
            [
                'step' => '03',
                'title' => 'Placement',
                'description' => 'Coordinate transparent placement documentation and structured candidate onboarding.',
                'icon' => 'briefcase',
            ],
            [
                'step' => '04',
                'title' => 'Assistance',
                'description' => 'Provide follow-up support after placement to foster long-term household harmony.',
                'icon' => 'life-buoy',
            ],
        ];
    }

    /**
     * Get sample client testimonials for demonstration.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getTestimonials(): array
    {
        if (Schema::hasTable('recruitment_testimonials')) {
            $dbTestimonials = RecruitmentTestimonial::query()
                ->active()
                ->orderBy('order')
                ->orderBy('id')
                ->get();

            if ($dbTestimonials->isNotEmpty()) {
                return $dbTestimonials->map(fn (RecruitmentTestimonial $item): array => [
                    'name' => $item->name,
                    'location' => (string) ($item->location ?? ''),
                    'service' => (string) ($item->service ?? ''),
                    'quote' => $item->quote,
                    'avatar' => (string) ($item->avatar ?? ''),
                    'avatar_url' => $item->avatar_url,
                    'rating' => $item->rating,
                ])->all();
            }
        }

        return [
            [
                'name' => 'Mrs. Ratna Wijaya',
                'location' => 'South Jakarta',
                'service' => 'Babysitter Placement',
                'quote' => 'The recruitment team listened carefully to our newborn routine needs. Our babysitter Nadia is attentive, hygienic, and communicates daily schedules clearly.',
                'avatar' => 'lp-assets/images/workers/worker-2.jpg',
                'rating' => 5,
            ],
            [
                'name' => 'Mr. Hendra Kusuma',
                'location' => 'Bekasi, West Java',
                'service' => 'Elderly Care Placement',
                'quote' => 'Finding a respectful caregiver for my father after surgery felt daunting until we consulted PT Permata Kasih Anugerah. Their candidate screening made us confident.',
                'avatar' => 'lp-assets/images/workers/worker-3.jpg',
                'rating' => 5,
            ],
            [
                'name' => 'Mrs. Clarissa Tan',
                'location' => 'BSD City, Tangerang',
                'service' => 'Domestic Helper (ART)',
                'quote' => 'Clear communication from initial WhatsApp inquiry through onboarding. Our helper Siti maintains the home thoughtfully and adapts smoothly to our family.',
                'avatar' => 'lp-assets/images/workers/worker-1.jpg',
                'rating' => 5,
            ],
        ];
    }

    /**
     * Get interactive FAQ items matching project requirements.
     *
     * @return array<int, array<string, string>>
     */
    public function getFaqs(): array
    {
        if (Schema::hasTable('lp_faqs')) {
            $dbFaqs = LpFaq::query()
                ->active()
                ->orderBy('order')
                ->orderBy('id')
                ->get();

            if ($dbFaqs->isNotEmpty()) {
                return $dbFaqs->map(fn (LpFaq $faq): array => [
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                ])->all();
            }
        }

        return [
            [
                'question' => 'How can I find a suitable worker?',
                'answer' => 'You can browse our Worker Catalog using filters for category, age range, experience, origin, and availability—or message our recruitment consultants via WhatsApp for curated candidate recommendations.',
            ],
            [
                'question' => 'What worker categories are available?',
                'answer' => 'We provide Domestic Helpers (ART), Infant Babysitters, Toddler Nannies, Caregivers, Elderly Care specialists, as well as Other Manpower Services including Private Drivers and Family Cooks.',
            ],
            [
                'question' => 'How does the recruitment process work?',
                'answer' => 'Our process follows four clear stages: initial Consultation to understand your needs, Candidate Selection with video or office interviews, Placement coordination, and follow-up Assistance after arrival.',
            ],
            [
                'question' => 'Can I choose a specific candidate?',
                'answer' => 'Yes. Each worker profile displays a unique candidate code. Click "View Details" to review their background and skills, then use the WhatsApp button to inquire specifically about that candidate.',
            ],
            [
                'question' => 'How can I contact the recruitment team?',
                'answer' => 'You can reach our consultants immediately via the WhatsApp buttons across the site, call our office telephone line, email us, or visit our Jatiasih, Bekasi office during business hours.',
            ],
            [
                'question' => 'What information is available in worker profiles?',
                'answer' => 'Public worker profiles present recruitment-relevant details such as candidate code, age, regional origin, education, work experience, skills, languages, and placement preferences while keeping sensitive personal documents strictly private.',
            ],
        ];
    }

    /**
     * Get all structured sample worker profiles, optionally filtered and sorted.
     *
     * @param  array<string, mixed>  $filters
     * @return Collection<int, array<string, mixed>>
     */
    public function getWorkers(array $filters = []): Collection
    {
        $workers = collect($this->allWorkersData());

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $needle = Str::lower($search);
            $workers = $workers->filter(function (array $worker) use ($needle): bool {
                $searchableText = Str::lower(implode(' ', [
                    $worker['name'],
                    $worker['code'],
                    $worker['origin'],
                    $worker['origin_region'],
                    $worker['category'],
                    $worker['category_label'],
                    $worker['bio'],
                    implode(' ', $worker['skills']),
                ]));

                return str_contains($searchableText, $needle);
            });
        }

        $category = trim((string) ($filters['category'] ?? ''));
        if ($category !== '' && $category !== 'all') {
            $workers = $workers->where('category', $category);
        }

        $experience = trim((string) ($filters['experience'] ?? ''));
        if ($experience !== '' && $experience !== 'all') {
            $workers = $workers->where('experience_bracket', $experience);
        }

        $ageRange = trim((string) ($filters['age_range'] ?? ''));
        if ($ageRange !== '' && $ageRange !== 'all') {
            $workers = $workers->where('age_bracket', $ageRange);
        }

        $origin = trim((string) ($filters['origin'] ?? ''));
        if ($origin !== '' && $origin !== 'all') {
            $workers = $workers->where('origin_region', $origin);
        }

        $status = trim((string) ($filters['status'] ?? ''));
        if ($status !== '' && $status !== 'all') {
            $workers = $workers->where('status', $status);
        }

        $sort = (string) ($filters['sort'] ?? 'newest');

        return match ($sort) {
            'experience_desc' => $workers->sortByDesc('experience_years')->values(),
            'name_asc' => $workers->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values(),
            default => $workers->sortByDesc('created_at')->values(),
        };
    }

    /**
     * Get featured workers for the homepage.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getFeaturedWorkers(int $limit = 4): Collection
    {
        return $this->getWorkers()
            ->where('is_featured', true)
            ->take($limit)
            ->values();
    }

    /**
     * Find a single worker profile by its URL slug.
     *
     * @return array<string, mixed>|null
     */
    public function findWorkerBySlug(string $slug): ?array
    {
        return $this->getWorkers()->firstWhere('slug', $slug);
    }

    /**
     * Get related candidates for a worker detail page.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getRelatedWorkers(string $currentSlug, string $category, int $limit = 3): Collection
    {
        $all = $this->getWorkers()->reject(fn (array $worker): bool => $worker['slug'] === $currentSlug);

        $sameCategory = $all->where('category', $category);
        if ($sameCategory->count() >= $limit) {
            return $sameCategory->take($limit)->values();
        }

        return $sameCategory
            ->merge($all->where('category', '!=', $category))
            ->take($limit)
            ->values();
    }

    /**
     * Get available regional origin options for catalog filtering.
     *
     * @return array<string, string>
     */
    public function getOriginOptions(): array
    {
        return $this->getWorkers()
            ->pluck('origin_region')
            ->unique()
            ->sort()
            ->mapWithKeys(fn (string $region): array => [$region => $region])
            ->all();
    }

    /**
     * Get categorized gallery items across all six required categories.
     *
     * @return array<int, array<string, string>>
     */
    public function getGalleryItems(): array
    {
        if (Schema::hasTable('lp_galleries')) {
            $dbGalleries = LpGallery::query()
                ->active()
                ->orderBy('order')
                ->orderBy('id')
                ->get();

            if ($dbGalleries->isNotEmpty()) {
                return $dbGalleries->map(fn (LpGallery $item): array => [
                    'id' => 'gal-'.$item->id,
                    'title' => $item->title,
                    'category' => $item->category,
                    'date' => (string) ($item->date ?? ''),
                    'description' => (string) ($item->description ?? ''),
                    'image' => (string) ($item->image ?? ''),
                    'image_url' => $item->image_url,
                ])->all();
            }
        }

        return [
            [
                'id' => 'gal-1',
                'title' => 'Structured Childcare & Hygiene Training',
                'category' => 'Training Activities',
                'date' => 'October 2026',
                'description' => 'Hands-on classroom module covering infant safety, clean nursery practices, and residential care standards.',
                'image' => 'lp-assets/images/gallery/gallery-1.jpg',
            ],
            [
                'id' => 'gal-2',
                'title' => 'Client Interview & Candidate Matching',
                'category' => 'Recruitment Activities',
                'date' => 'October 2026',
                'description' => 'Consultants facilitating pre-placement interviews between hiring families and screened candidates.',
                'image' => 'lp-assets/images/gallery/gallery-2.jpg',
            ],
            [
                'id' => 'gal-3',
                'title' => 'Pre-Placement Household Orientation',
                'category' => 'Worker Preparation',
                'date' => 'September 2026',
                'description' => 'Final readiness briefing aligning communication habits, work schedules, and family etiquette prior to deployment.',
                'image' => 'lp-assets/images/gallery/gallery-3.jpg',
            ],
            [
                'id' => 'gal-4',
                'title' => 'Elderly Care & Vital Monitoring Readiness',
                'category' => 'Training Activities',
                'date' => 'September 2026',
                'description' => 'Caregiver and senior companion candidates completing blood pressure observation and mobility assistance training.',
                'image' => 'lp-assets/images/gallery/gallery-4.jpg',
            ],
            [
                'id' => 'gal-5',
                'title' => 'Bekasi Placement Desk Operations',
                'category' => 'Company Activities',
                'date' => 'August 2026',
                'description' => 'Daily candidate coordination and client consultation activities at our Jatiasih headquarters.',
                'image' => 'lp-assets/images/gallery/gallery-5.jpg',
            ],
            [
                'id' => 'gal-6',
                'title' => 'Early Childhood Engagement Workshop',
                'category' => 'Worker Preparation',
                'date' => 'August 2026',
                'description' => 'Nanny candidates practicing toddler communication, nutritious snack preparation, and structured routines.',
                'image' => 'lp-assets/images/gallery/gallery-6.jpg',
            ],
            [
                'id' => 'gal-7',
                'title' => 'Annual Hospitality & Ethics Refresher',
                'category' => 'Events',
                'date' => 'July 2026',
                'description' => 'Gathering focused on professional ethics, culinary hygiene, and service pride among active personnel.',
                'image' => 'lp-assets/images/gallery/gallery-7.jpg',
            ],
            [
                'id' => 'gal-8',
                'title' => 'Candidate Verification & Clearance Desk',
                'category' => 'Documentation',
                'date' => 'July 2026',
                'description' => 'Administrative screening, family reference validation, and placement contract documentation.',
                'image' => 'lp-assets/images/gallery/gallery-8.jpg',
            ],
        ];
    }

    /**
     * Build a click-to-chat WhatsApp URL with URL-encoded text.
     */
    public function buildWhatsAppUrl(?string $customMessage = null): string
    {
        $number = preg_replace('/[^0-9]/', '', (string) config('company.whatsapp_number', '6282299109766'));
        $message = $customMessage ?: (string) config('company.default_whatsapp_message');

        return 'https://wa.me/'.$number.'?text='.rawurlencode($message);
    }

    /**
     * Build a candidate-specific WhatsApp consultation URL including name, code, category, and page URL.
     *
     * @param  array<string, mixed>  $worker
     */
    public function buildCandidateWhatsAppUrl(array $worker, ?string $pageUrl = null): string
    {
        $resolvedUrl = $pageUrl ?: route('worker.detail', $worker['slug']);

        $message = sprintf(
            'Hello, I am interested in candidate %s (%s) - %s. Profile URL: %s. I would like to know more about this candidate.',
            $worker['name'],
            $worker['code'],
            $worker['category_label'],
            $resolvedUrl
        );

        return $this->buildWhatsAppUrl($message);
    }

    /**
     * Structured candidate profiles backed by Eloquent with static fallback.
     *
     * @return array<int, array<string, mixed>>
     */
    private function allWorkersData(): array
    {
        if (Schema::hasTable('workers')) {
            $dbWorkers = Worker::query()->orderByDesc('published_date')->latest('id')->get();

            if ($dbWorkers->isNotEmpty()) {
                return $dbWorkers->map(fn (Worker $worker): array => [
                    'id' => $worker->id,
                    'slug' => $worker->slug,
                    'code' => $worker->code,
                    'name' => $worker->name,
                    'category' => $worker->category,
                    'category_label' => $worker->category_label,
                    'age' => $worker->age,
                    'age_bracket' => $worker->age_bracket,
                    'origin' => (string) ($worker->origin ?? ''),
                    'origin_region' => (string) ($worker->origin_region ?? ''),
                    'education' => (string) ($worker->education ?? ''),
                    'experience_years' => $worker->experience_years,
                    'experience_label' => (string) ($worker->experience_label ?? ''),
                    'experience_bracket' => $worker->experience_bracket,
                    'status' => $worker->status,
                    'is_featured' => $worker->is_featured,
                    'created_at' => ($worker->published_date ?? $worker->created_at)?->format('Y-m-d') ?? now()->toDateString(),
                    'photo' => (string) ($worker->photo ?? ''),
                    'photo_url' => $worker->photo_url,
                    'gallery' => $worker->gallery ?? ($worker->photo ? [$worker->photo] : []),
                    'gallery_urls' => $worker->gallery_urls,
                    'languages' => $worker->languages ?? [],
                    'bio' => (string) ($worker->bio ?? ''),
                    'skills' => $worker->skills ?? [],
                    'work_history' => $worker->work_history ?? [],
                    'placement_preferences' => $worker->placement_preferences ?? [],
                    'training_certifications' => $worker->training_certifications ?? [],
                    'medical_check' => (string) ($worker->medical_check ?: 'General health & physical readiness screening completed'),
                ])->all();
            }
        }

        return $this->defaultWorkersData();
    }

    /**
     * Default sample candidate profiles for initial seeding & fallback.
     *
     * @return array<int, array<string, mixed>>
     */
    public function defaultWorkersData(): array
    {
        return [
            [
                'id' => 1,
                'slug' => 'siti-rahmawati',
                'code' => 'PKA-2026-001',
                'name' => 'Siti Rahmawati',
                'category' => 'ART',
                'category_label' => 'Domestic Helper (ART)',
                'age' => 28,
                'age_bracket' => '26-35',
                'origin' => 'Banyumas, Central Java',
                'origin_region' => 'Central Java',
                'education' => 'Senior High School (SMA)',
                'experience_years' => 4.0,
                'experience_label' => '4 Years',
                'experience_bracket' => '3-5',
                'status' => 'Available',
                'is_featured' => true,
                'created_at' => '2026-11-15',
                'photo' => 'lp-assets/images/workers/worker-1.jpg',
                'gallery' => [
                    'lp-assets/images/workers/worker-1.jpg',
                    'lp-assets/images/general/about-training.jpg',
                    'lp-assets/images/general/hero-main.jpg',
                ],
                'languages' => ['Indonesian (Native)', 'Javanese'],
                'bio' => 'Organized and dependable domestic helper with four years of residential experience in Jakarta and Bekasi. Confident in daily home sanitization, laundry care, and everyday Indonesian cooking.',
                'skills' => [
                    'Deep Cleaning & Organization',
                    'Daily Indonesian Cooking',
                    'Laundry & Steam Ironing',
                    'Modern Kitchen Appliances',
                    'Pet-Friendly (Cats)',
                ],
                'work_history' => [
                    '2023 – 2026: Live-in Domestic Helper for a family of 4 in South Jakarta.',
                    '2022 – 2023: Household Assistant handling daily housekeeping and meal prep.',
                ],
                'placement_preferences' => [
                    'Placement Type' => 'Live-in (Stay-in)',
                    'Preferred Location' => 'Jabodetabek & West Java',
                    'Comfortable With' => 'Modern appliances, AC rooms, indoor cats',
                    'Availability' => 'Ready for immediate interview & placement',
                ],
                'training_certifications' => [
                    'PKA Standard Housekeeping & Hygiene Certificate',
                    'Kitchen Safety & Food Handling Orientation',
                ],
                'medical_check' => 'General health & physical readiness screening completed (Oct 2026)',
            ],
            [
                'id' => 2,
                'slug' => 'nadia-putri-lestari',
                'code' => 'PKA-2026-002',
                'name' => 'Nadia Putri Lestari',
                'category' => 'Babysitter',
                'category_label' => 'Babysitter',
                'age' => 24,
                'age_bracket' => '18-25',
                'origin' => 'Sleman, Yogyakarta',
                'origin_region' => 'Yogyakarta',
                'education' => 'Vocational Nursing (SMK Kesehatan)',
                'experience_years' => 2.5,
                'experience_label' => '2.5 Years',
                'experience_bracket' => '1-3',
                'status' => 'Available',
                'is_featured' => true,
                'created_at' => '2026-11-16',
                'photo' => 'lp-assets/images/workers/worker-2.jpg',
                'gallery' => [
                    'lp-assets/images/workers/worker-2.jpg',
                    'lp-assets/images/general/hero-main.jpg',
                    'lp-assets/images/general/about-training.jpg',
                ],
                'languages' => ['Indonesian (Native)', 'Basic English'],
                'bio' => 'Gentle and methodical infant babysitter with a vocational healthcare education. Skilled in newborn hygiene, bottle sterilization, structured feeding logs, and soothing sleep routines.',
                'skills' => [
                    'Newborn & Infant Bathing',
                    'Bottle Sterilization & Formula Prep',
                    'MPASI Preparation',
                    'Infant Sleep & Feeding Tracking',
                    'Pediatric First-Aid Basics',
                ],
                'work_history' => [
                    '2024 – 2026: Infant Babysitter in Central Jakarta (newborn through 20 months).',
                    '2023 – 2024: Junior Babysitter at private family residence in Yogyakarta.',
                ],
                'placement_preferences' => [
                    'Placement Type' => 'Live-in (Stay-in)',
                    'Preferred Location' => 'Jabodetabek / Major Cities',
                    'Comfortable With' => 'Newborns (0–24 months), family travel',
                    'Availability' => 'Ready for immediate placement',
                ],
                'training_certifications' => [
                    'PKA Newborn & Infant Care Competency',
                    'Pediatric Choking & Basic Emergency Training',
                ],
                'medical_check' => 'Health screening & immunization records verified (Nov 2026)',
            ],
            [
                'id' => 3,
                'slug' => 'ratna-dewi-kusuma',
                'code' => 'PKA-2026-003',
                'name' => 'Ratna Dewi Kusuma',
                'category' => 'Caregiver',
                'category_label' => 'Caregiver',
                'age' => 32,
                'age_bracket' => '26-35',
                'origin' => 'Malang, East Java',
                'origin_region' => 'East Java',
                'education' => 'Nursing Diploma (D3 Keperawatan)',
                'experience_years' => 6.0,
                'experience_label' => '6 Years',
                'experience_bracket' => '5+',
                'status' => 'Available',
                'is_featured' => true,
                'created_at' => '2026-11-17',
                'photo' => 'lp-assets/images/workers/worker-3.jpg',
                'gallery' => [
                    'lp-assets/images/workers/worker-3.jpg',
                    'lp-assets/images/general/about-training.jpg',
                    'lp-assets/images/general/hero-main.jpg',
                ],
                'languages' => ['Indonesian (Native)', 'Conversational English'],
                'bio' => 'Compassionate D3-educated caregiver specializing in post-hospital recovery assistance, vital sign monitoring, safe wheelchair transfers, and coordinated medication reminders.',
                'skills' => [
                    'Vital Sign Monitoring (BP & Pulse)',
                    'Medication Reminder & Logging',
                    'Safe Mobility & Wheelchair Transfer',
                    'Post-Hospital Recovery Support',
                    'Balanced Diet & Hydration Care',
                ],
                'work_history' => [
                    '2022 – 2026: Live-in Caregiver for recovering senior couple in North Jakarta.',
                    '2020 – 2022: Home Care Assistant for post-orthopedic patient in Surabaya.',
                ],
                'placement_preferences' => [
                    'Placement Type' => 'Live-in or Dedicated Shift',
                    'Preferred Location' => 'Jabodetabek & Major Cities',
                    'Comfortable With' => 'Mobility assistance, wellness monitoring',
                    'Availability' => 'Immediate placement ready',
                ],
                'training_certifications' => [
                    'Diploma III Nursing Qualification',
                    'PKA Care & Fall Prevention Workshop',
                ],
                'medical_check' => 'Comprehensive medical & fitness screening cleared (Nov 2026)',
            ],
            [
                'id' => 4,
                'slug' => 'nurhayati-wulandari',
                'code' => 'PKA-2026-004',
                'name' => 'Nurhayati Wulandari',
                'category' => 'ART',
                'category_label' => 'Domestic Helper (ART)',
                'age' => 38,
                'age_bracket' => '36-45',
                'origin' => 'Garut, West Java',
                'origin_region' => 'West Java',
                'education' => 'Junior High School (SMP)',
                'experience_years' => 7.0,
                'experience_label' => '7 Years',
                'experience_bracket' => '5+',
                'status' => 'Available',
                'is_featured' => false,
                'created_at' => '2026-11-12',
                'photo' => 'lp-assets/images/workers/worker-4.jpg',
                'gallery' => [
                    'lp-assets/images/workers/worker-4.jpg',
                    'lp-assets/images/general/about-training.jpg',
                    'lp-assets/images/general/hero-main.jpg',
                ],
                'languages' => ['Indonesian (Native)', 'Sundanese'],
                'bio' => 'Seasoned household assistant with seven years of loyal service in large multi-story family residences. Known for calm work ethics, reliable home cooking, and careful wardrobe care.',
                'skills' => [
                    'Multi-Story Residence Upkeep',
                    'Traditional & Modern Indonesian Cuisine',
                    'Fine Linen & Wardrobe Care',
                    'Grocery & Pantry Management',
                ],
                'work_history' => [
                    '2021 – 2026: Senior Domestic Helper for a family of 5 in North Jakarta.',
                    '2019 – 2021: Household Cook & Housekeeper in South Tangerang.',
                ],
                'placement_preferences' => [
                    'Placement Type' => 'Live-in (Stay-in)',
                    'Preferred Location' => 'Jakarta, Bekasi, Tangerang',
                    'Comfortable With' => 'Large homes, daily family cooking',
                    'Availability' => 'Available immediately',
                ],
                'training_certifications' => [
                    'PKA Senior Domestic Hospitality Refresher',
                    'Food Hygiene & Kitchen Safety',
                ],
                'medical_check' => 'Routine health examination cleared (Oct 2026)',
            ],
            [
                'id' => 5,
                'slug' => 'intan-permatasari',
                'code' => 'PKA-2026-005',
                'name' => 'Intan Permatasari',
                'category' => 'Nanny',
                'category_label' => 'Nanny',
                'age' => 27,
                'age_bracket' => '26-35',
                'origin' => 'Semarang, Central Java',
                'origin_region' => 'Central Java',
                'education' => 'Diploma in Early Childhood Education (D3 PAUD)',
                'experience_years' => 3.5,
                'experience_label' => '3.5 Years',
                'experience_bracket' => '3-5',
                'status' => 'Available',
                'is_featured' => true,
                'created_at' => '2026-11-14',
                'photo' => 'lp-assets/images/workers/worker-5.jpg',
                'gallery' => [
                    'lp-assets/images/workers/worker-5.jpg',
                    'lp-assets/images/general/hero-main.jpg',
                    'lp-assets/images/general/about-training.jpg',
                ],
                'languages' => ['Indonesian (Native)', 'Conversational English'],
                'bio' => 'Creative and structured nanny holding a D3 PAUD diploma. Specializes in toddler routine building, bilingual storybook reading, school readiness, and screen-free play activities.',
                'skills' => [
                    'Toddler Routine & Toilet Training',
                    'Educational & Sensory Play',
                    'English Storytelling Support',
                    'Healthy Snack & Lunchbox Prep',
                    'Positive Child Guidance',
                ],
                'work_history' => [
                    '2024 – 2026: Live-in Nanny for twin 3-year-olds in South Jakarta.',
                    '2022 – 2024: Preschool Assistant & Private Nanny in Semarang.',
                ],
                'placement_preferences' => [
                    'Placement Type' => 'Live-in (Stay-in)',
                    'Preferred Location' => 'Jabodetabek',
                    'Comfortable With' => 'Toddlers (2–7 years), bilingual households',
                    'Availability' => 'Available for immediate interview',
                ],
                'training_certifications' => [
                    'D3 Early Childhood Education (PAUD)',
                    'PKA Child Safety & Play Module',
                ],
                'medical_check' => 'General medical & wellness screening cleared (Nov 2026)',
            ],
            [
                'id' => 6,
                'slug' => 'hendra-wijaya',
                'code' => 'PKA-2026-006',
                'name' => 'Hendra Wijaya',
                'category' => 'Other',
                'category_label' => 'Other Manpower Services',
                'age' => 34,
                'age_bracket' => '26-35',
                'origin' => 'Bogor, West Java',
                'origin_region' => 'West Java',
                'education' => 'Vocational High School (SMK)',
                'experience_years' => 6.0,
                'experience_label' => '6 Years',
                'experience_bracket' => '5+',
                'status' => 'Available',
                'is_featured' => false,
                'created_at' => '2026-11-10',
                'photo' => 'lp-assets/images/workers/worker-6.jpg',
                'gallery' => [
                    'lp-assets/images/workers/worker-6.jpg',
                    'lp-assets/images/general/hero-main.jpg',
                    'lp-assets/images/general/about-training.jpg',
                ],
                'languages' => ['Indonesian (Native)', 'Basic English'],
                'bio' => 'Punctual, courteous private family chauffeur with six years of experience driving executive sedans and family MPVs across Jabodetabek. Familiar with school commutes, airport runs, and vehicle upkeep.',
                'skills' => [
                    'Defensive Driving (Auto & Manual)',
                    'Jabodetabek & Bandung Routes',
                    'Active SIM A License',
                    'Vehicle Cleanliness & Maintenance',
                    'Discreet Etiquette',
                ],
                'work_history' => [
                    '2022 – 2026: Private Family Driver in BSD City, Tangerang.',
                    '2020 – 2022: Corporate Executive Driver in Central Jakarta.',
                ],
                'placement_preferences' => [
                    'Placement Type' => 'Daily Commute or Live-in',
                    'Preferred Location' => 'Jakarta, Tangerang, Bekasi, Bogor',
                    'Comfortable With' => 'School runs, executive schedules, weekend family trips',
                    'Availability' => 'Ready immediately',
                ],
                'training_certifications' => [
                    'Defensive Driving Assessment',
                    'PKA Service Etiquette & Confidentiality Briefing',
                ],
                'medical_check' => 'Vision, blood pressure & fitness screening cleared (Oct 2026)',
            ],
            [
                'id' => 7,
                'slug' => 'amelia-sari-hartono',
                'code' => 'PKA-2026-007',
                'name' => 'Amelia Sari Hartono',
                'category' => 'Babysitter',
                'category_label' => 'Babysitter',
                'age' => 22,
                'age_bracket' => '18-25',
                'origin' => 'Metro, Lampung',
                'origin_region' => 'Lampung',
                'education' => 'Senior High School (SMA)',
                'experience_years' => 0.7,
                'experience_label' => '8 Months',
                'experience_bracket' => 'entry',
                'status' => 'In Training',
                'is_featured' => false,
                'created_at' => '2026-11-11',
                'photo' => 'lp-assets/images/workers/worker-7.jpg',
                'gallery' => [
                    'lp-assets/images/workers/worker-7.jpg',
                    'lp-assets/images/general/about-training.jpg',
                    'lp-assets/images/general/hero-main.jpg',
                ],
                'languages' => ['Indonesian (Native)'],
                'bio' => 'Patient, quick-learning junior babysitter currently completing intensive infant hygiene and weaning meal preparation training. Ideal for parents who value fresh, coachable support.',
                'skills' => [
                    'Infant Hygiene & Laundry Care',
                    'Bottle Washing & Sterilizing',
                    'Attentive Nap Supervision',
                    'Nursery Organization',
                ],
                'work_history' => [
                    '2026: Practical Infant Care Training at PT Permata Kasih Anugerah Academy.',
                ],
                'placement_preferences' => [
                    'Placement Type' => 'Live-in (Stay-in)',
                    'Preferred Location' => 'Jabodetabek',
                    'Comfortable With' => 'Infants 3–24 months under parental guidance',
                    'Availability' => 'Completing final training module; pre-booking open',
                ],
                'training_certifications' => [
                    'PKA Foundational Babysitter Curriculum (In Progress)',
                ],
                'medical_check' => 'Initial medical readiness screening cleared (Nov 2026)',
            ],
            [
                'id' => 8,
                'slug' => 'kartini-anggraini',
                'code' => 'PKA-2026-008',
                'name' => 'Kartini Anggraini',
                'category' => 'Elderly Care',
                'category_label' => 'Elderly Care',
                'age' => 39,
                'age_bracket' => '36-45',
                'origin' => 'Solo, Central Java',
                'origin_region' => 'Central Java',
                'education' => 'Vocational Health School (SMK Kesehatan)',
                'experience_years' => 8.0,
                'experience_label' => '8 Years',
                'experience_bracket' => '5+',
                'status' => 'Available',
                'is_featured' => false,
                'created_at' => '2026-11-09',
                'photo' => 'lp-assets/images/workers/worker-8.jpg',
                'gallery' => [
                    'lp-assets/images/workers/worker-8.jpg',
                    'lp-assets/images/general/about-training.jpg',
                    'lp-assets/images/general/hero-main.jpg',
                ],
                'languages' => ['Indonesian (Native)', 'Javanese'],
                'bio' => 'Warm-hearted, mature elderly care companion with eight years of experience supporting aging parents with mild dementia, diabetes meal planning, and calm daily routines.',
                'skills' => [
                    'Dementia & Memory Care Patience',
                    'Senior Diet & Hydration Prep',
                    'Bedbound Hygiene & Comfort Care',
                    'Blood Sugar & Blood Pressure Logging',
                ],
                'work_history' => [
                    '2021 – 2026: Full-time Live-in Elderly Companion in North Jakarta.',
                    '2018 – 2021: Senior Care Specialist in Solo and Yogyakarta.',
                ],
                'placement_preferences' => [
                    'Placement Type' => 'Live-in (Stay-in)',
                    'Preferred Location' => 'Jakarta, Bekasi, Depok, Bogor',
                    'Comfortable With' => 'Senior companionship, low-salt/diabetic meal prep',
                    'Availability' => 'Available immediately',
                ],
                'training_certifications' => [
                    'SMK Kesehatan Graduate',
                    'PKA Senior Dementia & Comfort Care Module',
                ],
                'medical_check' => 'Full medical fitness check cleared (Oct 2026)',
            ],
            [
                'id' => 9,
                'slug' => 'melati-puspitasari',
                'code' => 'PKA-2026-009',
                'name' => 'Melati Puspitasari',
                'category' => 'Nanny',
                'category_label' => 'Nanny',
                'age' => 25,
                'age_bracket' => '18-25',
                'origin' => 'Bandung, West Java',
                'origin_region' => 'West Java',
                'education' => 'Senior High School (SMA)',
                'experience_years' => 2.0,
                'experience_label' => '2 Years',
                'experience_bracket' => '1-3',
                'status' => 'Available',
                'is_featured' => false,
                'created_at' => '2026-11-13',
                'photo' => 'lp-assets/images/workers/worker-9.jpg',
                'gallery' => [
                    'lp-assets/images/workers/worker-9.jpg',
                    'lp-assets/images/general/hero-main.jpg',
                    'lp-assets/images/general/about-training.jpg',
                ],
                'languages' => ['Indonesian (Native)', 'Sundanese', 'Basic English'],
                'bio' => 'Cheerful, energetic nanny experienced with active toddlers and kindergarten children. Skilled in healthy snack preparation, bedtime routines, and keeping playrooms tidy.',
                'skills' => [
                    'Toddler Meal & Snack Prep',
                    'Playroom & Toy Sanitization',
                    'Homework & Drawing Companion',
                    'Outdoor Walk Safety',
                ],
                'work_history' => [
                    '2024 – 2026: Live-in Nanny for a 4-year-old child in Bintaro Jaya.',
                ],
                'placement_preferences' => [
                    'Placement Type' => 'Live-in (Stay-in)',
                    'Preferred Location' => 'Jabodetabek & Bandung',
                    'Comfortable With' => 'Active toddlers, domestic travel with family',
                    'Availability' => 'Available immediately',
                ],
                'training_certifications' => [
                    'PKA Toddler Care & Creative Play Certification',
                ],
                'medical_check' => 'Routine health screening cleared (Nov 2026)',
            ],
            [
                'id' => 10,
                'slug' => 'yuliana-safitri',
                'code' => 'PKA-2026-010',
                'name' => 'Yuliana Safitri',
                'category' => 'ART',
                'category_label' => 'Domestic Helper (ART)',
                'age' => 23,
                'age_bracket' => '18-25',
                'origin' => 'Brebes, Central Java',
                'origin_region' => 'Central Java',
                'education' => 'Senior High School (SMA)',
                'experience_years' => 1.5,
                'experience_label' => '1.5 Years',
                'experience_bracket' => '1-3',
                'status' => 'In Training',
                'is_featured' => false,
                'created_at' => '2026-11-08',
                'photo' => 'lp-assets/images/workers/worker-10.jpg',
                'gallery' => [
                    'lp-assets/images/workers/worker-10.jpg',
                    'lp-assets/images/general/about-training.jpg',
                    'lp-assets/images/general/hero-main.jpg',
                ],
                'languages' => ['Indonesian (Native)', 'Javanese'],
                'bio' => 'Polite, quick-learning domestic helper currently completing modern apartment housekeeping and culinary refresher modules. Previous experience assisting an urban couple in Bekasi.',
                'skills' => [
                    'Apartment & Condo Housekeeping',
                    'Laundry & Delicate Fabric Care',
                    'Simple Everyday Cooking',
                    'Checklist Task Execution',
                ],
                'work_history' => [
                    '2024 – 2026: Domestic Helper in Summarecon Bekasi.',
                ],
                'placement_preferences' => [
                    'Placement Type' => 'Live-in (Stay-in)',
                    'Preferred Location' => 'Bekasi, Jakarta, Depok',
                    'Comfortable With' => 'Apartments or landed homes, young families',
                    'Availability' => 'Ready next week upon training completion',
                ],
                'training_certifications' => [
                    'PKA Modern Housekeeping Refresher (In Progress)',
                ],
                'medical_check' => 'Physical & laboratory screening cleared (Nov 2026)',
            ],
            [
                'id' => 11,
                'slug' => 'wahyu-prasetyo',
                'code' => 'PKA-2026-011',
                'name' => 'Wahyu Prasetyo',
                'category' => 'Other',
                'category_label' => 'Other Manpower Services',
                'age' => 31,
                'age_bracket' => '26-35',
                'origin' => 'Surabaya, East Java',
                'origin_region' => 'East Java',
                'education' => 'Culinary Vocational School (SMK Tata Boga)',
                'experience_years' => 4.5,
                'experience_label' => '4.5 Years',
                'experience_bracket' => '3-5',
                'status' => 'Available',
                'is_featured' => false,
                'created_at' => '2026-11-07',
                'photo' => 'lp-assets/images/workers/worker-11.jpg',
                'gallery' => [
                    'lp-assets/images/workers/worker-11.jpg',
                    'lp-assets/images/general/about-training.jpg',
                    'lp-assets/images/general/hero-main.jpg',
                ],
                'languages' => ['Indonesian (Native)', 'Basic English'],
                'bio' => 'Dedicated private family chef trained in culinary hygiene, weekly nutritious menu planning, Indonesian comfort dishes, and simple Asian home favorites.',
                'skills' => [
                    'Weekly Menu Planning',
                    'Indonesian & Asian Cuisine',
                    'Kitchen Inventory & Shopping',
                    'Food Safety & Allergen Care',
                ],
                'work_history' => [
                    '2022 – 2026: Private Household Cook in South Jakarta.',
                ],
                'placement_preferences' => [
                    'Placement Type' => 'Daily Commute or Live-in',
                    'Preferred Location' => 'Jakarta & South Tangerang',
                    'Comfortable With' => 'Special dietary menus (low sugar, low sodium, halal)',
                    'Availability' => 'Available immediately',
                ],
                'training_certifications' => [
                    'SMK Tata Boga Culinary Diploma',
                    'PKA Kitchen Hygiene Verification',
                ],
                'medical_check' => 'Food-handler health screening cleared (Oct 2026)',
            ],
            [
                'id' => 12,
                'slug' => 'lestari-handayani',
                'code' => 'PKA-2026-012',
                'name' => 'Lestari Handayani',
                'category' => 'Elderly Care',
                'category_label' => 'Elderly Care',
                'age' => 29,
                'age_bracket' => '26-35',
                'origin' => 'Cirebon, West Java',
                'origin_region' => 'West Java',
                'education' => 'Senior High School (SMA)',
                'experience_years' => 3.5,
                'experience_label' => '3.5 Years',
                'experience_bracket' => '3-5',
                'status' => 'Reserved',
                'is_featured' => false,
                'created_at' => '2026-11-05',
                'photo' => 'lp-assets/images/workers/worker-12.jpg',
                'gallery' => [
                    'lp-assets/images/workers/worker-12.jpg',
                    'lp-assets/images/general/about-training.jpg',
                    'lp-assets/images/general/hero-main.jpg',
                ],
                'languages' => ['Indonesian (Native)', 'Javanese'],
                'bio' => 'Patient and respectful senior care assistant experienced in assisting elderly family members with gentle daily walking, meal routines, and companionship.',
                'skills' => [
                    'Senior Daily Companion',
                    'Gentle Mobility Support',
                    'Nutritious Soft Meal Prep',
                    'Calm Communication',
                ],
                'work_history' => [
                    '2023 – 2026: Live-in Elderly Companion in Cibubur, East Jakarta.',
                ],
                'placement_preferences' => [
                    'Placement Type' => 'Live-in (Stay-in)',
                    'Preferred Location' => 'Jabodetabek',
                    'Comfortable With' => 'Independent or mildly assisted seniors',
                    'Availability' => 'In final client interview; similar candidates available',
                ],
                'training_certifications' => [
                    'PKA Elderly Companion Orientation',
                ],
                'medical_check' => 'Health & wellness screening cleared (Oct 2026)',
            ],
        ];
    }
}
