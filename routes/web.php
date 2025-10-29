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

