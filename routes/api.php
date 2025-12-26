<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/Register', [AuthController::class, 'user_Register']);
Route::get('/SendEmail/{email}', [AuthController::class, 'send_email']);
Route::post('/Verification/{email}', [AuthController::class, 'verification']);
Route::post('/ReSetPassword', [AuthController::class, 'reset_password']);

Route::post('/Login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

  Route::get('/Logout', [AuthController::class, 'logout']);
  // Route::post('/Refresh', [AuthController::class, 'refresh']);
  Route::get('/GovernmentSectors', [ComplaintController::class, 'get_all_government_sectors']);

  Route::get('/Complaint/{id}', [ComplaintController::class, 'get_complaint_details']);
  Route::get('/documents/download/{id}', [ComplaintController::class, 'download_document']);

  // Citizen Routes
  Route::middleware('role:citizen')->group(function () {

    Route::post('/Complaints', [ComplaintController::class, 'store']);
    Route::post('/Complaints/update', [ComplaintController::class, 'update_by_citizen']);
    Route::delete('/Complaints/delete/{id}', [ComplaintController::class, 'delete_by_citizen']);
    Route::get('/Complaints/Citizen', [ComplaintController::class, 'get_for_citizen']);
    Route::post('/Complaints/Search', [ComplaintController::class, 'search_complaint_number']);

    Route::get('/Notifications', [NotificationController::class, 'get_all_notifications']);
    Route::get('/NewNotifications/Count', [NotificationController::class, 'new_notifications_count']);
    Route::post('/SaveDeviceToken', [NotificationController::class, 'save_device_token']);
  });

  // Employee Routes
  Route::middleware('role:government_emplyee')->group(function () {

      Route::get('/Complaints/GovernmentSector', [ComplaintController::class, 'get_for_government_sector']);
      Route::post('/Complaints/UpdateStatus', [ComplaintController::class, 'update_complaint_status']);
      Route::post('/Notices', [ComplaintController::class, 'add_notice']);
  });

  // Admin Routes
  Route::middleware('role:admin')->group(function () {
      Route::get('/Complaints/GovernmentSector/{id}', [ComplaintController::class, 'get_by_government_sector']);
      Route::post('/CreateEmployee', [UserController::class, 'create_employee']);
      Route::get('/Users', [UserController::class, 'get_all_users']);
      Route::get('/Employees', [UserController::class, 'get_all_employees']);

      Route::post('/GetRecordsByDate', [ComplaintController::class, 'get_records_by_date']);
      Route::post('/ExportReportByDate', [ComplaintController::class, 'get_report_by_date']);
  });
  
});
