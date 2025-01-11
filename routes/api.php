<?php

use App\Http\Controllers\Technician\QuatationTecController;
use Illuminate\Http\Request;
use App\Models\InspectionSheet;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Organization\TicketController;
use App\Http\Controllers\Organization\InspectinSheetController;
use App\Http\Controllers\Role\UserController;
use App\Http\Controllers\Technician\QuatationController;

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
    Route::middleware('auth:api')->group(function(){
        Route::get('profile',[AuthController::class, 'profile']);
        Route::post('profile-update', [AuthController::class, 'updateProfile']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
    });
    Route::post('logout', [AuthController::class, 'logout']);

});
Route::middleware(['auth:api', 'Organization'])->group(function () {
    //ticket
    Route::post('create-ticket', [TicketController::class, 'createTicket']);
    Route::post('update-ticket/{id}', [TicketController::class, 'updateTicket']);
    Route::delete('delete-ticket/{id}', [TicketController::class, 'deleteTicket']);
    Route::get('ticket-list', [TicketController::class, 'ticketList']);
    Route::get('ticket-details/{id}', [TicketController::class, 'ticketDetails']);

    //inspection sheet
    Route::post('create-inspection',[InspectinSheetController::class,'createSheet']);
    Route::post('update-inspection/{id}',[InspectinSheetController::class,'updateInspectionSheet']);
    Route::delete('delete-inspection/{id}', [InspectinSheetController::class, 'deleteInspectionSheet']);

    Route::get('inspection-list', [InspectinSheetController::class, 'InspectionSheetList']);
    Route::get('inspection-details/{id}', [InspectinSheetController::class, 'InspectionSheetDetails']);


 });
 Route::middleware(['auth:api', 'Technician'])->group(function () {

    //quatation
    Route::post('create-quatation',[QuatationTecController::class,'createQuatation']);
    Route::post('update-quatation/{id}',[QuatationTecController::class,'updateQuatation']);
    Route::delete('delete-quatation/{id}',[QuatationTecController::class,'deleteQuatation']);
    Route::get('quatation-list',[QuatationTecController::class,'quatationList']);
    Route::get('quatation-details/{id}', [QuatationTecController::class, 'quatationDetails']);


 });
 Route::middleware(['auth:api', 'User'])->group(function () {
    // Quotation route
    Route::get('quotation-view/{id}', [UserController::class, 'getQuatation']);
});


