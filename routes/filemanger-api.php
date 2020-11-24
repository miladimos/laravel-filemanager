<?php

use Illuminate\Support\Facades\Route;
use Miladimos\FileManager\Http\Controllers\FileGroupController;


Route::get('/file-group', [FileGroupController::class, 'index']);
Route::get('/file-group/{id}', [FileGroupController::class, 'show']);
Route::put('/file-group', [FileGroupController::class, 'update']);
Route::delete('/file-group/{id}', [FileGroupController::class, 'delete']);
