<?php

use App\Http\Controllers\Checklist\OpenChecklistController;
use App\Http\Controllers\Checklist\StoreSignupController;
use Illuminate\Support\Facades\Route;
use Made\CmsPackage\Content\Models\Page;

// Stijlgids met alle gedeelde componenten, alleen lokaal.
if (app()->environment('local')) {
    Route::view('/componenten', 'pages.componenten')->name('componenten');
}

// De aanmelding voor de checklist en de link in de checklist-mail (#25).
Route::post('/checklist/aanmelden', StoreSignupController::class)
    ->middleware('throttle:checklist-signup')
    ->name('checklist.signup');

Route::get('/checklist/openen/{signup}', OpenChecklistController::class)
    ->middleware('signed')
    ->whereUlid('signup')
    ->name('checklist.open');

// Elke pagina uit het CMS, op het adres dat geen route hierboven claimt, ook de homepage (/). Dit blijft de laatste
// route: routes eronder worden nooit bereikt. Een vaste route hierboven gaat voor, ook als het CMS een pagina op
// hetzelfde adres heeft. Deze route heeft geen naam, dus een link naar de homepage is url('/').
Route::madeCms(fn (Page $page) => view('page', ['page' => $page]));
