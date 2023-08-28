<?php

namespace App\Filament\Resources\AbsensiResource\Pages;

use App\Filament\Resources\AbsensiResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbsensi extends EditRecord
{
    protected static string $resource = AbsensiResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
