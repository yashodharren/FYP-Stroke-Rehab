<?php

namespace App\Filament\Resources\RehabPlans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class RehabPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('patient_id')
                    ->required()
                    ->numeric(),
                TextInput::make('clinician_id')
                    ->required()
                    ->numeric(),
                TextInput::make('plan_name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('recovery_probability')
                    ->numeric(),
                TextInput::make('difficulty_level')
                    ->required()
                    ->default('1'),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date'),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                Textarea::make('ml_metadata')
                    ->columnSpanFull(),
            ]);
    }
}
