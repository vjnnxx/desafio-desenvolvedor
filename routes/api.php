<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/search', [FileController::class, 'search'])->name('file.search');
Route::get('/search-content', [FileController::class, 'searchContent'])->name('file.search.content');
Route::post('/upload', [FileController::class, 'upload'])->name('file.upload');

