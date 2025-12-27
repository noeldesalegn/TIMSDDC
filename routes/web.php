<?php

use App\Http\Controllers\AdminTaxpayerController;
use App\Http\Controllers\InterviewerUploadController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TaxpayerController;
use App\Http\Controllers\InterviewerController;
use App\Http\Controllers\CashierPaymentController;

Route::get('/', function () {
    return view('landing');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    // Admin Taxpayers
    Route::get('/admin/taxpayers', [AdminTaxpayerController::class, 'index'])->name('admin.taxpayers.index');
    Route::get('/admin/taxpayers/export', [AdminTaxpayerController::class, 'export'])->name('admin.taxpayers.export');
    Route::post('/admin/taxpayers/bulk-verify', [AdminTaxpayerController::class, 'bulkVerify'])->name('admin.taxpayers.bulkVerify');
    Route::get('/admin/taxpayers/payments', [AdminTaxpayerController::class, 'payments'])->name('admin.taxpayers.payments');
    Route::get('/admin/taxpayers/{user}', [AdminTaxpayerController::class, 'show'])->name('admin.taxpayers.show');
    Route::patch('/admin/taxpayers/{user}', [AdminTaxpayerController::class, 'update'])->name('admin.taxpayers.update');
    Route::post('/admin/taxpayers/payments/{id}/verify', [AdminTaxpayerController::class, 'verifyPayment'])->name('admin.taxpayers.payments.verify');
    Route::post('/admin/taxpayers/payments/{id}/reject', [AdminTaxpayerController::class, 'rejectPayment'])->name('admin.taxpayers.payments.reject');

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

    // Admin tax summaries
    Route::get('/admin/tax', [AdminTaxpayerController::class, 'taxcalc'])->name('admin.tax.index');
    Route::post('/admin/tax/{summary}/calculate',
        [AdminTaxpayerController::class, 'calculateTax'])
        ->name('admin.tax.calculate');
    Route::get('/admin/tax/{summary}/verify', [AdminTaxpayerController::class, 'verify'])
        ->name('admin.tax.verify');

    Route::get('/admin/tax/{summary}/edit', [AdminTaxpayerController::class, 'edit'])
        ->name('admin.tax.edit');
    Route::patch('/admin/tax/{summary}', [AdminTaxpayerController::class, 'updateTax'])
        ->name('admin.tax.update');

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
    Route::get('/interviewer/{user}/taxpayer', [InterviewerController::class, 'taxpayer'])->name('interviewer.taxpayer.show');
    // Interviewer File Uploads
    Route::get('/interviewer/upload', [InterviewerUploadController::class, 'index'])->name('interviewer.upload');
    Route::post('/interviewer/upload', [InterviewerUploadController::class, 'store'])->name('interviewer.upload.store');
    Route::get('/interviewer/upload/download/{upload}', [InterviewerUploadController::class, 'download'])->name('interviewer.upload.download');
    Route::delete('/interviewer/upload/{upload}', [InterviewerUploadController::class, 'destroy'])->name('interviewer.upload.destroy');
    Route::get(
        '/interviewer/taxpayers/{taxpayer}/taxpayeruploads',
        [InterviewerUploadController::class, 'taxpayerindex']
    )->name('interviewer.taxpayer.taxpayeruploads');

    Route::post(
        '/interviewer/taxpayers/{taxpayer}/taxpayeruploads',
        [InterviewerUploadController::class, 'taxpayerindexstore']
    )->name('interviewer.taxpayer.uploads.taxpayerstore');

    Route::get(
        '/interviewer/taxpayers/{taxpayer}/taxpayeruploads/{upload}/download',
        [InterviewerUploadController::class, 'taxpayerDownload']
    )->name('interviewer.taxpayer.uploads.download');

    Route::delete(
        '/interviewer/taxpayers/{taxpayer}/taxpayeruploads/{upload}',
        [InterviewerUploadController::class, 'taxpayerDestroy']
    )->name('interviewer.taxpayer.uploads.destroy');


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


Route::middleware(['auth', 'role:cashier'])->group(function () {
    Route::get('/cashier/dashboard', [CashierPaymentController::class, 'dashboard'])->name('cashier.dashboard');
    Route::get('/cashier/payments', [CashierPaymentController::class, 'viewPaymentHistory'])->name('cashier.payments.index');
    Route::get('/cashier/payments/create', [CashierPaymentController::class, 'create'])->name('cashier.payments.create');
    Route::post('/cashier/payments', [CashierPaymentController::class, 'processPayment'])->name('cashier.payments.store');
    Route::get('/cashier/payments/{payment}/receipt', [CashierPaymentController::class, 'generateReceipt'])->name('cashier.payments.receipt');
    Route::post('/cashier/payments/{payment}/refund', [CashierPaymentController::class, 'processRefunds'])->name('cashier.payments.refund');
    Route::get('/cashier/api/verify-taxpayer', [CashierPaymentController::class, 'verifyTaxpayer'])->name('cashier.taxpayers.verify');
});


Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'taxpayer') {
        return redirect()->route('taxpayer.dashboard');
    } elseif ($user->role === 'interviewer') {
        return redirect()->route('interviewer.dashboard');
    } elseif ($user->role === 'cashier') {
        return redirect()->route('cashier.dashboard');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';

