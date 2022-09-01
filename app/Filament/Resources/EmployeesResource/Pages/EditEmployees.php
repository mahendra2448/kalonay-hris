<?php

namespace App\Filament\Resources\EmployeesResource\Pages;

use App\Filament\Resources\EmployeesResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployees extends EditRecord
{
    protected static string $resource = EmployeesResource::class;
    protected $name = '';

    protected function getActions(): array
    {
        $employee = $this->name;

        return [
            Actions\DeleteAction::make()
                ->hidden(fn (User $user): bool => auth()->user()->hasRole('Viewer'))
                ->before(function () {
                    $this->name = $this->data['name'];
                })
                ->after(function () {
                    activity('employee')
                        ->causedBy(auth()->user())
                        ->log(\Str::title(auth()->user()->name) . ' has deleted an employee (' . $this->name . ')');
                }),
        ];
    }
    
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['updated_by'] = auth()->user()->name;
        $record->update($data);
        activity('employee')
            ->causedBy(auth()->user())
            ->log(\Str::title(auth()->user()->name) . ' has updated an employee (' . $data['name'] . ')');
    
        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
