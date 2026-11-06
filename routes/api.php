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

/*
|--------------------------------------------------------------------------
| Public Portal API Routes (Konsumsi Website Publik Golang)
|--------------------------------------------------------------------------
| Read-only endpoints untuk artikel solusi & manual book berstatus publik
*/
Route::prefix('bank-solusi')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\BankSolusiApiController::class, 'index']);
    Route::get('/systems', [\App\Http\Controllers\Api\BankSolusiApiController::class, 'systems']);
    Route::get('/{id}', [\App\Http\Controllers\Api\BankSolusiApiController::class, 'show']);
});

Route::get('/manual-books', [\App\Http\Controllers\Api\BankSolusiApiController::class, 'manualBooks']);
