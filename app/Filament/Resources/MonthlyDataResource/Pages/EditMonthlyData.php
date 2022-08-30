<?php

namespace App\Filament\Resources\MonthlyDataResource\Pages;

use App\Filament\Resources\MonthlyDataResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMonthlyData extends EditRecord
{
    protected static string $resource = MonthlyDataResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
