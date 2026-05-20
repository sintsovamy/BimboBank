<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\TransferController;
use Illuminate\Support\Facades\Route;

Route::get('statistics', [StatisticsController::class, 'statistics'])->name('statistics');

Route::get('account/{account}', [AccountController::class, 'item'])->name('item');

Route::post('transfer', [TransferController::class, 'transfer'])->name('transfer');

Route::get('transfer/recipient-user', [TransferController::class, 'getRecipientUser'])->name('recipient-user');

