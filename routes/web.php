<?php

use App\Http\Controllers\BlogartikelController;
use Illuminate\Support\Facades\Route;
use Made\CmsPackage\Content\Models\Page;

Route::view('/blog', 'pages.blog')->name('blog');
Route::view('/blog/archief', 'pages.archief')->name('blog.archief');
Route::get('/blog/{slug}', BlogartikelController::class)->name('blog.artikel');
Route::view('/veelgestelde-vragen', 'pages.veelgestelde-vragen')->name('veelgestelde-vragen');
Route::view('/checklist', 'pages.checklist')->name('checklist');
Route::view('/kennismaken', 'pages.kennismaken')->name('kennismaken');
Route::view('/privacyverklaring', 'pages.privacyverklaring')->name('privacyverklaring');
Route::view('/cookiebeleid', 'pages.cookiebeleid')->name('cookiebeleid');
Route::view('/algemene-voorwaarden', 'pages.algemene-voorwaarden')->name('algemene-voorwaarden');

// Stijlgids met alle gedeelde componenten, alleen lokaal.
if (app()->environment('local')) {
    Route::view('/componenten', 'pages.componenten')->name('componenten');
}

// Elke pagina uit het CMS, op het adres dat geen route hierboven claimt, ook de homepage (/). Dit blijft de laatste
// route: routes eronder worden nooit bereikt. Een vaste route hierboven gaat voor, ook als het CMS een pagina op
// hetzelfde adres heeft. Deze route heeft geen naam, dus een link naar de homepage is url('/').
Route::madeCms(fn (Page $page) => view('page', ['page' => $page]));
