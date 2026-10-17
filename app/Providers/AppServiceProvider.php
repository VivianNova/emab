<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\ContactMessage;
use App\Models\QuoteRequest;
use App\Observers\QuoteRequestObserver;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\View;
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
        // Quote notifications (new request + status changes)
        QuoteRequest::observe(QuoteRequestObserver::class);

        // Log any queued job that fails for good
        Queue::failing(function (JobFailed $event) {
            Log::error('Queue job failed', [
                'job'   => $event->job->resolveName(),
                'error' => $event->exception->getMessage(),
            ]);
        });

        // Admin sidebar badge counts
        View::composer('partials.admin.sidebar', function ($view) {
            $user = auth()->user();

            $view->with('menuCounts', [
                'quotes'   => QuoteRequest::where('status', 'new')->count(),
                // Staff see how many active jobs are assigned to them
                'bookings' => $user?->isStaff()
                    ? Booking::where('technician_id', $user->id)
                        ->whereIn('status', ['confirmed', 'in_progress'])
                        ->count()
                    : Booking::where('status', 'new')->count(),
                'messages' => ContactMessage::where('is_read', false)->count(),
            ]);
        });
    }
}