<?php

namespace App\Filament\Backend\Resources\BackendUsers\Pages;

use App\Filament\Backend\Resources\BackendUsers\BackendUserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBackendUser extends EditRecord
{
    protected static string $resource = BackendUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
