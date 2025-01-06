<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Organization\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('signup', [AuthController::class, 'signup']);
    Route::post('verify', [AuthController::class, 'verify']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
    Route::post('/profile-update', [AuthController::class, 'updateProfile'])->middleware('auth:api');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('auth:api');
    Route::post('logout', [AuthController::class, 'logout']);

});
Route::middleware(['auth:api', 'Organization'])->group(function () {
    Route::post('/create-ticket', [TicketController::class, 'createTicket']);
    Route::post('/update-ticket/{id}', [TicketController::class, 'updateTicket']);
    Route::delete('/delete-ticket/{id}', [TicketController::class, 'deleteTicket']);

 });
