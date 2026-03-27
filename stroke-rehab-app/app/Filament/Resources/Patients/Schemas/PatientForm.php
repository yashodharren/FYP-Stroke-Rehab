<?php

namespace App\Filament\Resources\Patients\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Filament\Schemas\Schema;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Basic Patient Information
                Section::make('Patient Information')
                    ->description('Basic patient details')
                    ->schema([
                        TextInput::make('user_id')
                            ->label('User ID')
                            ->required()
                            ->numeric()
                            ->disabled(),
                        TextInput::make('clinician_id')
                            ->label('Assigned Clinician ID')
                            ->numeric()
                            ->disabled(),
                        TextInput::make('age')
                            ->label('Age (years)')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(150),
                        Select::make('recovery_status')
                            ->label('Recovery Status')
                            ->options([
                                'new' => 'New',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'paused' => 'Paused',
                            ])
                            ->default('new')
                            ->required(),
                    ])->columns(2),

                // Demographics & Vitals (IST Dataset)
                Section::make('Demographics & Vitals')
                    ->description('Clinical baseline measurements from IST dataset')
                    ->schema([
                        Select::make('gender')
                            ->label('Gender')
                            ->options([
                                0 => 'Female',
                                1 => 'Male',
                            ])
                            ->required(),
                        TextInput::make('rsbp')
                            ->label('Systolic Blood Pressure (mmHg)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(300)
                            ->helperText('If > 160 mmHg, system suggests conservative, low-intensity plan'),
                    ])->columns(2),

                // Stroke Characterization (IST Dataset)
                Section::make('Stroke Characterization')
                    ->description('Type and severity of stroke event')
                    ->schema([
                        Select::make('stroke_subtype')
                            ->label('Stroke Subtype')
                            ->options([
                                'TACS' => 'TACS - Total Anterior Circulation Stroke (High Severity)',
                                'PACS' => 'PACS - Partial Anterior Circulation Stroke',
                                'LACS' => 'LACS - Lacunar Stroke (Small vessel, better recovery)',
                                'POCS' => 'POCS - Posterior Circulation Stroke',
                                'OTH' => 'OTH - Other/Unclassified',
                            ])
                            ->required(),
                        Select::make('conscious_state')
                            ->label('Conscious State')
                            ->options([
                                'Alert' => 'Fully Alert',
                                'Drowsy' => 'Drowsy',
                                'Unconscious' => 'Unconscious',
                            ])
                            ->required(),
                    ])->columns(2),

                // Functional Deficits (RDEF fields - IST Dataset)
                Section::make('Functional Deficits')
                    ->description('Select all deficits present. These map to exercise recommendations.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('rdef1')
                                    ->label('Face Deficit')
                                    ->helperText('Facial weakness or asymmetry'),
                                Toggle::make('rdef2')
                                    ->label('Arm/Hand Deficit')
                                    ->helperText('Upper limb weakness or loss of function'),
                                Toggle::make('rdef3')
                                    ->label('Leg/Foot Deficit')
                                    ->helperText('Lower limb weakness or loss of function'),
                                Toggle::make('rdef4')
                                    ->label('Dysphasia (Speech)')
                                    ->helperText('Speech or language impairment'),
                                Toggle::make('rdef5')
                                    ->label('Hemianopia (Vision)')
                                    ->helperText('Loss of visual field'),
                                Toggle::make('rdef6')
                                    ->label('Visuospatial Disorder')
                                    ->helperText('Spatial awareness or coordination issues'),
                                Toggle::make('rdef7')
                                    ->label('Brainstem/Cerebellar Signs')
                                    ->helperText('Balance, coordination, or brainstem symptoms'),
                                Toggle::make('rdef8')
                                    ->label('Other Deficits')
                                    ->helperText('Any other neurological deficits'),
                            ]),
                    ]),
            ]);
    }
}
