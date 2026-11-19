<?php

namespace App\Http\Controllers\Admin\LP;

use App\Exports\LpGalleriesExport;
use App\Exports\LpGalleriesTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\LpGalleriesImport;
use App\Models\LP\LpGallery;
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

class LpGalleryController extends Controller
{
    /**
     * @return array<int, string>
     */
    private function galleryCategories(): array
    {
        return [
            'Training Activities',
            'Recruitment Activities',
            'Worker Preparation',
            'Company Activities',
            'Events',
            'Documentation',
        ];
    }

    /**
     * Display a listing of LP gallery items.
     */
    public function index(): View
    {
        Gate::authorize('lp-galleries.view');

        $galleries = LpGallery::query()
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('modules.lp-galleries.index', compact('galleries'));
    }

    /**
     * Show the form for creating a new LP gallery item.
     */
    public function create(): View
    {
        Gate::authorize('lp-galleries.create');

        $categories = $this->galleryCategories();

        return view('modules.lp-galleries.create', compact('categories'));
    }

    /**
     * Store a newly created LP gallery item in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('lp-galleries.create');

        $validated = $this->validateGallery($request);

        if ($request->hasFile('image_file')) {
            $validated['image'] = $request->file('image_file')->store('lp-galleries', 'public');
        }

        LpGallery::query()->create($validated);

        return redirect()
            ->route('lp-galleries.index')
            ->with('success', __('Item galeri berhasil ditambahkan.'));
    }

    /**
     * Display the specified LP gallery item.
     */
    public function show(LpGallery $lpGallery): View
    {
        Gate::authorize('lp-galleries.view');

        return view('modules.lp-galleries.show', compact('lpGallery'));
    }

    /**
     * Show the form for editing the specified LP gallery item.
     */
    public function edit(LpGallery $lpGallery): View
    {
        Gate::authorize('lp-galleries.edit');

        $categories = $this->galleryCategories();

        return view('modules.lp-galleries.edit', compact('lpGallery', 'categories'));
    }

    /**
     * Update the specified LP gallery item in storage.
     */
    public function update(Request $request, LpGallery $lpGallery): RedirectResponse
    {
        Gate::authorize('lp-galleries.edit');

        $validated = $this->validateGallery($request);

        if ($request->hasFile('image_file')) {
            if ($lpGallery->image && Storage::disk('public')->exists($lpGallery->image)) {
                $isUsedByOthers = LpGallery::query()
                    ->where('id', '!=', $lpGallery->id)
                    ->where('image', $lpGallery->image)
                    ->exists();

                if (! $isUsedByOthers) {
                    Storage::disk('public')->delete($lpGallery->image);
                }
            }
            $validated['image'] = $request->file('image_file')->store('lp-galleries', 'public');
        }

        $lpGallery->update($validated);

        return redirect()
            ->route('lp-galleries.index')
            ->with('success', __('Item galeri berhasil diperbarui.'));
    }

    /**
     * Duplicate the specified LP gallery item.
     */
    public function duplicate(LpGallery $lpGallery): RedirectResponse
    {
        Gate::authorize('lp-galleries.create');

        $copy = $lpGallery->replicate();
        $copy->title = Str::limit($lpGallery->title, 143, '').' (Copy)';
        $copy->order = ((int) LpGallery::query()->max('order')) + 1;
        $copy->save();

        return redirect()
            ->route('lp-galleries.index')
            ->with('success', __('Item galeri berhasil diduplikasi.'));
    }

    /**
     * Remove the specified LP gallery item from storage.
     */
    public function destroy(LpGallery $lpGallery): RedirectResponse
    {
        Gate::authorize('lp-galleries.delete');

        if ($lpGallery->image && Storage::disk('public')->exists($lpGallery->image)) {
            $isUsedByOthers = LpGallery::query()
                ->where('id', '!=', $lpGallery->id)
                ->where('image', $lpGallery->image)
                ->exists();

            if (! $isUsedByOthers) {
                Storage::disk('public')->delete($lpGallery->image);
            }
        }

        $lpGallery->delete();

        return redirect()
            ->route('lp-galleries.index')
            ->with('success', __('Item galeri berhasil dihapus.'));
    }

    /**
     * Export LP gallery items to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('lp-galleries.view');

        return Excel::download(new LpGalleriesExport, 'lp-galleries-'.now()->format('Y-m-d-His').'.xlsx');
    }

    /**
     * Export LP gallery items to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('lp-galleries.view');

        $galleries = LpGallery::query()->orderBy('order')->orderBy('id')->get();
        $pdf = Pdf::loadView('modules.lp-galleries.pdf', compact('galleries'));

        return $pdf->download('lp-galleries-'.now()->format('Y-m-d-His').'.pdf');
    }

    /**
     * Download sample Excel template for importing LP gallery items.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('lp-galleries.create');

        return Excel::download(new LpGalleriesTemplateExport, 'lp-galleries-template.xlsx');
    }

    /**
     * Import LP gallery items from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('lp-galleries.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        Excel::import(new LpGalleriesImport, $request->file('file'));

        return redirect()
            ->route('lp-galleries.index')
            ->with('success', __('Item galeri berhasil diimport.'));
    }

    /**
     * Validate request payload for LP gallery items.
     *
     * @return array<string, mixed>
     */
    private function validateGallery(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:100'],
            'date' => ['nullable', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'max:2048'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['order'] = (int) ($validated['order'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active');

        unset($validated['image_file']);

        return $validated;
    }
}
