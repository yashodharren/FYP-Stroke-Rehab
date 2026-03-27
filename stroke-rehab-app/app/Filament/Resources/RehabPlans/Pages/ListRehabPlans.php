<?php

namespace App\Filament\Resources\RehabPlans\Pages;

use App\Filament\Resources\RehabPlans\RehabPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRehabPlans extends ListRecords
{
    protected static string $resource = RehabPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
