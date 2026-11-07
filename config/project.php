<?php

return [
    'filament' => [
        'panels' => [
            'backend' => [
                'domain' => env('FILAMENT_PANELS_BACKEND_DOMAIN'),
                'path' => env('FILAMENT_PANELS_BACKEND_PATH', ''),
            ],
        ],
    ],
];
