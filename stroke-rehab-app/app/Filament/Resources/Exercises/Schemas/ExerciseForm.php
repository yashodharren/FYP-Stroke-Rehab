<?php

namespace App\Filament\Resources\Exercises\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ExerciseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('difficulty_level')
                    ->required(),
                TextInput::make('target_area')
                    ->required(),
                TextInput::make('duration_minutes')
                    ->required()
                    ->numeric()
                    ->default(15),
                TextInput::make('repetitions')
                    ->required()
                    ->numeric()
                    ->default(10),
                Textarea::make('instructions')
                    ->columnSpanFull(),
                FileUpload::make('image_url')
                    ->image(),
                TextInput::make('video_url')
                    ->url(),
            ]);
    }
}
