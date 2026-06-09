<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\TransferController;
use Illuminate\Support\Facades\Route;

Route::get('statistics', StatisticsController::class)->name('statistics');

Route::get('account/{account}', AccountController::class)->name('item');

Route::post('transfer', TransferController::class)->name('transfer');

Route::post('message', MessageController::class)->name('send');

