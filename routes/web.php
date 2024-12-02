<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {

    return view('mails.reset-password', ['email' => 'example@gmail.com', 'code' => 456454]);
});

Route::get('/{slug}', function ($slug) {
    if ($slug == '/') {
        return view('welcome');
    } else {
        return view('welcome');
    }
});

Route::get('/', function () {
    return view('welcome');
});


