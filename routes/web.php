<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BloodSugarReadingController;
use App\Http\Controllers\DailySummaryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExerciseLogController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FoodLogController;
use App\Http\Controllers\Hba1cReadingController;
use App\Http\Controllers\HealthProfileController;
use App\Http\Controllers\InsulinLogController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MealPlanController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicShareController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShareLinkController;
use App\Http\Controllers\WaterLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));
Route::view('/mobile', 'mobile')->name('mobile');
Route::get('/share/{token}', [PublicShareController::class, 'show'])->name('share.public');
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/daily-summary', [DailySummaryController::class, 'index'])->name('daily-summary');

    Route::get('/health-profile', [HealthProfileController::class, 'edit'])->name('health-profile.edit');
    Route::put('/health-profile', [HealthProfileController::class, 'update'])->name('health-profile.update');

    Route::resource('blood-sugar', BloodSugarReadingController::class)
        ->except(['show', 'edit', 'update'])
        ->parameters(['blood-sugar' => 'bloodSugar']);

    Route::resource('meals', MealPlanController::class)->only(['index', 'show']);
    Route::resource('appointments', AppointmentController::class)->except(['show']);
    Route::resource('hba1c', Hba1cReadingController::class)->except(['show', 'edit', 'update']);
    Route::resource('medications', MedicationController::class)->except(['show']);

    Route::resource('insulin', InsulinLogController::class)->except(['show', 'edit', 'update']);
    Route::resource('food', FoodLogController::class)->except(['show', 'edit', 'update']);
    Route::resource('exercise', ExerciseLogController::class)->except(['show', 'edit', 'update']);
    Route::get('/water', [WaterLogController::class, 'index'])->name('water.index');
    Route::post('/water', [WaterLogController::class, 'store'])->name('water.store');
    Route::delete('/water/{water}', [WaterLogController::class, 'destroy'])->name('water.destroy');

    Route::get('/share', [ShareLinkController::class, 'index'])->name('share.index');
    Route::post('/share', [ShareLinkController::class, 'store'])->name('share.store');
    Route::delete('/share/{share}', [ShareLinkController::class, 'destroy'])->name('share.destroy');

    Route::get('/report/pdf', [ReportController::class, 'pdf'])->name('report.pdf');
    Route::get('/export/json', [ExportController::class, 'json'])->name('export.json');
    Route::get('/export/csv', [ExportController::class, 'csv'])->name('export.csv');

    Route::get('/push/vapid-key', [PushSubscriptionController::class, 'vapidKey'])->name('push.vapid');
    Route::post('/push/subscribe', [PushSubscriptionController::class, 'store'])->name('push.subscribe');
    Route::delete('/push/unsubscribe', [PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
