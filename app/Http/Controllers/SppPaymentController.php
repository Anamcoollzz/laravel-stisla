<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSppPaymentRequest;
use App\Http\Requests\UpdateSppPaymentRequest;
use App\Models\SppPayment;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SppPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
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
        $sppPayment->delete();

        return redirect()
            ->route('spp-payments.index')
            ->with('success', __('SPP payment deleted successfully.'));
    }
}
