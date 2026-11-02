<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\SppPayment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Display the application dashboard with statistics and summaries.
     */
    public function index(): View
    {
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
}

