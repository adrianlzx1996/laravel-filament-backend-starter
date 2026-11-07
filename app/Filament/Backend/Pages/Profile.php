<?php

namespace App\Filament\Backend\Pages;

use Carbon\CarbonTimeZone;
use Filament\Auth\Pages\EditProfile;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class Profile extends EditProfile
{
    public static function getLabel(): string
    {
        return __('navigations.backend.my_profile');
    }

    protected Width | string | null $maxContentWidth = Width::ThreeExtraLarge;

    protected function getPasswordFormComponent(): Component
    {
        /** @var TextInput $textInput */
        $textInput = parent::getPasswordFormComponent();

        return $textInput->helperText(__('passwords.rules'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function getRedirectUrl(): ?string
    {
        return Filament::getUrl();
    }
}
