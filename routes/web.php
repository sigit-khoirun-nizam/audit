<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// dashboard pages
Route::get('/', function () {
    return redirect()->route('dashboard.index');
})->name('dashboard');

// calender pages
Route::get('/calendar', function () {
    return view('pages.calender', ['title' => 'Calendar']);
})->name('calendar');

// profile pages
Route::get('/profile', function () {
    return view('pages.profile', ['title' => 'Profile']);
})->name('profile');

// form pages
Route::get('/form-elements', function () {
    return view('pages.form.form-elements', ['title' => 'Form Elements']);
})->name('form-elements');

// tables pages
Route::get('/basic-tables', function () {
    return view('pages.tables.basic-tables', ['title' => 'Basic Tables']);
})->name('basic-tables');

// pages

Route::get('/blank', function () {
    return view('pages.blank', ['title' => 'Blank']);
})->name('blank');

// error pages
Route::get('/error-404', function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
})->name('error-404');

// chart pages
Route::get('/line-chart', function () {
    return view('pages.chart.line-chart', ['title' => 'Line Chart']);
})->name('line-chart');

Route::get('/bar-chart', function () {
    return view('pages.chart.bar-chart', ['title' => 'Bar Chart']);
})->name('bar-chart');


// authentication pages
use App\Http\Controllers\Auth\LoginController;

Route::get('/signin', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/signin', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ui elements pages
Route::get('/alerts', function () {
    return view('pages.ui-elements.alerts', ['title' => 'Alerts']);
})->name('alerts');

Route::get('/avatars', function () {
    return view('pages.ui-elements.avatars', ['title' => 'Avatars']);
})->name('avatars');

Route::get('/badge', function () {
    return view('pages.ui-elements.badges', ['title' => 'Badges']);
})->name('badges');

Route::get('/buttons', function () {
    return view('pages.ui-elements.buttons', ['title' => 'Buttons']);
})->name('buttons');

Route::get('/image', function () {
    return view('pages.ui-elements.images', ['title' => 'Images']);
})->name('images');

Route::get('/videos', function () {
    return view('pages.ui-elements.videos', ['title' => 'Videos']);
})->name('videos');

use App\Http\Controllers\AuditTransactionController;
use App\Http\Controllers\AuditResponseController;
use App\Http\Controllers\AuditCommentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Users Management
    Route::resource('users', UserController::class)->middleware('role:Superadmin');

    // Audit Transactions
    Route::get('audit/import/template', [AuditTransactionController::class, 'downloadTemplate'])->name('audit.import.template');
    Route::post('audit/import', [AuditTransactionController::class, 'importExcel'])->name('audit.import');
    Route::get('audit/{id}/pdf', [AuditTransactionController::class, 'downloadPdf'])->name('audit.pdf');
    Route::resource('audit', AuditTransactionController::class);

    // Responses & Evidence
    Route::post('audit/{id}/response', [AuditResponseController::class, 'store'])->name('audit.response.store');

    // Comments / Discussion
    Route::post('audit/{id}/comment', [AuditCommentController::class, 'store'])->name('audit.comment.store');

    // Reports
    Route::get('report', [ReportController::class, 'index'])->name('report.index');
    Route::get('report/export/excel', [ReportController::class, 'exportExcel'])->name('report.export.excel');
    Route::get('report/export/pdf', [ReportController::class, 'exportPdf'])->name('report.export.pdf');

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index')->middleware('role:Superadmin|Auditor');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update')->middleware('role:Superadmin|Auditor');
});






















