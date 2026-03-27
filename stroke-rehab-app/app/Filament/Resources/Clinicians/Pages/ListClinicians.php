<?php

namespace App\Filament\Resources\Clinicians\Pages;

use App\Filament\Resources\Clinicians\ClinicianResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClinicians extends ListRecords
{
    protected static string $resource = ClinicianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
