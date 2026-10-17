<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\BookingsController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ProjectsController;
use App\Http\Controllers\Admin\QuoteRequestController;
use App\Http\Controllers\Admin\ServicesController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('notifications/{id}', [NotificationController::class, 'open'])->name('notifications.open');
Route::post('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'about')->name('about');

Route::get('/services', [ServiceController::class, 'index'])->name('services');
Route::get('/services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');

Route::get('/quote', [QuoteController::class, 'create'])->name('quote');
Route::post('/quote', [QuoteController::class, 'store'])
    ->middleware('throttle:5,10')
    ->name('quote.store');

Route::get('/projects', [ProjectController::class, 'index'])->name('projects');
Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

Route::get('/book', [BookingController::class, 'create'])->name('book');
Route::post('/book', [BookingController::class, 'store'])
    ->middleware('throttle:5,10')
    ->name('book.store');

Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,10')
    ->name('contact.store');

Route::redirect('/login', '/admin/login')->middleware('guest')->name('login');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'create'])->name('login');
        Route::post('login', [LoginController::class, 'store'])
            ->middleware('throttle:5,1')
            ->name('login.store');
    });

    // Super admin, admin and staff
    Route::middleware(['auth', 'role:super_admin,admin,staff'])->group(function () {
        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('bookings', [BookingsController::class, 'index'])->name('bookings.index');
        Route::get('bookings/{booking}', [BookingsController::class, 'show'])->name('bookings.show');
        Route::patch('bookings/{booking}/status', [BookingsController::class, 'updateStatus'])
            ->name('bookings.status');
    });

    // Super admin and admin
    Route::middleware(['auth', 'role:super_admin,admin'])->group(function () {
        Route::patch('bookings/{booking}/technician', [BookingsController::class, 'assign'])
            ->name('bookings.technician');
        Route::patch('bookings/{booking}/notes', [BookingsController::class, 'updateNotes'])
            ->name('bookings.notes');

        Route::get('quotes', [QuoteRequestController::class, 'index'])->name('quotes.index');
        Route::get('quotes/{quote}', [QuoteRequestController::class, 'show'])->name('quotes.show');
        Route::patch('quotes/{quote}/status', [QuoteRequestController::class, 'updateStatus'])
            ->name('quotes.status');
        Route::patch('quotes/{quote}/notes', [QuoteRequestController::class, 'updateNotes'])
            ->name('quotes.notes');
        Route::get('quotes/{quote}/file', [QuoteRequestController::class, 'file'])->name('quotes.file');

        Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

        Route::resource('services', ServicesController::class)->except(['show']);
        Route::patch('services/{service}/toggle', [ServicesController::class, 'toggle'])
            ->name('services.toggle');

        Route::get('messages', [ContactMessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
        Route::patch('messages/{message}/unread', [ContactMessageController::class, 'markUnread'])
            ->name('messages.unread');

        Route::resource('categories', CategoriesController::class)->except(['show', 'destroy']);
        Route::resource('projects', ProjectsController::class)->except(['show', 'destroy']);

        Route::resource('testimonials', TestimonialController::class)->except(['show', 'destroy']);
        Route::patch('testimonials/{testimonial}/toggle', [TestimonialController::class, 'toggle'])
            ->name('testimonials.toggle');

        Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    });

    // Super admin only
    Route::middleware(['auth', 'role:super_admin'])->group(function () {
        Route::resource('users', UserController::class)->except(['show', 'destroy']);
        Route::patch('users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
    });
});