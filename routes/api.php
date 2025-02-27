<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MaintainanceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Organization\AssetController;
use App\Http\Controllers\Organization\FAQController;
use App\Http\Controllers\Organization\SettingController;
use App\Http\Controllers\Statistic\LocationEmployee;
use App\Http\Controllers\Statistic\Organization;
use App\Http\Controllers\Statistic\SuperAdmin;
use App\Http\Controllers\Statistic\SupportAgent;
use App\Http\Controllers\SupportAgent\InspectionSheetController;
use App\Http\Controllers\SupportAgent\JobCardController;
use App\Http\Controllers\User\AdminController;
use App\Http\Controllers\User\LocationController;
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
    Route::post('social-login', [AuthController::class, 'socialLogin']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('resend-otp', [AuthController::class, 'resendOtp']);
    Route::middleware('auth:api')->group(function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('profile-update', [AuthController::class, 'updateProfile']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        Route::post('location', [LocationController::class, 'Address']);
        Route::get('get-address/{id}', [LocationController::class, 'getAddress']);
    });
    Route::post('logout', [AuthController::class, 'logout']);

});
Route::middleware(['auth:api', 'super_admin'])->group(function () {

    //statistics
    Route::get('super-admin-overview', [SuperAdmin::class, 'overview']);
    Route::get('super-admin-chart', [SuperAdmin::class, 'chartsuperAdmin']);

    Route::get('ticket-activity-super', [SuperAdmin::class, 'activityTicket']);
    //inspection sheet
    Route::get('inspection-statistics', [SuperAdmin::class, 'statisticsInspectionSheet']);
    //job card
    Route::get('card-statistics', [SuperAdmin::class, 'statisticsJobCard']);

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

Route::middleware(['auth:api', 'user'])->group(function () {

    //ticket
    Route::post('create_ticket', [TicketController::class, 'createTicket']);
    Route::get('ticket_list', [TicketController::class, 'ticketList']);
    Route::get('ticket_details/{id}', [TicketController::class, 'ticketDetails']);
    Route::get('ticket_delete/{id}', [TicketController::class, 'deleteTicket']);

});

Route::middleware(['auth:api', 'common'])->group(function () {

    //update ticket
    Route::post('update_ticket/{id}', [TicketController::class, 'updateTicket']);
    Route::get('ticket_details/{id}', [TicketController::class, 'ticketDetails']);
    Route::get('ticket_list', [TicketController::class, 'ticketList']);

    //message routes
    Route::post('send-message', [MessageController::class, 'sendMessage']);
    Route::get('get-message', [MessageController::class, 'getMessage']);
    Route::get('mark-read', [MessageController::class, 'markRead']);
    Route::get('search-new-user', [MessageController::class, 'searchNewUser']);
    Route::get('chat-list', [MessageController::class, 'chatList']);
});
Route::middleware(['auth:api', 'super_admin.third_party.organization'])->group(function () {

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

Route::middleware(['auth:api', 'super_admin.location_employee.organization'])->group(function () {
    Route::get('technician', [MaintainanceController::class, 'technicianGet']);
    Route::get('asset', [MaintainanceController::class, 'assetGet']);
    Route::get('maintainance', [MaintainanceController::class, 'maintainanceGet']);


});

Route::middleware(['auth:api', 'location_employee'])->group(function () {
    Route::post('set-reminder', [MaintainanceController::class, 'setReminder']);
    Route::get('get-reminder', [MaintainanceController::class, 'getReminder']);
    Route::post('update-maintainance/{id}', [MaintainanceController::class, 'updateStatus']);
    Route::get('location-employee-dashboard', [LocationEmployee::class, 'dashboard']);
});

Route::middleware(['auth:api', 'support_agent'])->group(function () {

    //get ticket details
    Route::get('get_ticket_details/{id}', [TicketController::class, 'getTicketDetails']);
    //inspection sheet
    Route::post('create_inspection_sheet', [InspectionSheetController::class, 'createInspectionSheet']);
    //job card
    Route::post('create_job_card', [JobCardController::class, 'createJobCard']);
});
Route::middleware(['auth:api', 'support_agent.location_employee.technician.third_party'])->group(function () {

    //get ticket details
    Route::get('get_ticket_details/{id}', [TicketController::class, 'getTicketDetails']);
    //inspection sheet
    Route::post('update_inspection/{id}', [InspectionSheetController::class, 'updateInspectionSheet']);
    Route::delete('delete_inspection/{id}', [InspectionSheetController::class, 'deleteInspectionSheet']);

    Route::get('inspection_list', [InspectionSheetController::class, 'InspectionSheetList']);
    Route::get('inspection_details/{id}', [InspectionSheetController::class, 'InspectionSheetDetails']);

    //job card update list details
    Route::post('update_card/{id}', [JobCardController::class, 'updateJobCard']);
    Route::delete('delete_card/{id}', [JobCardController::class, 'deleteJobCard']);

    Route::get('card_list', [JobCardController::class, 'JobCardList']);
    Route::get('card_details/{id}', [JobCardController::class, 'detailsJobCard']);

    //get notification when new ticket has been created
    Route::get('get_notifications', [TicketController::class, 'getNotifications']);
    Route::get('read_notifications/{notificationId}', [TicketController::class, 'markNotification']);
    Route::get('read_all_notifications', [TicketController::class, 'markAllNotification']);

    //notification
    Route::get('notifications', [InspectionSheetController::class, 'getAllNotifications']);
    Route::get('notification_read/{notificationId}', [InspectionSheetController::class, 'markNotification']);
    Route::get('all_notifications_read', [InspectionSheetController::class, 'markAllNotification']);

    //notification
    Route::get('notification_get', [JobCardController::class, 'notifications']);
    Route::get('notification_read_one/{notificationId}', [JobCardController::class, 'notificationMark']);
    Route::get('notifications_read_all', [JobCardController::class, 'allNotificationMark']);
});

Route::middleware(['auth:api', 'organization'])->group(function () {
    Route::get('organization-dashboard', [Organization::class, 'dashboard']);
    Route::get('organization-ticket-activity', [Organization::class, 'ticketActivity']);
    Route::get('inspaction-sheet-overview', [Organization::class, 'inspactionSheetOverview']);
    Route::get('job-card-overview', [Organization::class, 'jobCardOverview']);

});
Route::middleware(['auth:api', 'support_agent'])->group(function () {
    Route::get('support-agent-dashboard', [SupportAgent::class, 'chartSupportAgent']);
    Route::get('ticket-activity', [SupportAgent::class, 'activityTicket']);
    //inspection sheet
    Route::get('inspection-sheet-statistics', [SupportAgent::class, 'statisticsInspectionSheet']);
    //job card
    Route::get('job-card-statistics', [SupportAgent::class, 'statisticsJobCard']);
});
Route::middleware(['auth:api', 'location_employee'])->group(function () {
    Route::get('location-employee-dashboard', [LocationEmployee::class, 'dashboardLocationEmployee']);

});

