<?php

namespace App\Filament\Resources\MonthlyDataResource\Pages;

use App\Filament\Resources\MonthlyDataResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMonthlyData extends ListRecords
{
    protected static string $resource = MonthlyDataResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
