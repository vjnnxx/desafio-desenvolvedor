<?php

use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

Route::get('/search', [FileController::class, 'search'])->name('file.search');
Route::post('/upload', [FileController::class, 'upload'])->name('file.upload');
