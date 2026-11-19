<?php

namespace App\Http\Controllers\Admin\LP;

use App\Exports\WorkersExport;
use App\Exports\WorkersTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\WorkersImport;
use App\Models\LP\Worker;
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

class WorkerController extends Controller
{
    /**
     * @return array<string, string>
     */
    private function categoryMap(): array
    {
        return [
            'ART' => 'Domestic Helper (ART)',
            'Babysitter' => 'Babysitter',
            'Nanny' => 'Nanny',
            'Caregiver' => 'Caregiver',
            'Elderly Care' => 'Elderly Care',
            'Other' => 'Other Manpower Services',
        ];
    }

    /**
     * Display a listing of workers.
     */
    public function index(): View
    {
        Gate::authorize('lp-workers.view');

        $workers = Worker::query()
            ->latest('id')
            ->get();

        return view('modules.lp-workers.index', compact('workers'));
    }

    /**
     * Show the form for creating a new worker.
     */
    public function create(): View
    {
        Gate::authorize('lp-workers.create');

        $categories = $this->categoryMap();

        return view('modules.lp-workers.create', compact('categories'));
    }

    /**
     * Store a newly created worker in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('lp-workers.create');

        $validated = $this->validateWorker($request);

        if ($request->hasFile('photo_file')) {
            $validated['photo'] = $request->file('photo_file')->store('lp-workers', 'public');
            if ($validated['gallery'] === []) {
                $validated['gallery'] = [$validated['photo']];
            }
        }

        Worker::query()->create($validated);

        return redirect()
            ->route('lp-workers.index')
            ->with('success', __('Kandidat pekerja berhasil ditambahkan.'));
    }

    /**
     * Display the specified worker.
     */
    public function show(Worker $worker): View
    {
        Gate::authorize('lp-workers.view');

        return view('modules.lp-workers.show', compact('worker'));
    }

    /**
     * Show the form for editing the specified worker.
     */
    public function edit(Worker $worker): View
    {
        Gate::authorize('lp-workers.edit');

        $categories = $this->categoryMap();

        return view('modules.lp-workers.edit', compact('worker', 'categories'));
    }

    /**
     * Update the specified worker in storage.
     */
    public function update(Request $request, Worker $worker): RedirectResponse
    {
        Gate::authorize('lp-workers.edit');

        $validated = $this->validateWorker($request, $worker);

        if ($request->hasFile('photo_file')) {
            if ($worker->photo && Storage::disk('public')->exists($worker->photo)) {
                $isUsedByOthers = Worker::query()
                    ->where('id', '!=', $worker->id)
                    ->where('photo', $worker->photo)
                    ->exists();

                if (! $isUsedByOthers) {
                    Storage::disk('public')->delete($worker->photo);
                }
            }
            $validated['photo'] = $request->file('photo_file')->store('lp-workers', 'public');
            if ($validated['gallery'] === []) {
                $validated['gallery'] = [$validated['photo']];
            }
        }

        $worker->update($validated);

        return redirect()
            ->route('lp-workers.index')
            ->with('success', __('Kandidat pekerja berhasil diperbarui.'));
    }

    /**
     * Duplicate the specified worker.
     */
    public function duplicate(Worker $worker): RedirectResponse
    {
        Gate::authorize('lp-workers.create');

        $copy = $worker->replicate();
        $copy->name = Str::limit($worker->name, 143, '').' (Copy)';

        $baseCode = Str::limit($worker->code, 42, '').'-COPY';
        $code = $baseCode;
        $counter = 2;
        while (Worker::query()->where('code', $code)->exists()) {
            $code = "{$baseCode}-{$counter}";
            $counter++;
        }
        $copy->code = $code;

        $baseSlug = Str::slug($worker->slug.'-copy');
        $slug = $baseSlug;
        $slugCounter = 2;
        while (Worker::query()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$slugCounter}";
            $slugCounter++;
        }
        $copy->slug = $slug;

        $copy->save();

        return redirect()
            ->route('lp-workers.index')
            ->with('success', __('Kandidat pekerja berhasil diduplikasi.'));
    }

    /**
     * Remove the specified worker from storage.
     */
    public function destroy(Worker $worker): RedirectResponse
    {
        Gate::authorize('lp-workers.delete');

        if ($worker->photo && Storage::disk('public')->exists($worker->photo)) {
            $isUsedByOthers = Worker::query()
                ->where('id', '!=', $worker->id)
                ->where('photo', $worker->photo)
                ->exists();

            if (! $isUsedByOthers) {
                Storage::disk('public')->delete($worker->photo);
            }
        }

        $worker->delete();

        return redirect()
            ->route('lp-workers.index')
            ->with('success', __('Kandidat pekerja berhasil dihapus.'));
    }

    /**
     * Export workers to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('lp-workers.view');

        return Excel::download(new WorkersExport, 'lp-workers-'.now()->format('Y-m-d-His').'.xlsx');
    }

    /**
     * Export workers to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('lp-workers.view');

        $workers = Worker::query()->latest('id')->get();
        $pdf = Pdf::loadView('modules.lp-workers.pdf', compact('workers'))->setPaper('a4', 'landscape');

        return $pdf->download('lp-workers-'.now()->format('Y-m-d-His').'.pdf');
    }

    /**
     * Download sample Excel template for importing workers.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('lp-workers.create');

        return Excel::download(new WorkersTemplateExport, 'lp-workers-template.xlsx');
    }

    /**
     * Import workers from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('lp-workers.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        Excel::import(new WorkersImport, $request->file('file'));

        return redirect()
            ->route('lp-workers.index')
            ->with('success', __('Kandidat pekerja berhasil diimport.'));
    }

    /**
     * Validate request payload for workers.
     *
     * @return array<string, mixed>
     */
    private function validateWorker(Request $request, ?Worker $worker = null): array
    {
        $categories = $this->categoryMap();

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('workers', 'code')->ignore($worker?->id)],
            'slug' => ['nullable', 'string', 'max:160', Rule::unique('workers', 'slug')->ignore($worker?->id)],
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:60'],
            'category_label' => ['nullable', 'string', 'max:120'],
            'age' => ['required', 'integer', 'min:17', 'max:75'],
            'origin' => ['nullable', 'string', 'max:150'],
            'origin_region' => ['nullable', 'string', 'max:100'],
            'education' => ['nullable', 'string', 'max:150'],
            'experience_years' => ['required', 'numeric', 'min:0', 'max:60'],
            'experience_label' => ['nullable', 'string', 'max:60'],
            'status' => ['required', Rule::in(['Available', 'In Training', 'Placed'])],
            'published_date' => ['nullable', 'date'],
            'photo' => ['nullable', 'string', 'max:255'],
            'photo_file' => ['nullable', 'image', 'max:2048'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'medical_check' => ['nullable', 'string', 'max:255'],
            'gallery_text' => ['nullable', 'string'],
            'languages_text' => ['nullable', 'string'],
            'skills_text' => ['nullable', 'string'],
            'work_history_text' => ['nullable', 'string'],
            'placement_preferences_text' => ['nullable', 'string'],
            'training_certifications_text' => ['nullable', 'string'],
        ]);

        $age = (int) $validated['age'];
        $exp = (float) $validated['experience_years'];

        $validated['slug'] = filled($validated['slug'] ?? null)
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);
        $validated['category_label'] = filled($validated['category_label'] ?? null)
            ? $validated['category_label']
            : ($categories[$validated['category']] ?? $validated['category']);
        $validated['age_bracket'] = match (true) {
            $age <= 25 => '18-25',
            $age <= 35 => '26-35',
            $age <= 45 => '36-45',
            default => '46+',
        };
        $validated['experience_bracket'] = match (true) {
            $exp < 1 => '0-1',
            $exp <= 3 => '1-3',
            $exp <= 5 => '3-5',
            default => '5+',
        };
        $validated['experience_label'] = filled($validated['experience_label'] ?? null)
            ? $validated['experience_label']
            : ($exp.' '.($exp == 1.0 ? 'Year' : 'Years'));
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['published_date'] = $validated['published_date'] ?? now()->toDateString();
        $validated['gallery'] = $this->linesToArray($request->input('gallery_text'));
        $validated['languages'] = $this->linesToArray($request->input('languages_text'));
        $validated['skills'] = $this->linesToArray($request->input('skills_text'));
        $validated['work_history'] = $this->linesToArray($request->input('work_history_text'));
        $validated['training_certifications'] = $this->linesToArray($request->input('training_certifications_text'));
        $validated['placement_preferences'] = $this->linesToKeyValue($request->input('placement_preferences_text'));

        unset(
            $validated['photo_file'],
            $validated['gallery_text'],
            $validated['languages_text'],
            $validated['skills_text'],
            $validated['work_history_text'],
            $validated['placement_preferences_text'],
            $validated['training_certifications_text']
        );

        return $validated;
    }

    /**
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

    /**
     * Parse "Key: Value" lines into an associative array.
     *
     * @return array<string, string>
     */
    private function linesToKeyValue(?string $text): array
    {
        $result = [];
        foreach ($this->linesToArray($text) as $line) {
            if (str_contains($line, ':')) {
                [$key, $val] = explode(':', $line, 2);
                $k = trim($key);
                $v = trim($val);
                if ($k !== '' && $v !== '') {
                    $result[$k] = $v;
                }
            }
        }

        return $result;
    }
}
