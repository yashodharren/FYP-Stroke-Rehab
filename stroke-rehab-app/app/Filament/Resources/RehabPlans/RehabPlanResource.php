<?php

namespace App\Filament\Resources\RehabPlans;

use App\Filament\Resources\RehabPlans\Pages\CreateRehabPlan;
use App\Filament\Resources\RehabPlans\Pages\EditRehabPlan;
use App\Filament\Resources\RehabPlans\Pages\ListRehabPlans;
use App\Filament\Resources\RehabPlans\Pages\ViewRehabPlan;
use App\Filament\Resources\RehabPlans\Schemas\RehabPlanForm;
use App\Filament\Resources\RehabPlans\Schemas\RehabPlanInfolist;
use App\Filament\Resources\RehabPlans\Tables\RehabPlansTable;
use App\Models\RehabPlan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RehabPlanResource extends Resource
{
    protected static ?string $model = RehabPlan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RehabPlanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RehabPlanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RehabPlansTable::configure($table);
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
            'index' => ListRehabPlans::route('/'),
            'create' => CreateRehabPlan::route('/create'),
            'view' => ViewRehabPlan::route('/{record}'),
            'edit' => EditRehabPlan::route('/{record}/edit'),
        ];
    }
}
