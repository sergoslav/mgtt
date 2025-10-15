<?php

use App\Http\Controllers\UploaderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::post('/upload', UploaderController::class)->name('upload');;
