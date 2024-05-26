<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Membuat default route menjadi mengarah ke /admin
Route::redirect('/', '/admin');