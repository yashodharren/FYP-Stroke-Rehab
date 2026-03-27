<?php

namespace App\Filament\Resources\RehabPlans\Pages;

use App\Filament\Resources\RehabPlans\RehabPlanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRehabPlan extends ViewRecord
{
    protected static string $resource = RehabPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
