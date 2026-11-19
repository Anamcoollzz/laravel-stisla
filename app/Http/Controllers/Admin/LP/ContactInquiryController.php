<?php

namespace App\Http\Controllers\Admin\LP;

use App\Exports\ContactInquiriesExport;
use App\Exports\ContactInquiriesTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\ContactInquiriesImport;
use App\Models\LP\ContactInquiry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ContactInquiryController extends Controller
{
    /**
     * Display a listing of contact inquiries.
     */
    public function index(Request $request): View
    {
        Gate::authorize('lp-contact-inquiries.view');

        $status = (string) $request->query('status', 'all');

        $inquiries = ContactInquiry::query()
            ->when($status === 'unread', fn ($q) => $q->unread())
            ->when($status === 'read', fn ($q) => $q->read())
            ->latest('id')
            ->get();

        $totalCount = ContactInquiry::query()->count();
        $unreadCount = ContactInquiry::query()->unread()->count();
        $readCount = $totalCount - $unreadCount;

        return view('modules.lp-contact-inquiries.index', compact(
            'inquiries',
            'status',
            'totalCount',
            'unreadCount',
            'readCount'
        ));
    }

    /**
     * Display the specified contact inquiry and mark it as read.
     */
    public function show(ContactInquiry $contactInquiry): View
    {
        Gate::authorize('lp-contact-inquiries.view');

        if (! $contactInquiry->is_read) {
            $contactInquiry->update(['is_read' => true]);
        }

        return view('modules.lp-contact-inquiries.show', compact('contactInquiry'));
    }

    /**
     * Toggle read/unread status of the specified contact inquiry.
     */
    public function toggleRead(ContactInquiry $contactInquiry): RedirectResponse
    {
        Gate::authorize('lp-contact-inquiries.edit');

        $contactInquiry->update([
            'is_read' => ! $contactInquiry->is_read,
        ]);

        return back()->with(
            'success',
            $contactInquiry->is_read
                ? __('Pesan kontak ditandai sudah dibaca.')
                : __('Pesan kontak ditandai belum dibaca.')
        );
    }

    /**
     * Remove the specified contact inquiry from storage.
     */
    public function destroy(ContactInquiry $contactInquiry): RedirectResponse
    {
        Gate::authorize('lp-contact-inquiries.delete');

        $contactInquiry->delete();

        return redirect()
            ->route('lp-contact-inquiries.index')
            ->with('success', __('Pesan kontak berhasil dihapus.'));
    }

    /**
     * Export contact inquiries to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('lp-contact-inquiries.view');

        return Excel::download(new ContactInquiriesExport, 'lp-contact-inquiries-'.now()->format('Y-m-d-His').'.xlsx');
    }

    /**
     * Export contact inquiries to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('lp-contact-inquiries.view');

        $inquiries = ContactInquiry::query()->latest('id')->get();
        $pdf = Pdf::loadView('modules.lp-contact-inquiries.pdf', compact('inquiries'))->setPaper('a4', 'landscape');

        return $pdf->download('lp-contact-inquiries-'.now()->format('Y-m-d-His').'.pdf');
    }

    /**
     * Download sample Excel template for importing contact inquiries.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('lp-contact-inquiries.create');

        return Excel::download(new ContactInquiriesTemplateExport, 'lp-contact-inquiries-template.xlsx');
    }

    /**
     * Import contact inquiries from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('lp-contact-inquiries.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        Excel::import(new ContactInquiriesImport, $request->file('file'));

        return redirect()
            ->route('lp-contact-inquiries.index')
            ->with('success', __('Pesan kontak berhasil diimport.'));
    }
}
