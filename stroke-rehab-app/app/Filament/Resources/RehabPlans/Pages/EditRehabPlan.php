<?php

namespace App\Filament\Resources\RehabPlans\Pages;

use App\Filament\Resources\RehabPlans\RehabPlanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditRehabPlan extends EditRecord
{
    protected static string $resource = RehabPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
