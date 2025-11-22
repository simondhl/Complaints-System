<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use Illuminate\Support\Facades\Route;

Route::post('/Register', [AuthController::class, 'user_Register']);
Route::get('/SendEmail/{email}', [AuthController::class, 'send_email']);
Route::post('/Verification/{email}', [AuthController::class, 'verification']);
Route::post('/ReSetPassword', [AuthController::class, 'reset_password']);

Route::post('/Login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

  Route::get('/Logout', [AuthController::class, 'logout']);
  Route::post('/Refresh', [AuthController::class, 'refresh']);
  Route::get('/GovernmentSectors', [ComplaintController::class, 'get_all_government_sectors']);

  Route::get('/Complaint/{id}', [ComplaintController::class, 'get_complaint_details']);
  Route::get('/documents/download/{id}', [ComplaintController::class, 'download_document']);

  // Citizen Routes
  Route::middleware('role:citizen')->group(function () {

    Route::post('/Complaints', [ComplaintController::class, 'store']);
  });

  // Employee Routes
  Route::middleware('role:government_emplyee')->group(function () {

      Route::get('/Complaints/GovernmentSector', [ComplaintController::class, 'get_for_government_sector']);
      Route::put('/Complaints/UpdateStatus', [ComplaintController::class, 'update_complaint_status']);
      Route::post('/Notices', [ComplaintController::class, 'add_notice']);
  });

  // Admin Routes
  Route::middleware('role:admin')->group(function () {

  });
  
});
