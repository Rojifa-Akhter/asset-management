<?php

use App\Http\Controllers\SupportAgent\TicketTecController;
use App\Http\Controllers\Technician\QuatationTecController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Organization\AssetController;
use App\Http\Controllers\Organization\InsSheetController;
use App\Http\Controllers\Role\UserController;
use App\Http\Controllers\SupportAgent\JobController;
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
    //asset
    Route::get('create-asset',[AssetController::class,'createAsset']);
    Route::post('update-asset/{id}', [AssetController::class, 'updateAsset']);
    Route::get('asset-list', [AssetController::class, 'assetList']);
    Route::get('asset-details/{id}', [AssetController::class, 'assetDetails']);

    Route::delete('delete-asset/{id}', [AssetController::class, 'deleteAsset']);


 });
 Route::middleware(['auth:api', 'Technician'])->group(function () {

    //quatation
    Route::post('create-quatation',[QuatationTecController::class,'createQuatation']);
    Route::post('update-quatation/{id}',[QuatationTecController::class,'updateQuatation']);
    Route::delete('delete-quatation/{id}',[QuatationTecController::class,'deleteQuatation']);
    Route::get('quatation-list',[QuatationTecController::class,'quatationList']);
    Route::get('quatation-details/{id}', [QuatationTecController::class, 'quatationDetails']);


 });
 Route::middleware(['auth:api', 'Support Agent'])->group(function () {

    //ticket
    Route::post('create-ticket', [TicketTecController::class,'createTicket']);
    Route::post('update-ticket/{id}', [TicketTecController::class, 'updateTicket']);
    Route::delete('delete-ticket/{id}', [TicketTecController::class, 'deleteTicket']);
    Route::get('ticket-list', [TicketTecController::class, 'ticketList']);
    Route::get('ticket-details/{id}', [TicketTecController::class, 'ticketDetails']);

        //inspection sheet
    Route::post('create-inspection',action: [InsSheetController::class,'createSheet']);
    Route::post('update-inspection/{id}',[InsSheetController::class,'updateInspectionSheet']);
    Route::delete('delete-inspection/{id}', [InsSheetController::class, 'deleteInspectionSheet']);

    Route::get('inspection-list', [InsSheetController::class, 'InspectionSheetList']);
    Route::get('inspection-details/{id}', [InsSheetController::class, 'InspectionSheetDetails']);

     //job card

     Route::post('create-jobcard',[JobController::class,'createJob']);
     Route::post('update-jobcard/{id}',[JobController::class,'updateJob']);
     Route::delete('delete-jobcard/{id}',[JobController::class,'deleteJob']);
     Route::get('list-jobcard',[JobController::class,'listJob']);
     Route::get('details-jobcard/{id}',[JobController::class,'detailsJob']);

 });
 Route::middleware(['auth:api', 'User'])->group(function () {
    // Quotation route
    Route::get('quotation-view/{id}', [UserController::class, 'getQuatation']);
    // Route::post('order', [OrderController::class, 'createOrder']);

});


