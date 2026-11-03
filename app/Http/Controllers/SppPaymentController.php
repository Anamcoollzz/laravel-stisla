<?php

namespace App\Http\Controllers;

use App\Exports\SppPaymentsExport;
use App\Exports\SppPaymentsTemplateExport;
use App\Http\Requests\StoreSppPaymentRequest;
use App\Http\Requests\UpdateSppPaymentRequest;
use App\Imports\SppPaymentsImport;
use App\Models\SppPayment;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SppPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('spp-payments.view');

        $sppPayments = SppPayment::query()
            ->with('student.schoolClass')
            ->latest()
            ->get();

        return view('modules.spp-payments.index', [
            'sppPayments' => $sppPayments,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('spp-payments.create');

        $students = Student::query()
            ->with('schoolClass')
            ->orderBy('name')
            ->get();

        return view('modules.spp-payments.create', [
            'students' => $students,
            'months' => SppPayment::months(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSppPaymentRequest $request): RedirectResponse
    {
        Gate::authorize('spp-payments.create');

        SppPayment::query()->create(
            $request->safe()->only([
                'student_id',
                'invoice_number',
                'month',
                'year',
                'amount',
                'payment_date',
                'payment_method',
                'status',
                'notes',
            ])
        );

        return redirect()
            ->route('spp-payments.index')
            ->with('success', __('SPP payment created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(SppPayment $sppPayment): View
    {
        Gate::authorize('spp-payments.view');

        $sppPayment->load('student.schoolClass');

        return view('modules.spp-payments.show', [
            'sppPayment' => $sppPayment,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SppPayment $sppPayment): View
    {
        Gate::authorize('spp-payments.edit');

        $students = Student::query()
            ->with('schoolClass')
            ->orderBy('name')
            ->get();

        return view('modules.spp-payments.edit', [
            'sppPayment' => $sppPayment,
            'students' => $students,
            'months' => SppPayment::months(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSppPaymentRequest $request, SppPayment $sppPayment): RedirectResponse
    {
        Gate::authorize('spp-payments.edit');

        $sppPayment->update(
            $request->safe()->only([
                'student_id',
                'invoice_number',
                'month',
                'year',
                'amount',
                'payment_date',
                'payment_method',
                'status',
                'notes',
            ])
        );

        return redirect()
            ->route('spp-payments.index')
            ->with('success', __('SPP payment updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SppPayment $sppPayment): RedirectResponse
    {
        Gate::authorize('spp-payments.delete');

        $sppPayment->delete();

        return redirect()
            ->route('spp-payments.index')
            ->with('success', __('SPP payment deleted successfully.'));
    }

    /**
     * Export SPP payments to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('spp-payments.view');

        return Excel::download(new SppPaymentsExport, 'spp_payments_'.date('Y-m-d_His').'.xlsx');
    }

    /**
     * Download SPP payment import template.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('spp-payments.create');

        return Excel::download(new SppPaymentsTemplateExport, 'spp_payments_import_example.xlsx');
    }

    /**
     * Import SPP payments from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('spp-payments.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            Excel::import(new SppPaymentsImport, $request->file('file'));

            return redirect()
                ->route('spp-payments.index')
                ->with('success', __('Data imported successfully.'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('spp-payments.index')
                ->with('error', __('Import failed: ').$e->getMessage());
        }
    }
}
