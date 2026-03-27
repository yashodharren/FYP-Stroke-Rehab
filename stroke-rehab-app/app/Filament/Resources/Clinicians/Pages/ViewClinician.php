<?php

namespace App\Filament\Resources\Clinicians\Pages;

use App\Filament\Resources\Clinicians\ClinicianResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewClinician extends ViewRecord
{
    protected static string $resource = ClinicianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
