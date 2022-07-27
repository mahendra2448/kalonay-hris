<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PositionResource\Pages;
use App\Filament\Resources\PositionResource\RelationManagers;
use App\Models\Position;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PositionResource extends Resource
{
    protected static ?string $model = Position::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'Advanced';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('created_at')->date(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (User $user): bool => auth()->user()->hasRole(['Super Admin', 'Admin']))
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['updated_by'] = auth()->user()->name;
                        return $data;
                    })
                    ->after(function ($data) {
                        activity('advanced-menu')
                            ->causedBy(auth()->user())
                            ->log(Str::title(auth()->user()->name) . ' has update position: ' . $data['name'] . '.');
                    }),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (User $user): bool => auth()->user()->hasRole(['Super Admin', 'Admin']))
                    ->after(function () {
                        activity('advanced-menu')
                            ->causedBy(auth()->user())
                            ->log(Str::title(auth()->user()->name) . ' has deleted a position.');
                    }),
            ])
            ->bulkActions([]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePositions::route('/'),
        ];
    }    
}
