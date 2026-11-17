<?php

use Illuminate\Support\Facades\Route;

Route::prefix('pages-spark-admin')->name('pages.spark-admin.')->group(function () {
    Route::view('/dashboard', 'spark-admin.index')->name('index');
    Route::view('/tables-basic', 'spark-admin.tables-basic')->name('tables-basic');
    Route::view('/ui-forms', 'spark-admin.ui-forms')->name('ui-forms');
    Route::view('/ui-buttons', 'spark-admin.ui-buttons')->name('ui-buttons');
    Route::view('/page-blank', 'spark-admin.page-blank')->name('page-blank');
    Route::view('/page-login', 'spark-admin.page-login')->name('page-login');
    Route::view('/page-404', 'spark-admin.page-404')->name('page-404');
});
