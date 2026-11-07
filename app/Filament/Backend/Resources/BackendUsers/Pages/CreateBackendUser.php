<?php

namespace App\Filament\Backend\Resources\BackendUsers\Pages;

use App\Enums\FilamentPanelEnum;
use App\Filament\Backend\Resources\BackendUsers\BackendUserResource;
use App\Models\User;
use App\Notifications\Backend\BackendUser\Invite;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateBackendUser extends CreateRecord
{
    protected static string $resource = BackendUserResource::class;

    protected string $password;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->password = Str::password(12);
        $data['password'] = bcrypt($this->password);

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        /** @var User $backendUser */
        $backendUser = parent::handleRecordCreation($data);
        $backendUser->markEmailAsVerified();

        $backendUser->notify(new Invite($this->password, filament()->getPanel(FilamentPanelEnum::BACKEND->value)->getUrl()));

        return $backendUser;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
