<?php

namespace App\Http\Controllers\Admin\LP;

use App\Exports\RecruitmentTestimonialsExport;
use App\Exports\RecruitmentTestimonialsTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\RecruitmentTestimonialsImport;
use App\Models\LP\RecruitmentTestimonial;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RecruitmentTestimonialController extends Controller
{
    /**
     * Display a listing of recruitment testimonials.
     */
    public function index(): View
    {
        Gate::authorize('lp-testimonials.view');

        $testimonials = RecruitmentTestimonial::query()
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('modules.lp-testimonials.index', compact('testimonials'));
    }

    /**
     * Show the form for creating a new recruitment testimonial.
     */
    public function create(): View
    {
        Gate::authorize('lp-testimonials.create');

        return view('modules.lp-testimonials.create');
    }

    /**
     * Store a newly created recruitment testimonial in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('lp-testimonials.create');

        $validated = $this->validateTestimonial($request);

        if ($request->hasFile('avatar_file')) {
            $validated['avatar'] = $request->file('avatar_file')->store('lp-testimonials', 'public');
        }

        RecruitmentTestimonial::query()->create($validated);

        return redirect()
            ->route('lp-testimonials.index')
            ->with('success', __('Testimoni berhasil ditambahkan.'));
    }

    /**
     * Display the specified recruitment testimonial.
     */
    public function show(RecruitmentTestimonial $recruitmentTestimonial): View
    {
        Gate::authorize('lp-testimonials.view');

        return view('modules.lp-testimonials.show', compact('recruitmentTestimonial'));
    }

    /**
     * Show the form for editing the specified recruitment testimonial.
     */
    public function edit(RecruitmentTestimonial $recruitmentTestimonial): View
    {
        Gate::authorize('lp-testimonials.edit');

        return view('modules.lp-testimonials.edit', compact('recruitmentTestimonial'));
    }

    /**
     * Update the specified recruitment testimonial in storage.
     */
    public function update(Request $request, RecruitmentTestimonial $recruitmentTestimonial): RedirectResponse
    {
        Gate::authorize('lp-testimonials.edit');

        $validated = $this->validateTestimonial($request);

        if ($request->hasFile('avatar_file')) {
            if ($recruitmentTestimonial->avatar && Storage::disk('public')->exists($recruitmentTestimonial->avatar)) {
                $isAvatarUsedByOthers = RecruitmentTestimonial::query()
                    ->where('id', '!=', $recruitmentTestimonial->id)
                    ->where('avatar', $recruitmentTestimonial->avatar)
                    ->exists();

                if (! $isAvatarUsedByOthers) {
                    Storage::disk('public')->delete($recruitmentTestimonial->avatar);
                }
            }
            $validated['avatar'] = $request->file('avatar_file')->store('lp-testimonials', 'public');
        }

        $recruitmentTestimonial->update($validated);

        return redirect()
            ->route('lp-testimonials.index')
            ->with('success', __('Testimoni berhasil diperbarui.'));
    }

    /**
     * Duplicate the specified recruitment testimonial.
     */
    public function duplicate(RecruitmentTestimonial $recruitmentTestimonial): RedirectResponse
    {
        Gate::authorize('lp-testimonials.create');

        $copy = $recruitmentTestimonial->replicate();
        $copy->name = Str::limit($recruitmentTestimonial->name, 143, '').' (Copy)';
        $copy->order = ((int) RecruitmentTestimonial::query()->max('order')) + 1;
        $copy->save();

        return redirect()
            ->route('lp-testimonials.index')
            ->with('success', __('Testimoni berhasil diduplikasi.'));
    }

    /**
     * Remove the specified recruitment testimonial from storage.
     */
    public function destroy(RecruitmentTestimonial $recruitmentTestimonial): RedirectResponse
    {
        Gate::authorize('lp-testimonials.delete');

        if ($recruitmentTestimonial->avatar && Storage::disk('public')->exists($recruitmentTestimonial->avatar)) {
            $isAvatarUsedByOthers = RecruitmentTestimonial::query()
                ->where('id', '!=', $recruitmentTestimonial->id)
                ->where('avatar', $recruitmentTestimonial->avatar)
                ->exists();

            if (! $isAvatarUsedByOthers) {
                Storage::disk('public')->delete($recruitmentTestimonial->avatar);
            }
        }

        $recruitmentTestimonial->delete();

        return redirect()
            ->route('lp-testimonials.index')
            ->with('success', __('Testimoni berhasil dihapus.'));
    }

    /**
     * Export recruitment testimonials to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('lp-testimonials.view');

        return Excel::download(new RecruitmentTestimonialsExport, 'lp-testimonials-'.now()->format('Y-m-d-His').'.xlsx');
    }

    /**
     * Export recruitment testimonials to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('lp-testimonials.view');

        $testimonials = RecruitmentTestimonial::query()->orderBy('order')->orderBy('id')->get();
        $pdf = Pdf::loadView('modules.lp-testimonials.pdf', compact('testimonials'));

        return $pdf->download('lp-testimonials-'.now()->format('Y-m-d-His').'.pdf');
    }

    /**
     * Download sample Excel template for importing recruitment testimonials.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('lp-testimonials.create');

        return Excel::download(new RecruitmentTestimonialsTemplateExport, 'lp-testimonials-template.xlsx');
    }

    /**
     * Import recruitment testimonials from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('lp-testimonials.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        Excel::import(new RecruitmentTestimonialsImport, $request->file('file'));

        return redirect()
            ->route('lp-testimonials.index')
            ->with('success', __('Testimoni berhasil diimport.'));
    }

    /**
     * Validate request payload for recruitment testimonials.
     *
     * @return array<string, mixed>
     */
    private function validateTestimonial(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'location' => ['nullable', 'string', 'max:150'],
            'service' => ['nullable', 'string', 'max:150'],
            'quote' => ['required', 'string', 'max:3000'],
            'avatar' => ['nullable', 'string', 'max:255'],
            'avatar_file' => ['nullable', 'image', 'max:2048'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['order'] = (int) ($validated['order'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active');

        unset($validated['avatar_file']);

        return $validated;
    }
}
