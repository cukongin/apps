<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// --- Data Synchronization Routes (Protected by Sync Token) ---
Route::middleware(\App\Http\Middleware\EnsureSyncToken::class)->prefix('sync')->group(function () {
    Route::get('/master-data', [\App\Http\Controllers\Api\SyncController::class, 'pullMasterData']);
    Route::get('/academic-data', [\App\Http\Controllers\Api\SyncController::class, 'pullAcademicData']);
    Route::get('/finance-data', [\App\Http\Controllers\Api\SyncController::class, 'pullFinanceData']);
    Route::post('/finance-push', [\App\Http\Controllers\Api\SyncController::class, 'receiveFinancePush']);
});
