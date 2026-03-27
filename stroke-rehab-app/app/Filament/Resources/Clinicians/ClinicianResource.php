<?php

namespace App\Filament\Resources\Clinicians;

use App\Filament\Resources\Clinicians\Pages\CreateClinician;
use App\Filament\Resources\Clinicians\Pages\EditClinician;
use App\Filament\Resources\Clinicians\Pages\ListClinicians;
use App\Filament\Resources\Clinicians\Pages\ViewClinician;
use App\Filament\Resources\Clinicians\Schemas\ClinicianForm;
use App\Filament\Resources\Clinicians\Schemas\ClinicianInfolist;
use App\Filament\Resources\Clinicians\Tables\CliniciansTable;
use App\Models\Clinician;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClinicianResource extends Resource
{
    protected static ?string $model = Clinician::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ClinicianForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClinicianInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CliniciansTable::configure($table);
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
            'index' => ListClinicians::route('/'),
            'create' => CreateClinician::route('/create'),
            'view' => ViewClinician::route('/{record}'),
            'edit' => EditClinician::route('/{record}/edit'),
        ];
    }
}
