<?php

namespace App\Filament\Resources\AchievementResource\Pages;

use App\Filament\Resources\AchievementResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAchievement extends EditRecord
{
    protected static string $resource = AchievementResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
