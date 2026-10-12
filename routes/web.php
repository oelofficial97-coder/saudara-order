<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');
Route::view('/pre-order', 'pages.preorder')->name('preorder');
Route::view('/cek-pesanan', 'pages.tracking')->name('tracking');
