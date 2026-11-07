<?php

use App\Enums\FilamentPanelEnum;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', fn () => redirect(filament()->getPanel(FilamentPanelEnum::BACKEND->value)->getUrl()))
    ->name('login');
