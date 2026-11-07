<?php

namespace App\Filament\Backend\Resources\BackendUsers;

use App\Enums\RolesEnum;
use App\Filament\Backend\Resources\BackendUsers\Pages\CreateBackendUser;
use App\Filament\Backend\Resources\BackendUsers\Pages\EditBackendUser;
use App\Filament\Backend\Resources\BackendUsers\Pages\ListBackendUsers;
use App\Filament\Backend\Resources\BackendUsers\Schemas\BackendUserForm;
use App\Filament\Backend\Resources\BackendUsers\Tables\BackendUsersTable;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BackendUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedUsers;

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return $record->getAttribute('name') . ' - ' . $record->getAttribute('email');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigations.backend.params');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigations.backend.backend-users');
    }

    public static function getModelLabel(): string
    {
        return trans_choice('roles.backend', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('roles.backend', 2);
    }

    public static function form(Schema $schema): Schema
    {
        return BackendUserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BackendUsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBackendUsers::route('/'),
            'create' => CreateBackendUser::route('/create'),
            'edit' => EditBackendUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('roles', function (Builder $query) {
                $query->where('name', RolesEnum::BACKEND->value);
            });
    }
}
