<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RolesEnum: string implements HasLabel
{
    case BACKEND = 'backend';

    public function getLabel(): string
    {
        return match ($this) {
            RolesEnum::BACKEND => trans_choice('roles.backend', 1),
        };
    }
}
