<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageApplications extends ManageRecords
{
    protected static string $resource = ApplicationResource::class;

    protected function handleRecordCreation(array $data): Application
    {
        $name = $data['name'];
        
        try {
            DB::beginTransaction();
            $create = static::getModel()::firstOrCreate(['name' => $name], ['created_by' => auth()->user()->name]);
            DB::commit();

            activity('advanced-menu')
                ->causedBy(auth()->user())
                ->log(Str::title(auth()->user()->name) . ' has added application/partner: ' . $name . '.');

            return $create;

        } catch (\Throwable $th) {
            DB::rollback();
        }
    }
}
