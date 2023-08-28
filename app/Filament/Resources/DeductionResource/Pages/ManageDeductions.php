<?php

namespace App\Filament\Resources\DeductionResource\Pages;

use App\Filament\Resources\DeductionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDeductions extends ManageRecords
{
    protected static string $resource = DeductionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
