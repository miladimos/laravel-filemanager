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
    Route::post('/directories/{directory}/parent', [DirectoryController::class, 'getParentDirectory'])->name('parent');

    Route::post('directories', [DirectoryController::class, 'createDirectory'])->name('create');
    Route::delete('directories/{directory}', [DirectoryController::class, 'deleteDirectories'])->name('delete');
    Route::post('directories/{directory}/rename', [DirectoryController::class, 'renameDirectory'])->name('rename');
});

Route::group(['as' => 'files.'], function () {
    Route::delete('files/{file}', [FileController::class, 'deleteFile'])->name('delete');
    Route::post('files/{file}/rename', [FileController::class, 'renameFile'])->name('rename');
    Route::post('files/{file}/{directory}/move', [FileController::class, 'moveFile'])->name('move');
});

Route::group(['as' => 'uploads.'], function () {
    Route::post('upload', [UploadController::class, 'upload'])->name('upload');
});

Route::group(['as' => 'downloads.'], function () {
    //download/$file->uuid?mac=$hash&t=$timestamp
    Route::get("download/{file}?max={hash}&et={timestamp}", [DownloadController::class, 'download'])->name('download');
});

Route::group(['as' => 'filegroups.'], function () {
    Route::get('filegroups', [FileGroupController::class, 'index'])->name('index');
    Route::post('filegroups', [FileGroupController::class, 'store'])->name('store');
    Route::put('filegroups/{filegroup}/update', [FileGroupController::class, 'update'])->name('update');
    Route::delete('filegroups/{filegroup}', [FileGroupController::class, 'delete'])->name('delete');
});
