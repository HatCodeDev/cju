<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrintCredentialController;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/print/credentials', PrintCredentialController::class)
    ->middleware('auth') // Asegurar protección
    ->name('print.credentials');
