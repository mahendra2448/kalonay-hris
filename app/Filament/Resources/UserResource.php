<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Users Management';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $modelLabel = 'Users Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('name')->required(),
                    Forms\Components\TextInput::make('email')->email()->required(),
                    Forms\Components\TextInput::make('password')->password()->required(),
                    Forms\Components\Select::make('role')->options(Role::all()->pluck('name', 'id'))->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email')
                    ->hidden(fn (User $user): bool => auth()->user()->hasRole('Viewer')),
                Tables\Columns\TextColumn::make('roleName.name'),
                Tables\Columns\TextColumn::make('created_at')->since(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (User $user): bool => auth()->user()->hasRole('Super Admin'))
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['password']   = Hash::make($data['password']);
                        $data['updated_by'] = auth()->user()->name;
                        return $data;
                    })
                    ->after(function ($data) {
                        $user = User::where('email', $data['email'])->first();
                        $role = Role::where('id', $user->role)->pluck('name')->first();
                        $user->syncRoles($role);

                        activity('user-management')
                            ->causedBy(auth()->user())
                            ->log(\Str::title(auth()->user()->name) . ' has update user (' . $user->name . ')');
                    }),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (User $user): bool => auth()->user()->hasRole('Super Admin'))
                    // ->after(function ($data) {

                    // }),
            ])
            ->bulkActions([]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }    
    
}
