<?php

namespace App\Filament\Resources\Clinicians\Pages;

use App\Filament\Resources\Clinicians\ClinicianResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClinician extends CreateRecord
{
    protected static string $resource = ClinicianResource::class;
}
