<?php

namespace App\Filament\Resources\Clinicians\Pages;

use App\Filament\Resources\Clinicians\ClinicianResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditClinician extends EditRecord
{
    protected static string $resource = ClinicianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
