<?php

namespace App\Http\Controllers\Admin\LP;

use App\Exports\RecruitmentServicesExport;
use App\Exports\RecruitmentServicesTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\RecruitmentServicesImport;
use App\Models\LP\RecruitmentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RecruitmentServiceController extends Controller
{
    /**
     * Display a listing of recruitment services.
     */
    public function index(): View
    {
        Gate::authorize('lp-services.view');

        $services = RecruitmentService::query()
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('modules.lp-services.index', compact('services'));
    }

    /**
     * Show the form for creating a new recruitment service.
     */
    public function create(): View
    {
        Gate::authorize('lp-services.create');

        return view('modules.lp-services.create');
    }

    /**
     * Store a newly created recruitment service in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('lp-services.create');

        $validated = $this->validateService($request);

        if ($request->hasFile('image_file')) {
            $validated['image'] = $request->file('image_file')->store('lp-services', 'public');
        }

        RecruitmentService::query()->create($validated);

        return redirect()
            ->route('lp-services.index')
            ->with('success', __('Layanan rekrutmen berhasil ditambahkan.'));
    }

    /**
     * Display the specified recruitment service.
     */
    public function show(RecruitmentService $recruitmentService): View
    {
        Gate::authorize('lp-services.view');

        return view('modules.lp-services.show', compact('recruitmentService'));
    }

    /**
     * Show the form for editing the specified recruitment service.
     */
    public function edit(RecruitmentService $recruitmentService): View
    {
        Gate::authorize('lp-services.edit');

        return view('modules.lp-services.edit', compact('recruitmentService'));
    }

    /**
     * Update the specified recruitment service in storage.
     */
    public function update(Request $request, RecruitmentService $recruitmentService): RedirectResponse
    {
        Gate::authorize('lp-services.edit');

        $validated = $this->validateService($request, $recruitmentService);

        if ($request->hasFile('image_file')) {
            if ($recruitmentService->image && Storage::disk('public')->exists($recruitmentService->image)) {
                Storage::disk('public')->delete($recruitmentService->image);
            }
            $validated['image'] = $request->file('image_file')->store('lp-services', 'public');
        }

        $recruitmentService->update($validated);

        return redirect()
            ->route('lp-services.index')
            ->with('success', __('Layanan rekrutmen berhasil diperbarui.'));
    }

    /**
     * Duplicate the specified recruitment service.
     */
    public function duplicate(RecruitmentService $recruitmentService): RedirectResponse
    {
        Gate::authorize('lp-services.create');

        $copy = $recruitmentService->replicate();
        $copy->title = Str::limit($recruitmentService->title, 143, '').' (Copy)';

        $baseSlug = Str::slug($recruitmentService->slug.'-copy');
        $slug = $baseSlug;
        $counter = 2;
        while (RecruitmentService::query()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $copy->slug = $slug;
        $copy->order = ((int) RecruitmentService::query()->max('order')) + 1;
        $copy->save();

        return redirect()
            ->route('lp-services.index')
            ->with('success', __('Layanan rekrutmen berhasil diduplikasi.'));
    }

    /**
     * Remove the specified recruitment service from storage.
     */
    public function destroy(RecruitmentService $recruitmentService): RedirectResponse
    {
        Gate::authorize('lp-services.delete');

        if ($recruitmentService->image && Storage::disk('public')->exists($recruitmentService->image)) {
            $isImageUsedByOthers = RecruitmentService::query()
                ->where('id', '!=', $recruitmentService->id)
                ->where('image', $recruitmentService->image)
                ->exists();

            if (! $isImageUsedByOthers) {
                Storage::disk('public')->delete($recruitmentService->image);
            }
        }

        $recruitmentService->delete();

        return redirect()
            ->route('lp-services.index')
            ->with('success', __('Layanan rekrutmen berhasil dihapus.'));
    }

    /**
     * Export recruitment services to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('lp-services.view');

        return Excel::download(new RecruitmentServicesExport, 'lp-services-'.now()->format('Y-m-d-His').'.xlsx');
    }

    /**
     * Export recruitment services to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('lp-services.view');

        $services = RecruitmentService::query()->orderBy('order')->orderBy('id')->get();
        $pdf = Pdf::loadView('modules.lp-services.pdf', compact('services'))->setPaper('a4', 'landscape');

        return $pdf->download('lp-services-'.now()->format('Y-m-d-His').'.pdf');
    }

    /**
     * Download sample Excel template for importing recruitment services.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('lp-services.create');

        return Excel::download(new RecruitmentServicesTemplateExport, 'lp-services-template.xlsx');
    }

    /**
     * Import recruitment services from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('lp-services.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        Excel::import(new RecruitmentServicesImport, $request->file('file'));

        return redirect()
            ->route('lp-services.index')
            ->with('success', __('Layanan rekrutmen berhasil diimport.'));
    }

    /**
     * Validate request payload for recruitment services.
     *
     * @return array<string, mixed>
     */
    private function validateService(Request $request, ?RecruitmentService $recruitmentService = null): array
    {
        $validated = $request->validate([
            'slug' => [
                'nullable',
                'string',
                'max:160',
                Rule::unique('recruitment_services', 'slug')->ignore($recruitmentService?->id),
            ],
            'category_key' => ['required', 'string', 'max:60'],
            'title' => ['required', 'string', 'max:150'],
            'subtitle' => ['nullable', 'string', 'max:200'],
            'icon' => ['required', 'string', 'max:100'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'max:2048'],
            'short_description' => ['nullable', 'string', 'max:2000'],
            'description' => ['nullable', 'string', 'max:5000'],
            'responsibilities_text' => ['nullable', 'string'],
            'skills_text' => ['nullable', 'string'],
            'benefits_text' => ['nullable', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['slug'] = filled($validated['slug'] ?? null)
            ? Str::slug($validated['slug'])
            : Str::slug($validated['title']);

        $validated['responsibilities'] = $this->linesToArray($request->input('responsibilities_text'));
        $validated['skills'] = $this->linesToArray($request->input('skills_text'));
        $validated['benefits'] = $this->linesToArray($request->input('benefits_text'));
        $validated['order'] = (int) ($validated['order'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active');

        unset($validated['image_file'], $validated['responsibilities_text'], $validated['skills_text'], $validated['benefits_text']);

        return $validated;
    }

    /**
     * Convert newline-separated textarea input into a clean string list.
     *
     * @return array<int, string>
     */
    private function linesToArray(?string $text): array
    {
        if ($text === null || trim($text) === '') {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];

        return array_values(array_filter(array_map('trim', $lines), fn (string $line): bool => $line !== ''));
    }
}
