<?php

return [
    'title' => 'Renew password',
    'heading' => 'It\'s time to renew your password to accurate the security of your account.',
    'form' => [
        'current-password' => [
            'label' => 'Current password',
        ],
        'password' => [
            'label' => 'New password',
            'helps' => 'The password must contain at least 12 characters, including one lowercase letter, one uppercase letter, one number, and one special character.'
        ],
        'password-confirmation' => [
            'label' => 'New password confirmation',
        ],
        'actions' => [
            'renew' => [
                'label' => 'Renew',
            ],
        ],
    ],
    'notifications' => [
        'title' => 'Renew Password',
        'body' => 'Successfully!',
    ],
];
