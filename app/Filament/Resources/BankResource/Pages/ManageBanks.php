<?php

namespace App\Filament\Resources\BankResource\Pages;

use App\Filament\Resources\BankResource;
use App\Models\Bank;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBanks extends ManageRecords
{
    protected static string $resource = BankResource::class;

    protected function handleRecordCreation(array $data): Bank
    {
        $name = Str::replace('bank ','',$data['name']);
        
        try {
            DB::beginTransaction();
            $create = static::getModel()::firstOrCreate(['name' => $name], ['created_by' => auth()->user()->name]);
            DB::commit();

            activity('advanced-menu')
                ->causedBy(auth()->user())
                ->log(Str::title(auth()->user()->name) . ' has create a bank: ' . $data['name'] . '.');

            return $create;

        } catch (\Throwable $th) {
            DB::rollback();
        }
    }

}
