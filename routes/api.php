<?php

use App\Http\Controllers\Api\AppointmentController as ApiAppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BloodSugarController as ApiBloodSugarController;
use App\Http\Controllers\Api\DailySummaryController;
use App\Http\Controllers\Api\ExerciseLogController as ApiExerciseLogController;
use App\Http\Controllers\Api\Hba1cController;
use App\Http\Controllers\Api\HealthProfileController as ApiHealthProfileController;
use App\Http\Controllers\Api\InsulinLogController as ApiInsulinLogController;
use App\Http\Controllers\Api\MealPlanController as ApiMealPlanController;
use App\Http\Controllers\Api\MedicationController as ApiMedicationController;
use App\Http\Controllers\Api\WaterLogController as ApiWaterLogController;
use App\Http\Controllers\ExportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/daily-summary', [DailySummaryController::class, 'today']);
    Route::get('/alerts', [DailySummaryController::class, 'alerts']);
    Route::get('/health-profile', [ApiHealthProfileController::class, 'show']);
    Route::put('/health-profile', [ApiHealthProfileController::class, 'update']);

    Route::get('blood-sugar/chart/weekly', [ApiBloodSugarController::class, 'weeklyChart']);
    Route::apiResource('blood-sugar', ApiBloodSugarController::class)
        ->parameters(['blood-sugar' => 'bloodSugar']);

    Route::get('meals/today', [ApiMealPlanController::class, 'today']);
    Route::apiResource('meals', ApiMealPlanController::class)->only(['index', 'show']);

    Route::get('appointments/upcoming', [ApiAppointmentController::class, 'upcoming']);
    Route::apiResource('appointments', ApiAppointmentController::class);

    Route::apiResource('hba1c', Hba1cController::class)->only(['index', 'store', 'destroy']);
    Route::get('medications/today', [ApiMedicationController::class, 'today']);
    Route::apiResource('medications', ApiMedicationController::class)->only(['index', 'store']);

    Route::apiResource('insulin', ApiInsulinLogController::class)->only(['index', 'store', 'destroy']);
    Route::get('exercise/today', [ApiExerciseLogController::class, 'today']);
    Route::apiResource('exercise', ApiExerciseLogController::class)->only(['index', 'store', 'destroy']);
    Route::get('water/today', [ApiWaterLogController::class, 'today']);
    Route::apiResource('water', ApiWaterLogController::class)->only(['index', 'store', 'destroy']);

    Route::get('/export/json', [ExportController::class, 'json']);
    Route::get('/export/csv', [ExportController::class, 'csv']);
});
