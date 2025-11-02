<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TaxpayerController;
use App\Http\Controllers\InterviewerController;

Route::get('/', function () {
    return view('welcome');
});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    // Admin Taxpayers
    Route::get('/admin/taxpayers', [\App\Http\Controllers\AdminTaxpayerController::class, 'index'])->name('admin.taxpayers.index');
    Route::get('/admin/taxpayers/export', [\App\Http\Controllers\AdminTaxpayerController::class, 'export'])->name('admin.taxpayers.export');
    Route::post('/admin/taxpayers/bulk-verify', [\App\Http\Controllers\AdminTaxpayerController::class, 'bulkVerify'])->name('admin.taxpayers.bulkVerify');
    Route::get('/admin/taxpayers/{user}', [\App\Http\Controllers\AdminTaxpayerController::class, 'show'])->name('admin.taxpayers.show');
    Route::patch('/admin/taxpayers/{user}', [\App\Http\Controllers\AdminTaxpayerController::class, 'update'])->name('admin.taxpayers.update');

    // Admin News
    Route::get('/admin/news', [\App\Http\Controllers\AdminNewsController::class, 'index'])->name('admin.news.index');
    Route::get('/admin/news/create', [\App\Http\Controllers\AdminNewsController::class, 'create'])->name('admin.news.create');
    Route::post('/admin/news', [\App\Http\Controllers\AdminNewsController::class, 'store'])->name('admin.news.store');
    Route::get('/admin/news/{news}/edit', [\App\Http\Controllers\AdminNewsController::class, 'edit'])->name('admin.news.edit');
    Route::patch('/admin/news/{news}', [\App\Http\Controllers\AdminNewsController::class, 'update'])->name('admin.news.update');
    Route::patch('/admin/news/{news}/toggle', [\App\Http\Controllers\AdminNewsController::class, 'toggle'])->name('admin.news.toggle');

    // Admin Reports
    Route::get('/admin/reports', [\App\Http\Controllers\AdminReportsController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/reports/export', [\App\Http\Controllers\AdminReportsController::class, 'export'])->name('admin.reports.export');
    Route::get('/admin/reports/data', [\App\Http\Controllers\AdminReportsController::class, 'data'])->name('admin.reports.data');

    // Admin Complaints
    Route::get('/admin/complaints', [\App\Http\Controllers\AdminComplaintsController::class, 'index'])->name('admin.complaints.index');
    Route::get('/admin/complaints/{complaint}', [\App\Http\Controllers\AdminComplaintsController::class, 'show'])->name('admin.complaints.show');
    Route::patch('/admin/complaints/{complaint}', [\App\Http\Controllers\AdminComplaintsController::class, 'update'])->name('admin.complaints.update');
});
Route::middleware(['auth', 'role:taxpayer'])->group(function () {
    Route::get('/taxpayer/dashboard', [TaxpayerController::class, 'index'])->name('taxpayer.dashboard');
    Route::get('/taxpayer/summary', [TaxpayerController::class, 'summary'])->name('taxpayer.summary');
    Route::get('/taxpayer/payment', [TaxpayerController::class, 'paymentForm'])->name('taxpayer.payment');
    Route::post('/taxpayer/payment', [TaxpayerController::class, 'processPayment'])->name('taxpayer.payment.process');
    Route::get('/taxpayer/complaints', [TaxpayerController::class, 'complaints'])->name('taxpayer.complaints');
    Route::post('/taxpayer/complaints', [TaxpayerController::class, 'submitComplaint'])->name('taxpayer.complaints.submit');
    Route::get('/taxpayer/news', [TaxpayerController::class, 'news'])->name('taxpayer.news');
    Route::post('/taxpayer/news/{newsId}/comments', [TaxpayerController::class, 'submitComment'])->name('taxpayer.news.comment');
});
Route::middleware(['auth', 'role:interviewer'])->group(function () {
    Route::get('/interviewer/dashboard', [InterviewerController::class, 'index'])->name('interviewer.dashboard');

    // Interviewer File Uploads
    Route::get('/interviewer/upload', [\App\Http\Controllers\InterviewerUploadController::class, 'index'])->name('interviewer.upload');
    Route::post('/interviewer/upload', [\App\Http\Controllers\InterviewerUploadController::class, 'store'])->name('interviewer.upload.store');
    Route::get('/interviewer/upload/download/{upload}', [\App\Http\Controllers\InterviewerUploadController::class, 'download'])->name('interviewer.upload.download');
    Route::delete('/interviewer/upload/{upload}', [\App\Http\Controllers\InterviewerUploadController::class, 'destroy'])->name('interviewer.upload.destroy');

    // Interviewer Schedule
    Route::get('/interviewer/schedule', [\App\Http\Controllers\InterviewerScheduleController::class, 'index'])->name('interviewer.schedule');
    Route::get('/interviewer/schedule/events', [\App\Http\Controllers\InterviewerScheduleController::class, 'events'])->name('interviewer.schedule.events');
    Route::post('/interviewer/schedule', [\App\Http\Controllers\InterviewerScheduleController::class, 'store'])->name('interviewer.schedule.store');
    Route::patch('/interviewer/schedule/{appointment}', [\App\Http\Controllers\InterviewerScheduleController::class, 'update'])->name('interviewer.schedule.update');
    Route::delete('/interviewer/schedule/{appointment}', [\App\Http\Controllers\InterviewerScheduleController::class, 'destroy'])->name('interviewer.schedule.destroy');

    // Interviewer Income Reports
    Route::get('/interviewer/reports', [\App\Http\Controllers\InterviewerReportsController::class, 'index'])->name('interviewer.reports');
    Route::get('/interviewer/reports/create', [\App\Http\Controllers\InterviewerReportsController::class, 'create'])->name('interviewer.reports.create');
    Route::post('/interviewer/reports', [\App\Http\Controllers\InterviewerReportsController::class, 'store'])->name('interviewer.reports.store');
    Route::get('/interviewer/reports/{report}', [\App\Http\Controllers\InterviewerReportsController::class, 'show'])->name('interviewer.reports.show');
    Route::patch('/interviewer/reports/{report}', [\App\Http\Controllers\InterviewerReportsController::class, 'update'])->name('interviewer.reports.update');
});
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    // Redirect based on role
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'taxpayer') {
        return redirect()->route('taxpayer.dashboard');
    } elseif ($user->role === 'interviewer') {
        return redirect()->route('interviewer.dashboard');
    }
    
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';

