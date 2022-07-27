<?php

namespace App\Filament\Resources\PositionResource\Pages;

use App\Filament\Resources\PositionResource;
use App\Models\User;
use App\Models\Position;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePositions extends ManageRecords
{
    protected static string $resource = PositionResource::class;

    protected function handleRecordCreation(array $data): Position
    {
        
        try {
            DB::beginTransaction();
            $create = static::getModel()::firstOrCreate(['name' => $data['name']], ['created_by' => auth()->user()->name]);
            DB::commit();

            activity('advanced-menu')
                ->causedBy(auth()->user())
                ->log(Str::title(auth()->user()->name) . ' has create a position: ' . $data['name'] . '.');

            return $create;

        } catch (\Throwable $th) {
            DB::rollback();
        }
    }
}
