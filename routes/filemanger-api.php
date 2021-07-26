<?php

use Illuminate\Support\Facades\Route;
use Miladimos\FileManager\Http\Controllers\DirectoryController;
use Miladimos\FileManager\Http\Controllers\DownloadController;
use Miladimos\FileManager\Http\Controllers\FileController;
use Miladimos\FileManager\Http\Controllers\FileGroupController;


Route::apiResources([
    'file-groups' => FileGroupController::class,
]);

Route::group(['as' => 'directories.'], function () {
    Route::post('directories', [DirectoryController::class, 'createDirectory'])->name('create');
    Route::delete('directories', [DirectoryController::class, 'deleteDirectories'])->name('delete');
    Route::post('directories/rename', [DirectoryController::class, 'renameDirectory'])->name('rename');
});

Route::group(['as' => 'files.'], function () {
    Route::delete('files', [FileController::class, 'deleteFile'])->name('delete');
    Route::post('files/rename', [FileController::class, 'renameFile'])->name('rename');
    Route::post('files/move', [FileController::class, 'moveFile'])->name('move');
});

Route::get("download/{file}", [DownloadController::class, 'download']);

//Route::post('/browser/folders/parent', ['uses' => 'FolderController@getParentFolderId'])->name('browser.folder.parent');
//Route::post('/browser/folders/get-breadcrumb', ['uses' => 'FolderController@getFolderBreadcrumb'])->name('browser.folder.getBreadcrumb');

