<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Pet;
use App\Models\Service;

Route::view('/', 'welcome')->name('home');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('dashboard', '/GM_page1')->name('dashboard');
});

require __DIR__.'/settings.php';

Route::get('/GM_page1', function () {
    $monthlyBookings = collect(range(5, 0))->map(function ($monthsAgo) {
        $month = now()->startOfMonth()->subMonths($monthsAgo);
        $nextMonth = $month->copy()->addMonth();

        return [
            'label' => $month->format('M'),
            'count' => Booking::where('booking_date', '>=', $month->toDateString())
                ->where('booking_date', '<', $nextMonth->toDateString())
                ->count(),
        ];
    });

    $maxMonthlyBookings = max(1, $monthlyBookings->max('count'));

    $bookingStatuses = [
        'pending' => Booking::where('booking_status', 'pending')->count(),
        'confirmed' => Booking::where('booking_status', 'confirmed')->count(),
        'completed' => Booking::where('booking_status', 'completed')->count(),
    ];

    $totalBookings = array_sum($bookingStatuses);
    $pendingPercent = $totalBookings ? ($bookingStatuses['pending'] / $totalBookings) * 100 : 0;
    $confirmedPercent = $totalBookings ? ($bookingStatuses['confirmed'] / $totalBookings) * 100 : 0;
    $completedPercent = $totalBookings ? ($bookingStatuses['completed'] / $totalBookings) * 100 : 0;

    return view('GM_page1', [
        'customerCount' => Customer::count(),
        'petCount' => Pet::count(),
        'bookingCount' => Booking::count(),
        'revenue' => (float) DB::table('payments')
            ->where('payment_status', 'completed')
            ->sum('payment_amount'),
        'monthlyBookings' => $monthlyBookings,
        'maxMonthlyBookings' => $maxMonthlyBookings,
        'bookingStatuses' => $bookingStatuses,
        'totalBookings' => $totalBookings,
        'pendingPercent' => $pendingPercent,
        'confirmedPercent' => $confirmedPercent,
        'completedPercent' => $completedPercent,
        'topServices' => Service::withCount('bookings')
            ->where('service_status', 'active')
            ->orderByDesc('bookings_count')
            ->orderBy('service_name')
            ->take(5)
            ->get(),
    ]);
})->name('admin.home');


require __DIR__.'/bookings.php';
require __DIR__.'/customers.php';

Route::get('/services', [AdminController::class, 'services'])->name('services');
Route::get('/services/create', [AdminController::class, 'createService'])->name('createService');
Route::post('/services', [AdminController::class, 'storeService'])->name('storeService');
Route::get('/services/{service}/edit', [AdminController::class, 'editService'])->name('editService');
Route::put('/services/{service}', [AdminController::class, 'updateService'])->name('updateService');
Route::delete('/services/{service}', [AdminController::class, 'deleteService'])->name('deleteService');
Route::get('/staff', [AdminController::class, 'staff'])->name('staff');
Route::post('/staff', [AdminController::class, 'storeStaff'])->name('storeStaff');
Route::post('/staff/schedules', [AdminController::class, 'storeSchedule'])->name('storeSchedule');
Route::post('/staff/closed-days', [AdminController::class, 'toggleStoreClosure'])->name('toggleStoreClosure');
Route::get('/admin/payments', [AdminController::class, 'payments'])->name('payments');
