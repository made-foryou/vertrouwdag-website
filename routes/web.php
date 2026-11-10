<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');
Route::view('/diensten', 'pages.diensten')->name('diensten');
Route::view('/werkwijze', 'pages.werkwijze')->name('werkwijze');
Route::view('/over-mij', 'pages.over-mij')->name('over-mij');
Route::view('/blog', 'pages.blog')->name('blog');
Route::view('/blog/archief', 'pages.archief')->name('blog.archief');
Route::view('/blog/wanneer-weddingplanner-inschakelen', 'pages.wanneer-weddingplanner-inschakelen')->name('blog.artikel');
Route::view('/veelgestelde-vragen', 'pages.veelgestelde-vragen')->name('veelgestelde-vragen');
Route::view('/checklist', 'pages.checklist')->name('checklist');
Route::view('/kennismaken', 'pages.kennismaken')->name('kennismaken');
Route::view('/privacyverklaring', 'pages.privacyverklaring')->name('privacyverklaring');
Route::view('/cookiebeleid', 'pages.cookiebeleid')->name('cookiebeleid');
Route::view('/algemene-voorwaarden', 'pages.algemene-voorwaarden')->name('algemene-voorwaarden');
