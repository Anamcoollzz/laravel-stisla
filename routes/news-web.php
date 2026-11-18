<?php

use App\Http\Controllers\News\NewsController as PublicNewsController;
use Illuminate\Support\Facades\Route;

Route::prefix('news')->name('news.')->group(function () {
    Route::view('/', 'news.index')->name('index');
    Route::view('/index.html', 'news.index')->name('index.html');
    Route::get('/all', [PublicNewsController::class, 'all'])->name('all');
    Route::get('/p/{slug}', [PublicNewsController::class, 'show'])->name('show');
    Route::view('/about', 'news.about')->name('about');
    Route::view('/categori', 'news.categori')->name('categori');
    Route::view('/latest_news', 'news.latest_news')->name('latest_news');
    Route::view('/blog', 'news.blog')->name('blog');
    Route::view('/blog_details', 'news.blog_details')->name('blog_details');
    Route::view('/elements', 'news.elements')->name('elements');
    Route::view('/contact', 'news.contact')->name('contact');
});
