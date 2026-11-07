<?php

namespace App\Filament\Backend\Resources\BackendUsers\Tables;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BackendUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('users.attributes.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('users.attributes.email'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('timezone')
                    ->label(__('users.attributes.timezone')),
                TextColumn::make('created_at')
                    ->label(__('users.attributes.created_at'))
                    ->sortable(),
            ])
            ->filters([
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')
                            ->label(__('users.filters.created_from'))
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        DatePicker::make('created_until')
                            ->label(__('users.filters.created_until'))
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators[] = Indicator::make(__('users.filters.created_from') . ' ' . Carbon::parse($data['created_from'])->toFormattedDateString())
                                ->removeField('created_from');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = Indicator::make(__('users.filters.created_until') . ' ' . Carbon::parse($data['created_until'])->toFormattedDateString())
                                ->removeField('created_until');
                        }

                        return $indicators;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->where('created_at', '>=', Carbon::parse($date, auth()->user()->timezone)->startOfDay()->timezone(config('app.timezone'))),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->where('created_at', '<=', Carbon::parse($date, auth()->user()->timezone)->endOfDay()->timezone(config('app.timezone'))),
                            );
                    }),
                TernaryFilter::make('verified')
                    ->label('Verified email')
                    ->attribute('email_verified_at')
                    ->nullable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make('delete'),
                ]),
            ])
            ->defaultSort('name');
    }
}
