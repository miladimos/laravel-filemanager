<?php

use Illuminate\Support\Facades\Route;
use Miladimos\FileManager\Http\Controllers\FileGroupController;


Route::get('/file-group', [FileGroupController::class, 'index']);
Route::get('/file-group/{id}', [FileGroupController::class, 'show']);
Route::put('/file-group', [FileGroupController::class, 'update']);
Route::delete('/file-group/{id}', [FileGroupController::class, 'delete']);

// Route::get("/download/{file}", "DownloadController@download");


//    Route::get('browser/index', '\TalvBansal\MediaManager\Http\Controllers\MediaController@ls');
//
//    Route::post('browser/file', '\TalvBansal\MediaManager\Http\Controllers\MediaController@uploadFiles');
//    Route::delete('browser/file', '\TalvBansal\MediaManager\Http\Controllers\MediaController@deleteFile');
//    Route::post('browser/folder', '\TalvBansal\MediaManager\Http\Controllers\MediaController@createFolder');
//    Route::delete('browser/folder', '\TalvBansal\MediaManager\Http\Controllers\MediaController@deleteFolder');
//
//    Route::post('browser/rename', '\TalvBansal\MediaManager\Http\Controllers\MediaController@rename');
//    Route::get('browser/directories', '\TalvBansal\MediaManager\Http\Controllers\MediaController@allDirectories');
//    Route::post('browser/move', '\TalvBansal\MediaManager\Http\Controllers\MediaController@move');


    //Route::post('/upload', ['uses' => 'FileController@uploadFile'])->name('file.upload');
    //Route::post('/explorer/files', ['uses' => 'FileController@getUserFiles'])->name('explorer.files');
    //Route::post('/explorer/folders', ['uses' => 'FolderController@getUserFolders'])->name('explorer.folders');
    //Route::post('/explorer/folders/parent', ['uses' => 'FolderController@getParentFolderId'])->name('explorer.folder.parent');
    //Route::get('/explorer/files/download/{id?}', ['uses' => 'FileController@downloadFile'])->name('explorer.download');
    //Route::get('/explorer/files/delete/{id?}', ['uses' => 'FileController@deleteFile'])->name('explorer.delete');
    //Route::post('/explorer/folders/create', ['uses' => 'FolderController@createFolder'])->name('explorer.folder.create');
    //Route::post('/explorer/files/rename', ['uses' => 'FileController@renameFile'])->name('explorer.file.rename');
    //Route::post('/explorer/folders/rename', ['uses' => 'FolderController@renameFolder'])->name('explorer.folder.rename');
    //Route::post('/explorer/files/move', ['uses' => 'FileController@moveFile'])->name('explorer.file.move');
    //Route::post('/explorer/folders/get-breadcrumb', ['uses' => 'FolderController@getFolderBreadcrumb'])->name('explorer.folder.getBreadcrumb');

