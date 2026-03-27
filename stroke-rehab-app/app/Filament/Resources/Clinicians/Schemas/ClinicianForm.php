<?php

namespace App\Filament\Resources\Clinicians\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ClinicianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('specialization'),
                TextInput::make('license_number'),
                Textarea::make('bio')
                    ->columnSpanFull(),
            ]);
    }
}
