<?php

namespace App\Filament\Resources\Patients\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('clinician_id')
                    ->numeric(),
                TextInput::make('age')
                    ->required()
                    ->numeric(),
                TextInput::make('stroke_type'),
                TextInput::make('deficit_area'),
                Textarea::make('medical_history')
                    ->columnSpanFull(),
                TextInput::make('recovery_status')
                    ->required()
                    ->default('new'),
            ]);
    }
}
