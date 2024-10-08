<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\SettingController;

Route::get('/', function () {
    return redirect()->route('file.index');
});

Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
Route::post('/setting/store', [SettingController::class, 'store'])->name('setting.store');
Route::get('/setting/folder', [SettingController::class, 'folder'])->name('setting.folder');

Route::get('/sync', [SyncController::class, 'index'])->name('sync.index');
Route::post('/sync/sync', [SyncController::class, 'store'])->name('sync.sync');
Route::get('/sync/finish', [SyncController::class, 'finish'])->name('sync.finish');

Route::get('/file', [FileController::class, 'index'])->name('file.index');
Route::get('/file/download', [FileController::class, 'download'])->name('file.download');
Route::get('/file/view', [FileController::class, 'view'])->name('file.view');
