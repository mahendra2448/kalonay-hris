<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->hidden(fn (User $user): bool => auth()->user()->hasRole('Viewer'))
                ->mutateFormDataUsing(function (array $data): array {
                    $isExists = User::where('email', $data['email'])->exists();

                    if (!$isExists) {
                        $data['password']   = Hash::make($data['password']);
                        $data['created_by'] = auth()->user()->name;
                        
                        return $data;
                    } else {
                        return Filament::notify('warning', 'Email sudah pernah terdaftar.');
                    }
                    
                })
                ->after(function () {
                    $user = User::where('id', DB::getPdo()->lastInsertId())->first();
                    $role = Role::where('id', $user->role)->pluck('name')->first();
                    $user->assignRole($role);

                    activity('user-management')
                        ->causedBy(auth()->user())
                        ->log(\Str::title(auth()->user()->name) . ' has created a new user (' . $user->name . ')');
                })
        ];
    }
}
