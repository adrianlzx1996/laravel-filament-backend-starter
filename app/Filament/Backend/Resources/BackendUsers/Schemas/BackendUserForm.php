<?php

namespace App\Filament\Backend\Resources\BackendUsers\Schemas;

use App\Enums\RolesEnum;
use App\Models\User;
use Carbon\CarbonTimeZone;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class BackendUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    TextInput::make('name')
                        ->label(__('users.attributes.name'))
                        ->maxLength(255)
                        ->required(),
                    TextInput::make('email')
                        ->label(__('users.attributes.email'))
                        ->maxLength(255)
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true),
                    Select::make('timezone')
                        ->label(__('users.attributes.timezone'))
                        ->options(
                            collect(CarbonTimeZone::listIdentifiers())
                                ->mapWithKeys(function ($timezone) {
                                    return [$timezone => $timezone];
                                })
                                ->toArray()
                        )
                        ->searchable()
                        ->optionsLimit(500)
                        ->required(),
                ])
                    ->columns(2)
                    ->saveRelationshipsUsing(function (User $record, $state) {
                        $record->roles()->sync(Role::query()->firstWhere('name', RolesEnum::BACKEND->value), false);
                    }),
            ])->columns(1);
    }
}
