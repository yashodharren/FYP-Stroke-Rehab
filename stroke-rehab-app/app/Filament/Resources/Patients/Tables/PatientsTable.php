<?php

namespace App\Filament\Resources\Patients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PatientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('clinician_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('age')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stroke_type')
                    ->badge(),
                TextColumn::make('deficit_area')
                    ->badge(),
                TextColumn::make('recovery_status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('gender')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('rsbp')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stroke_subtype')
                    ->searchable(),
                TextColumn::make('conscious_state')
                    ->searchable(),
                IconColumn::make('rdef1')
                    ->boolean(),
                IconColumn::make('rdef2')
                    ->boolean(),
                IconColumn::make('rdef3')
                    ->boolean(),
                IconColumn::make('rdef4')
                    ->boolean(),
                IconColumn::make('rdef5')
                    ->boolean(),
                IconColumn::make('rdef6')
                    ->boolean(),
                IconColumn::make('rdef7')
                    ->boolean(),
                IconColumn::make('rdef8')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
