<?php

use App\Http\Controllers\Admin\News\NewsCategoryController as AdminNewsCategoryController;
use App\Http\Controllers\Admin\News\NewsCommentController as AdminNewsCommentController;
use App\Http\Controllers\Admin\News\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\News\NewsTagController as AdminNewsTagController;
use App\Http\Controllers\News\NewsController as PublicNewsController;
use Illuminate\Support\Facades\Route;

Route::prefix('news')->name('news.')->group(function () {
    Route::view('/', 'news.index')->name('index');
    Route::view('/index.html', 'news.index')->name('index.html');
    Route::get('/all', [PublicNewsController::class, 'all'])->name('all');
    Route::get('/p/{slug}', [PublicNewsController::class, 'show'])->name('show');
    Route::post('/p/{slug}/comment', [PublicNewsController::class, 'storeComment'])->name('comment.store');
    Route::get('/about', [PublicNewsController::class, 'about'])->name('about');
    Route::view('/categori', 'news.categori')->name('categori');
    Route::view('/latest_news', 'news.latest_news')->name('latest_news');
    Route::view('/blog', 'news.blog')->name('blog');
    Route::view('/blog_details', 'news.blog_details')->name('blog_details');
    Route::view('/elements', 'news.elements')->name('elements');
    Route::view('/contact', 'news.contact')->name('contact');
});

Route::prefix('app')->middleware('auth')->group(function () {
    Route::get('news-categories/export', [AdminNewsCategoryController::class, 'export'])->name('news-categories.export');
    Route::get('news-categories/export-pdf', [AdminNewsCategoryController::class, 'exportPdf'])->name('news-categories.exportPdf');
    Route::get('news-categories/template', [AdminNewsCategoryController::class, 'template'])->name('news-categories.template');
    Route::post('news-categories/import', [AdminNewsCategoryController::class, 'import'])->name('news-categories.import');
    Route::resource('news-categories', AdminNewsCategoryController::class)->parameters([
        'news-categories' => 'newsCategory',
    ]);

    Route::get('news-tags/export', [AdminNewsTagController::class, 'export'])->name('news-tags.export');
    Route::get('news-tags/export-pdf', [AdminNewsTagController::class, 'exportPdf'])->name('news-tags.exportPdf');
    Route::get('news-tags/template', [AdminNewsTagController::class, 'template'])->name('news-tags.template');
    Route::post('news-tags/import', [AdminNewsTagController::class, 'import'])->name('news-tags.import');
    Route::resource('news-tags', AdminNewsTagController::class)->parameters([
        'news-tags' => 'newsTag',
    ]);

    Route::get('news/export', [AdminNewsController::class, 'export'])->name('admin.news.export');
    Route::get('news/export-pdf', [AdminNewsController::class, 'exportPdf'])->name('admin.news.exportPdf');
    Route::get('news/template', [AdminNewsController::class, 'template'])->name('admin.news.template');
    Route::post('news/import', [AdminNewsController::class, 'import'])->name('admin.news.import');
    Route::resource('news', AdminNewsController::class)->parameters([
        'news' => 'news',
    ])->names('admin.news');

    Route::get('news-comments/export', [AdminNewsCommentController::class, 'export'])->name('news-comments.export');
    Route::get('news-comments/export-pdf', [AdminNewsCommentController::class, 'exportPdf'])->name('news-comments.exportPdf');
    Route::get('news-comments/template', [AdminNewsCommentController::class, 'template'])->name('news-comments.template');
    Route::post('news-comments/import', [AdminNewsCommentController::class, 'import'])->name('news-comments.import');
    Route::patch('news-comments/{newsComment}/toggle-approval', [AdminNewsCommentController::class, 'toggleApproval'])->name('news-comments.toggleApproval');
    Route::post('news-comments/{newsComment}/reply', [AdminNewsCommentController::class, 'reply'])->name('news-comments.reply');
    Route::resource('news-comments', AdminNewsCommentController::class)->only(['index', 'show', 'destroy'])->parameters([
        'news-comments' => 'newsComment',
    ]);
});
