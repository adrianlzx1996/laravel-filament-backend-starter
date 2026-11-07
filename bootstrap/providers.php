<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\BackendPanelProvider;
use App\Providers\HorizonServiceProvider;

return [
    AppServiceProvider::class,
    BackendPanelProvider::class,
    HorizonServiceProvider::class,
];
