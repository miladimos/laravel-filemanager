<?php

use Illuminate\Support\Facades\Route;
use Miladimos\FileManager\Http\Controllers\BrowseController;
use Miladimos\FileManager\Http\Controllers\FileGroupController;

// explorer
//Route::get('browser/index', [BrowseController::class, 'index']);
//Route::post('browser/file', [BrowseController::class, 'uploadFiles']);
//Route::delete('browser/file', [BrowseController::class, 'deleteFile']);
//Route::post('browser/folder', [BrowseController::class, 'createFolder']);
//Route::delete('browser/folder', [BrowseController::class, 'deleteFolder']);
//
//Route::post('browser/rename', [BrowseController::class, 'rename']);
//Route::get('browser/directories', [BrowseController::class, 'allDirectories']);
//Route::post('browser/move', [BrowseController::class, 'move']);

Route::get('/file-group', [FileGroupController::class, 'index']);
Route::get('/file-group/{uuid}', [FileGroupController::class, 'show']);
Route::put('/file-group', [FileGroupController::class, 'update']);
Route::delete('/file-group/{uuid}', [FileGroupController::class, 'delete']);

// Route::get("/download/{file}", "DownloadController@download");


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

