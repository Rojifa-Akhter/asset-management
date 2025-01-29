<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MaintainanceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Organization\AssetController;
use App\Http\Controllers\Organization\FAQController;
use App\Http\Controllers\Organization\SettingController;
use App\Http\Controllers\SupportAgent\InspectionSheetController;
use App\Http\Controllers\User\AdminController;
use App\Http\Controllers\User\OrganizationController;
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
Route::middleware(['auth:api', 'super_admin'])->group(function () {

    //add and update organization
    Route::post('organization_add', [AdminController::class, 'addOrganization']);
    Route::post('organization_update/{id}', [AdminController::class, 'updateOrganization']);

    Route::post('third_party_add', [AdminController::class, 'addThirdParty']);
    Route::post('third_party_update/{id}', [AdminController::class, 'updateThirdParty']);

    //add and update location employee
    Route::post('employee_add', [AdminController::class, 'addEmployee']);
    Route::post('employee_update/{id}', [AdminController::class, 'updateEmployee']);

    //add and update support agent
    Route::post('agent_add', [AdminController::class, 'addAgent']);
    Route::post('agent_update/{id}', [AdminController::class, 'updateSAgent']);

    //add and update technician
    Route::post('add_technician', [AdminController::class, 'technicianAdd']);
    Route::post('update_technician/{id}', [AdminController::class, 'technicianUpdate']);

    Route::delete('delete_user/{id}', [AdminController::class, 'deleteUser']);
    Route::get('soft_delete_user', [AdminController::class, 'SoftDeletedUsers']);

    //assetlist
    Route::get('get_asset_list', [AssetController::class, 'assetListAdmin']);

     //setting
     Route::post('create_setting', [SettingController::class, 'createSetting']);
     Route::get('settings', [SettingController::class, 'listSetting']);

     //faq
     Route::post('create_faq', [FAQController::class, 'createFaq']);
     Route::post('update_faq/{id}', [FAQController::class, 'updateFaq']);
     Route::get('faq_list', [FAQController::class, 'listFaq']);
     Route::delete('delete_faq/{id}', [FAQController::class, 'deleteFaq']);


});
Route::middleware(['auth:api', 'organization'])->group(function () {


});

Route::middleware(['auth:api', 'support_agent'])->group(function () {
    //inspection sheet
    Route::post('create-inspection', action: [InspectionSheetController::class, 'createInspectionSheet']);
    Route::post('update-inspection/{id}', [InspectionSheetController::class, 'updateInspectionSheet']);
    Route::delete('delete-inspection/{id}', [InspectionSheetController::class, 'deleteInspectionSheet']);

    Route::get('inspection-list', [InspectionSheetController::class, 'InspectionSheetList']);
    Route::get('inspection-details/{id}', action: [InspectionSheetController::class, 'InspectionSheetDetails']);

});

Route::middleware(['auth:api', 'user'])->group(function () {

    //ticket
    Route::post('create-ticket', [TicketController::class, 'createTicket']);
    Route::get('ticket-list', [TicketController::class, 'ticketList']);
    Route::get('ticket-details/{id}', [TicketController::class, 'ticketDetails']);

});

Route::middleware(['auth:api', 'common'])->group(function () {

    //update ticket
    Route::post('update-ticket/{id}', [TicketController::class, 'updateTicket']);
    Route::get('ticket_details/{id}', [TicketController::class, 'ticketDetails']);
    Route::get('ticket_list', [TicketController::class, 'ticketList']);






    //message routes
    Route::post('send-message',[MessageController::class,'sendMessage']);
    Route::get('get-message',[MessageController::class,'getMessage']);
    Route::get('mark-read',[MessageController::class,'markRead']);
    Route::get('search-new-user',[MessageController::class,'searchNewUser']);
    Route::get('chat-list',[MessageController::class,'chatList']);
});
Route::middleware(['auth:api', 'creator'])->group(function () {

    //add and update location employee
    Route::post('location_employee_add', [OrganizationController::class, 'addLocationEmployee']);
    Route::post('location_employee_update/{id}', [OrganizationController::class, 'updateLocationEmployee']);

    //add and update support agent
    Route::post('support_agent_add', [OrganizationController::class, 'addSupportAgent']);
    Route::post('support_agent_update/{id}', [OrganizationController::class, 'updateSupportAgent']);

    //add and update technician
    Route::post('technician_add', [OrganizationController::class, 'addTechnician']);
    Route::post('technician_update/{id}', [OrganizationController::class, 'updateTechnician']);

    //just delete supportagent, location employee and technician
    Route::delete('delete_user/{id}', [OrganizationController::class, 'deleteUser']);
    Route::get('all_user', [AdminController::class, 'userList']);
    Route::get('user_details/{id}', [AdminController::class, 'userDetails']);
    Route::get('get_user_details/{id}', [OrganizationController::class, 'getuserDetails']);

    //asset route
    Route::post('create_asset', [AssetController::class, 'createAsset']);
    Route::post('update_asset/{id}', [AssetController::class, 'updateAsset']);
    Route::get('asset_list', [AssetController::class, 'assetList']);
    Route::get('asset_maturity/{id}', [AssetController::class, 'assetMaturity']);
    Route::get('asset_details/{id}', [AssetController::class, 'assetDetails']);
    Route::delete('delete_asset/{id}', [AssetController::class, 'deleteAsset']);

    Route::post('import_asset', [AssetController::class, 'importAssets']);
});

Route::middleware(['auth:api','super_admin.location_employee.organization'])->group(function(){
    Route::get('technician',[MaintainanceController::class,'technicianGet']);
    Route::get('asset',[MaintainanceController::class,'assetGet']);
});
