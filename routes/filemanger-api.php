<?php

use Illuminate\Support\Facades\Route;
use Miladimos\FileManager\Http\Controllers\FileGroupController;


Route::get('/file-group', [FileGroupController::class, 'index']);
Route::get('/file-group/{id}', [FileGroupController::class, 'show']);
Route::put('/file-group', [FileGroupController::class, 'update']);
Route::delete('/file-group/{id}', [FileGroupController::class, 'delete']);

Route::group(['prefix' => config('media-manager.routes.prefix')], function () {
    Route::get('browser/index', '\TalvBansal\MediaManager\Http\Controllers\MediaController@ls');

    Route::post('browser/file', '\TalvBansal\MediaManager\Http\Controllers\MediaController@uploadFiles');
    Route::delete('browser/file', '\TalvBansal\MediaManager\Http\Controllers\MediaController@deleteFile');
    Route::post('browser/folder', '\TalvBansal\MediaManager\Http\Controllers\MediaController@createFolder');
    Route::delete('browser/folder', '\TalvBansal\MediaManager\Http\Controllers\MediaController@deleteFolder');

    Route::post('browser/rename', '\TalvBansal\MediaManager\Http\Controllers\MediaController@rename');
    Route::get('browser/directories', '\TalvBansal\MediaManager\Http\Controllers\MediaController@allDirectories');
    Route::post('browser/move', '\TalvBansal\MediaManager\Http\Controllers\MediaController@move');
});

