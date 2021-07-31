<?php

use Illuminate\Support\Facades\Route;
use Miladimos\FileManager\Http\Controllers\DirectoryController;
use Miladimos\FileManager\Http\Controllers\DownloadController;
use Miladimos\FileManager\Http\Controllers\FileController;
use Miladimos\FileManager\Http\Controllers\FileGroupController;
use Miladimos\FileManager\Http\Controllers\UploadController;

Route::get('test', function () {
//    dd("d");
    return view("filemanager::test");
});

Route::group(['as' => 'directories.'], function () {
//    Route::get('directories', [DirectoryController::class, 'allDirectory'])->name('index');
    Route::post('directories', [DirectoryController::class, 'createDirectory'])->name('create');
    Route::delete('directories', [DirectoryController::class, 'deleteDirectories'])->name('delete');
    Route::post('directories/rename', [DirectoryController::class, 'renameDirectory'])->name('rename');
});

Route::group(['as' => 'files.'], function () {
    Route::delete('files', [FileController::class, 'deleteFile'])->name('delete');
    Route::post('files/rename', [FileController::class, 'renameFile'])->name('rename');
    Route::post('files/move', [FileController::class, 'moveFile'])->name('move');
});

Route::group(['as' => 'uploads.'], function () {
    Route::post('upload', [UploadController::class, 'uploadFile'])->name('upload');
});

Route::group(['as' => 'downloads.'], function () {
    Route::post('download', [DownloadController::class, 'download'])->name('download');
});

Route::group(['as' => 'filegroups.'], function () {
    Route::get('filegroups', [FileGroupController::class, 'index'])->name('index');
    Route::post('filegroups', [FileGroupController::class, 'store'])->name('store');
    Route::put('filegroups/{filegroup}/update', [FileGroupController::class, 'update'])->name('update');
    Route::delete('filegroups/{filegroup}', [FileGroupController::class, 'delete'])->name('delete');
});


//download/$file->uuid?mac=$hash&t=$timestamp
Route::get("download/{file}?max={hash}&et={timestamp}", [DownloadController::class, 'download'])->name('download');

//Route::post('/browser/folders/parent', ['uses' => 'FolderController@getParentFolderId'])->name('browser.folder.parent');
//Route::post('/browser/folders/get-breadcrumb', ['uses' => 'FolderController@getFolderBreadcrumb'])->name('browser.folder.getBreadcrumb');

