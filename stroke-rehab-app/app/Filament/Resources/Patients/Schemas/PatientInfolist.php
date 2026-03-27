<?php

namespace App\Filament\Resources\Patients\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PatientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('clinician_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('age')
                    ->numeric(),
                TextEntry::make('stroke_type')
                    ->placeholder('-'),
                TextEntry::make('deficit_area')
                    ->placeholder('-'),
                TextEntry::make('medical_history')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('recovery_status'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
