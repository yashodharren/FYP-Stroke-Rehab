<?php

namespace App\Filament\Resources\Patients\Schemas;

use Filament\Infolists\Components\IconEntry;
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
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('deficit_area')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('medical_history')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('recovery_status')
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('gender')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('rsbp')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('stroke_subtype')
                    ->placeholder('-'),
                TextEntry::make('conscious_state')
                    ->placeholder('-'),
                IconEntry::make('rdef1')
                    ->boolean(),
                IconEntry::make('rdef2')
                    ->boolean(),
                IconEntry::make('rdef3')
                    ->boolean(),
                IconEntry::make('rdef4')
                    ->boolean(),
                IconEntry::make('rdef5')
                    ->boolean(),
                IconEntry::make('rdef6')
                    ->boolean(),
                IconEntry::make('rdef7')
                    ->boolean(),
                IconEntry::make('rdef8')
                    ->boolean(),
            ]);
    }
}
