<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Organization\AssetController;
use App\Http\Controllers\Organization\FAQController;
use App\Http\Controllers\Organization\SettingController;
use App\Http\Controllers\SupportAgent\InspectionSheetController;
use App\Http\Controllers\User\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('signup', [AuthController::class, 'signup']);
    Route::post('verify', [AuthController::class, 'verify']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('resend-otp', [AuthController::class, 'resendOtp']);
    Route::middleware('auth:api')->group(function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('profile-update', [AuthController::class, 'updateProfile']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
    });
    Route::post('logout', [AuthController::class, 'logout']);

});
Route::middleware(['auth:api', 'organization'])->group(function () {
    //asset
    Route::post('create-asset', [AssetController::class, 'createAsset']);
    Route::post('update-asset/{id}', [AssetController::class, 'updateAsset']);
    Route::get('asset-list', [AssetController::class, 'assetList']);
    Route::get('asset-details/{id}', [AssetController::class, 'assetDetails']);

    Route::delete('delete-asset/{id}', [AssetController::class, 'deleteAsset']);

    //setting
    Route::post('create-setting', [SettingController::class, 'createSetting']);
    Route::get('settings', [SettingController::class, 'listSetting']);

    //faq
    Route::post('create-faq', [FAQController::class, 'createFaq']);
    Route::post('update-faq/{id}', [FAQController::class, 'updateFaq']);
    Route::get('faq-list', [FAQController::class, 'listFaq']);
    Route::delete('delete-faq/{id}', [FAQController::class, 'deleteFaq']);
});

Route::middleware(['auth:api', 'user'])->group(function () {

    //ticket
    Route::post('create-ticket', [TicketController::class, 'createTicket']);
    Route::delete('delete-ticket/{id}', [TicketController::class, 'deleteTicket']);
    Route::get('ticket-list', [TicketController::class, 'ticketList']);
    Route::get('ticket-details/{id}', [TicketController::class, 'ticketDetails']);

});
Route::middleware(['auth:api', 'support_agent'])->group(function () {
    //inspection sheet
    Route::post('create-inspection', action: [InspectionSheetController::class, 'createInspectionSheet']);
    Route::post('update-inspection/{id}', [InspectionSheetController::class, 'updateInspectionSheet']);
    Route::delete('delete-inspection/{id}', [InspectionSheetController::class, 'deleteInspectionSheet']);

    Route::get('inspection-list', [InspectionSheetController::class, 'InspectionSheetList']);
    Route::get('inspection-details/{id}', action: [InspectionSheetController::class, 'InspectionSheetDetails']);

});
Route::middleware(['auth:api', 'common'])->group(function () {

    //update ticket
    Route::post('update-ticket/{id}', [TicketController::class, 'updateTicket']);
    Route::get('ticket-details/{id}', [TicketController::class, 'ticketDetails']);
    Route::get('ticket-list', [TicketController::class, 'ticketList']);

});
