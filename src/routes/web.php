<?php

use AstridTechnology\LaravelBackup\Controllers;
use Illuminate\Support\Facades\Route;
use AstridTechnology\LaravelBackup\Controllers\BackupServiceController;

Route::middleware(['web', 'auth', 'can:access-backup'])->group(function () {
    Route::get('backup-panel', BackupServiceController::class);
    Route::get('/download/{filename}', [BackupServiceController::class, 'downloadRouteFile'])->name('download.file');
    Route::post('/delete', [BackupServiceController::class, 'deleteRouteFile'])->name('delete.file');
    Route::get('create-backup', [BackupServiceController::class, 'createBackup'])->name('create-backup');
    Route::get('/backup-status', [BackupServiceController::class, 'getBackupStatus'])->name('backup-status');
});
