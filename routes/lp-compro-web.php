<?php

use App\Http\Controllers\Admin\LP\CompanyIntroductionController as AdminCompanyIntroductionController;
use App\Http\Controllers\Admin\LP\CompanyMilestoneController as AdminCompanyMilestoneController;
use App\Http\Controllers\Admin\LP\CompanyProfileController as AdminCompanyProfileController;
use App\Http\Controllers\Admin\LP\CompanyStatController as AdminCompanyStatController;
use App\Http\Controllers\Admin\LP\CompanyValueController as AdminCompanyValueController;
use App\Http\Controllers\Admin\LP\ContactInquiryController as AdminContactInquiryController;
use App\Http\Controllers\Admin\LP\LpFaqController as AdminLpFaqController;
use App\Http\Controllers\Admin\LP\LpGalleryController as AdminLpGalleryController;
use App\Http\Controllers\Admin\LP\RecruitmentServiceController as AdminRecruitmentServiceController;
use App\Http\Controllers\Admin\LP\RecruitmentStepController as AdminRecruitmentStepController;
use App\Http\Controllers\Admin\LP\RecruitmentTestimonialController as AdminRecruitmentTestimonialController;
use App\Http\Controllers\Admin\LP\VisionMissionController as AdminVisionMissionController;
use App\Http\Controllers\Admin\LP\WhyChooseUsController as AdminWhyChooseUsController;
use App\Http\Controllers\Admin\LP\WorkerController as AdminWorkerController;
use App\Http\Controllers\LP\PageController;
use App\Http\Controllers\LP\WorkerCatalogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/catalog', [WorkerCatalogController::class, 'index'])->name('catalog');
Route::get('/catalog/{slug}', [WorkerCatalogController::class, 'show'])->name('worker.detail');
Route::get('/gallery', [PageController::class, 'gallery'])->name('gallery');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'storeContact'])->name('contact.store');

Route::prefix('app')->middleware('auth')->group(function () {
    Route::get('lp-company-stats/export', [AdminCompanyStatController::class, 'export'])->name('lp-company-stats.export');
    Route::get('lp-company-stats/export-pdf', [AdminCompanyStatController::class, 'exportPdf'])->name('lp-company-stats.exportPdf');
    Route::get('lp-company-stats/template', [AdminCompanyStatController::class, 'template'])->name('lp-company-stats.template');
    Route::post('lp-company-stats/import', [AdminCompanyStatController::class, 'import'])->name('lp-company-stats.import');
    Route::post('lp-company-stats/{companyStat}/duplicate', [AdminCompanyStatController::class, 'duplicate'])->name('lp-company-stats.duplicate');
    Route::resource('lp-company-stats', AdminCompanyStatController::class)->parameters([
        'lp-company-stats' => 'companyStat',
    ]);

    Route::get('lp-services/export', [AdminRecruitmentServiceController::class, 'export'])->name('lp-services.export');
    Route::get('lp-services/export-pdf', [AdminRecruitmentServiceController::class, 'exportPdf'])->name('lp-services.exportPdf');
    Route::get('lp-services/template', [AdminRecruitmentServiceController::class, 'template'])->name('lp-services.template');
    Route::post('lp-services/import', [AdminRecruitmentServiceController::class, 'import'])->name('lp-services.import');
    Route::post('lp-services/{recruitmentService}/duplicate', [AdminRecruitmentServiceController::class, 'duplicate'])->name('lp-services.duplicate');
    Route::resource('lp-services', AdminRecruitmentServiceController::class)->parameters([
        'lp-services' => 'recruitmentService',
    ]);

    Route::get('lp-why-choose-us/export', [AdminWhyChooseUsController::class, 'export'])->name('lp-why-choose-us.export');
    Route::get('lp-why-choose-us/export-pdf', [AdminWhyChooseUsController::class, 'exportPdf'])->name('lp-why-choose-us.exportPdf');
    Route::get('lp-why-choose-us/template', [AdminWhyChooseUsController::class, 'template'])->name('lp-why-choose-us.template');
    Route::post('lp-why-choose-us/import', [AdminWhyChooseUsController::class, 'import'])->name('lp-why-choose-us.import');
    Route::post('lp-why-choose-us/{whyChooseUs}/duplicate', [AdminWhyChooseUsController::class, 'duplicate'])->name('lp-why-choose-us.duplicate');
    Route::resource('lp-why-choose-us', AdminWhyChooseUsController::class)->parameters([
        'lp-why-choose-us' => 'whyChooseUs',
    ]);

    Route::get('lp-recruitment-steps/export', [AdminRecruitmentStepController::class, 'export'])->name('lp-recruitment-steps.export');
    Route::get('lp-recruitment-steps/export-pdf', [AdminRecruitmentStepController::class, 'exportPdf'])->name('lp-recruitment-steps.exportPdf');
    Route::get('lp-recruitment-steps/template', [AdminRecruitmentStepController::class, 'template'])->name('lp-recruitment-steps.template');
    Route::post('lp-recruitment-steps/import', [AdminRecruitmentStepController::class, 'import'])->name('lp-recruitment-steps.import');
    Route::post('lp-recruitment-steps/{recruitmentStep}/duplicate', [AdminRecruitmentStepController::class, 'duplicate'])->name('lp-recruitment-steps.duplicate');
    Route::resource('lp-recruitment-steps', AdminRecruitmentStepController::class)->parameters([
        'lp-recruitment-steps' => 'recruitmentStep',
    ]);

    Route::get('lp-testimonials/export', [AdminRecruitmentTestimonialController::class, 'export'])->name('lp-testimonials.export');
    Route::get('lp-testimonials/export-pdf', [AdminRecruitmentTestimonialController::class, 'exportPdf'])->name('lp-testimonials.exportPdf');
    Route::get('lp-testimonials/template', [AdminRecruitmentTestimonialController::class, 'template'])->name('lp-testimonials.template');
    Route::post('lp-testimonials/import', [AdminRecruitmentTestimonialController::class, 'import'])->name('lp-testimonials.import');
    Route::post('lp-testimonials/{recruitmentTestimonial}/duplicate', [AdminRecruitmentTestimonialController::class, 'duplicate'])->name('lp-testimonials.duplicate');
    Route::resource('lp-testimonials', AdminRecruitmentTestimonialController::class)->parameters([
        'lp-testimonials' => 'recruitmentTestimonial',
    ]);

    Route::get('lp-faqs/export', [AdminLpFaqController::class, 'export'])->name('lp-faqs.export');
    Route::get('lp-faqs/export-pdf', [AdminLpFaqController::class, 'exportPdf'])->name('lp-faqs.exportPdf');
    Route::get('lp-faqs/template', [AdminLpFaqController::class, 'template'])->name('lp-faqs.template');
    Route::post('lp-faqs/import', [AdminLpFaqController::class, 'import'])->name('lp-faqs.import');
    Route::post('lp-faqs/{lpFaq}/duplicate', [AdminLpFaqController::class, 'duplicate'])->name('lp-faqs.duplicate');
    Route::resource('lp-faqs', AdminLpFaqController::class)->parameters([
        'lp-faqs' => 'lpFaq',
    ]);

    Route::get('lp-workers/export', [AdminWorkerController::class, 'export'])->name('lp-workers.export');
    Route::get('lp-workers/export-pdf', [AdminWorkerController::class, 'exportPdf'])->name('lp-workers.exportPdf');
    Route::get('lp-workers/template', [AdminWorkerController::class, 'template'])->name('lp-workers.template');
    Route::post('lp-workers/import', [AdminWorkerController::class, 'import'])->name('lp-workers.import');
    Route::post('lp-workers/{worker}/duplicate', [AdminWorkerController::class, 'duplicate'])->name('lp-workers.duplicate');
    Route::resource('lp-workers', AdminWorkerController::class)->parameters([
        'lp-workers' => 'worker',
    ]);

    Route::get('lp-galleries/export', [AdminLpGalleryController::class, 'export'])->name('lp-galleries.export');
    Route::get('lp-galleries/export-pdf', [AdminLpGalleryController::class, 'exportPdf'])->name('lp-galleries.exportPdf');
    Route::get('lp-galleries/template', [AdminLpGalleryController::class, 'template'])->name('lp-galleries.template');
    Route::post('lp-galleries/import', [AdminLpGalleryController::class, 'import'])->name('lp-galleries.import');
    Route::post('lp-galleries/{lpGallery}/duplicate', [AdminLpGalleryController::class, 'duplicate'])->name('lp-galleries.duplicate');
    Route::resource('lp-galleries', AdminLpGalleryController::class)->parameters([
        'lp-galleries' => 'lpGallery',
    ]);

    Route::get('lp-company-profile', [AdminCompanyProfileController::class, 'edit'])->name('lp-company-profile.edit');
    Route::put('lp-company-profile', [AdminCompanyProfileController::class, 'update'])->name('lp-company-profile.update');

    Route::get('lp-company-introduction', [AdminCompanyIntroductionController::class, 'edit'])->name('lp-company-introduction.edit');
    Route::put('lp-company-introduction', [AdminCompanyIntroductionController::class, 'update'])->name('lp-company-introduction.update');

    Route::get('lp-milestones/export', [AdminCompanyMilestoneController::class, 'export'])->name('lp-milestones.export');
    Route::get('lp-milestones/export-pdf', [AdminCompanyMilestoneController::class, 'exportPdf'])->name('lp-milestones.exportPdf');
    Route::get('lp-milestones/template', [AdminCompanyMilestoneController::class, 'template'])->name('lp-milestones.template');
    Route::post('lp-milestones/import', [AdminCompanyMilestoneController::class, 'import'])->name('lp-milestones.import');
    Route::post('lp-milestones/{companyMilestone}/duplicate', [AdminCompanyMilestoneController::class, 'duplicate'])->name('lp-milestones.duplicate');
    Route::resource('lp-milestones', AdminCompanyMilestoneController::class)->parameters([
        'lp-milestones' => 'companyMilestone',
    ]);

    Route::get('lp-vision-mission', [AdminVisionMissionController::class, 'edit'])->name('lp-vision-mission.edit');
    Route::put('lp-vision-mission', [AdminVisionMissionController::class, 'update'])->name('lp-vision-mission.update');

    Route::get('lp-company-values/export', [AdminCompanyValueController::class, 'export'])->name('lp-company-values.export');
    Route::get('lp-company-values/export-pdf', [AdminCompanyValueController::class, 'exportPdf'])->name('lp-company-values.exportPdf');
    Route::get('lp-company-values/template', [AdminCompanyValueController::class, 'template'])->name('lp-company-values.template');
    Route::post('lp-company-values/import', [AdminCompanyValueController::class, 'import'])->name('lp-company-values.import');
    Route::post('lp-company-values/{companyValue}/duplicate', [AdminCompanyValueController::class, 'duplicate'])->name('lp-company-values.duplicate');
    Route::resource('lp-company-values', AdminCompanyValueController::class)->parameters([
        'lp-company-values' => 'companyValue',
    ]);

    Route::get('lp-contact-inquiries/export', [AdminContactInquiryController::class, 'export'])->name('lp-contact-inquiries.export');
    Route::get('lp-contact-inquiries/export-pdf', [AdminContactInquiryController::class, 'exportPdf'])->name('lp-contact-inquiries.exportPdf');
    Route::get('lp-contact-inquiries/template', [AdminContactInquiryController::class, 'template'])->name('lp-contact-inquiries.template');
    Route::post('lp-contact-inquiries/import', [AdminContactInquiryController::class, 'import'])->name('lp-contact-inquiries.import');
    Route::patch('lp-contact-inquiries/{contactInquiry}/toggle-read', [AdminContactInquiryController::class, 'toggleRead'])->name('lp-contact-inquiries.toggleRead');
    Route::resource('lp-contact-inquiries', AdminContactInquiryController::class)->only(['index', 'show', 'destroy'])->parameters([
        'lp-contact-inquiries' => 'contactInquiry',
    ]);
});
