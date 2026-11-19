<?php

namespace App\Http\Controllers;

use App\Models\LP\CompanyMilestone;
use App\Models\LP\CompanyStat;
use App\Models\LP\CompanyValue;
use App\Models\LP\ContactInquiry;
use App\Models\LP\LpFaq;
use App\Models\LP\LpGallery;
use App\Models\LP\RecruitmentService;
use App\Models\LP\RecruitmentStep;
use App\Models\LP\RecruitmentTestimonial;
use App\Models\LP\WhyChooseUs;
use App\Models\LP\Worker;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductReview;
use App\Models\SchoolClass;
use App\Models\SppPayment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    /**
     * Display the application dashboard with statistics and summaries.
     */
    public function index(): View
    {
        Gate::authorize('dashboard.view');

        if (config('app.menu_app') === 'lp-compro-workers') {
            return $this->comproWorkers();
        }

        if (config('app.menu_app') === 'online-shop') {
            return $this->ecommerce();
        }

        $currentYear = (int) now()->format('Y');
        $currentMonth = (int) now()->format('n');

        $counts = [
            'students' => Student::query()->count(),
            'teachers' => Teacher::query()->count(),
            'classes' => SchoolClass::query()->count(),
            'subjects' => Subject::query()->count(),
            'users' => User::query()->count(),
        ];

        $sppStats = [
            'total_paid' => (int) SppPayment::query()->where('status', 'paid')->sum('amount'),
            'total_unpaid' => (int) SppPayment::query()->where('status', 'unpaid')->sum('amount'),
            'paid_count' => SppPayment::query()->where('status', 'paid')->count(),
            'unpaid_count' => SppPayment::query()->where('status', 'unpaid')->count(),
            'current_month_paid' => (int) SppPayment::query()
                ->where('status', 'paid')
                ->where('year', $currentYear)
                ->where('month', $currentMonth)
                ->sum('amount'),
        ];

        // Monthly SPP revenue trend for current year (1 - 12)
        $monthlyRevenueRaw = SppPayment::query()
            ->where('status', 'paid')
            ->where('year', $currentYear)
            ->selectRaw('month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->all();

        $monthlyChartData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyChartData[] = (int) ($monthlyRevenueRaw[$month] ?? 0);
        }

        // Student gender breakdown
        $genderStats = [
            'male' => Student::query()->where('gender', 'male')->count(),
            'female' => Student::query()->where('gender', 'female')->count(),
        ];

        // Recent 5 SPP Payments with student and class relation
        $recentPayments = SppPayment::query()
            ->with(['student.schoolClass'])
            ->latest('payment_date')
            ->latest('id')
            ->take(5)
            ->get();

        // Recent 5 Students
        $recentStudents = Student::query()
            ->with('schoolClass')
            ->latest('id')
            ->take(5)
            ->get();

        return view('dashboard', [
            'counts' => $counts,
            'sppStats' => $sppStats,
            'monthlyChartData' => $monthlyChartData,
            'genderStats' => $genderStats,
            'recentPayments' => $recentPayments,
            'recentStudents' => $recentStudents,
            'currentYear' => $currentYear,
        ]);
    }

    /**
     * Display the LP Compro Workers dashboard.
     */
    public function comproWorkers(): View
    {
        Gate::authorize('dashboard.view');

        $counts = [
            'workers' => Worker::query()->count(),
            'workers_available' => Worker::query()->where('status', 'Available')->count(),
            'workers_featured' => Worker::query()->where('is_featured', true)->count(),
            'services' => RecruitmentService::query()->active()->count(),
            'inquiries' => ContactInquiry::query()->count(),
            'inquiries_unread' => ContactInquiry::query()->unread()->count(),
            'galleries' => LpGallery::query()->active()->count(),
            'testimonials' => RecruitmentTestimonial::query()->active()->count(),
            'stats' => CompanyStat::query()->active()->count(),
            'why_choose_us' => WhyChooseUs::query()->active()->count(),
            'steps' => RecruitmentStep::query()->active()->count(),
            'faqs' => LpFaq::query()->active()->count(),
            'milestones' => CompanyMilestone::query()->active()->count(),
            'values' => CompanyValue::query()->active()->count(),
        ];

        $workersByCategoryRaw = Worker::query()
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->all();

        $categoryLabels = array_keys($workersByCategoryRaw);
        $categoryTotals = array_values(array_map('intval', $workersByCategoryRaw));

        $workersByStatus = [
            'Available' => Worker::query()->where('status', 'Available')->count(),
            'In Training' => Worker::query()->where('status', 'In Training')->count(),
            'Placed' => Worker::query()->where('status', 'Placed')->count(),
        ];

        $recentInquiries = ContactInquiry::query()
            ->latest('id')
            ->take(5)
            ->get();

        $recentWorkers = Worker::query()
            ->latest('id')
            ->take(5)
            ->get();

        return view('dashboards.compro-workers', compact(
            'counts',
            'categoryLabels',
            'categoryTotals',
            'workersByStatus',
            'recentInquiries',
            'recentWorkers'
        ));
    }

    /**
     * Display the ecommerce dashboard with sales, order, and product summaries.
     */
    public function ecommerce(): View
    {
        Gate::authorize('dashboard.view');

        $currentYear = (int) now()->format('Y');
        $currentMonth = (int) now()->format('n');

        $counts = [
            'orders' => Order::query()->count(),
            'products' => Product::query()->active()->count(),
            'categories' => ProductCategory::query()->where('is_active', true)->count(),
            'customers' => User::query()->count(),
            'open_tickets' => Ticket::query()->where('status', '!=', 'closed')->count(),
            'pending_reviews' => ProductReview::query()->where('is_approved', false)->count(),
        ];

        $orderStats = [
            'total_revenue' => (float) Order::query()->where('payment_status', 'paid')->sum('grand_total'),
            'current_month_revenue' => (float) Order::query()
                ->where('payment_status', 'paid')
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->sum('grand_total'),
            'pending_count' => Order::query()->where('status', 'pending')->count(),
            'processing_count' => Order::query()->whereIn('status', ['processing', 'shipped'])->count(),
            'completed_count' => Order::query()->where('status', 'completed')->count(),
            'cancelled_count' => Order::query()->where('status', 'cancelled')->count(),
            'waiting_verification_count' => Order::query()->where('payment_status', 'waiting_verification')->count(),
        ];

        $paidOrdersThisYear = Order::query()
            ->where('payment_status', 'paid')
            ->whereYear('created_at', $currentYear)
            ->get(['grand_total', 'created_at'])
            ->groupBy(fn (Order $order) => (int) $order->created_at->format('n'));

        $monthlyChartData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyChartData[] = (float) ($paidOrdersThisYear->get($month)?->sum('grand_total') ?? 0);
        }

        $recentOrders = Order::query()
            ->with(['user', 'items'])
            ->latest('id')
            ->take(6)
            ->get();

        $topProducts = Product::query()
            ->active()
            ->with('category')
            ->withSum('orderItems as total_sold', 'quantity')
            ->orderByDesc('total_sold')
            ->orderByDesc('is_trending')
            ->take(5)
            ->get();

        $lowStockProducts = Product::query()
            ->active()
            ->where('stock', '<=', 10)
            ->orderBy('stock')
            ->take(5)
            ->get();

        return view('dashboard-ecommerce', [
            'counts' => $counts,
            'orderStats' => $orderStats,
            'monthlyChartData' => $monthlyChartData,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'lowStockProducts' => $lowStockProducts,
            'currentYear' => $currentYear,
        ]);
    }
}
