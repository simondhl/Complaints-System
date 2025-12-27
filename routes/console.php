<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Schedule::command('backup:run')
    ->dailyAt('14:53')->timezone('Asia/Damascus')
    ->onSuccess(function () {
        Log::info('Scheduled backup completed successfully');
    })
    ->onFailure(function () {
        Log::error('Scheduled backup failed');
    });
