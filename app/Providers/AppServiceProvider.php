<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('layouts.partials.admin-notifications', function ($view) {
            if (auth()->check() && auth()->user()->role === 'admin') {
                $pendingUsers = \App\Models\User::where('tin_status', 'pending')->count();
                $pendingPayments = \App\Models\Payment::where('status', 'pending')->count();
                $pendingUploads = \App\Models\InterviewerUpload::where('status', 'uploaded')->count();

                $view->with('pendingUsers', $pendingUsers);
                $view->with('pendingPayments', $pendingPayments);
                $view->with('pendingUploads', $pendingUploads);
                $view->with('totalNotifications', $pendingUsers + $pendingPayments + $pendingUploads);
            }
        });
    }
}
