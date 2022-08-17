<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeesResource\Pages;
use App\Filament\Resources\EmployeesResource\RelationManagers;
use App\Models\Bank;
use App\Models\Employee;
use App\Models\User;
use App\Models\Position;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeesResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Employee Management';
    protected static ?string $navigationGroup = 'Advanced';
    protected static ?string $modelLabel = 'Employee Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    TextInput::make('name')->label('Nama Lengkap')->minLength(5)->required(),
                    TextInput::make('nik')->label('NIK')->numeric()->minLength(10)->required(),
                    TextInput::make('email')->email()->required(),
                    TextInput::make('phone')->minLength(11)->required(),
                ])->columns(2),
                Card::make()->schema([
                    TextInput::make('nip')->label('NIP')->nullable(),
                    Select::make('position_id')->label('Posisi/Jabatan')->options(Position::all()->pluck('name', 'id'))->required(),
                    TextInput::make('main_salary')->label('Gaji Pokok')->mask(fn (TextInput\Mask $mask) => $mask->money('Rp ', '.', 0))->required(),
                    Select::make('account_bank_id')->label('Bank')->options(Bank::all()->pluck('name', 'id'))->required(),
                    TextInput::make('account_name')->label('Nama Akun Bank (pemilik rekening)')->minLength(3)->required(),
                    TextInput::make('account_number')->label('No. Rekening Bank')->required(),
                    TextInput::make('workdays')->label('Hari Kerja (dalam 1 bulan)')->numeric()->required(),
                ])->columns(3),
                Card::make()->schema([
                    Select::make('parking_status')->label('Parkir Kendaraan')->options([1 => 'Iya', 0 => 'Tidak'])->nullable(),
                    TextInput::make('parking_amount')->label('Biaya Parkir')->numeric()->mask(fn (TextInput\Mask $mask) => $mask->money('Rp ', '.', 0))->nullable(),
                    TextInput::make('insurance_amount')->label('Nominal Asuransi')->numeric()->mask(fn (TextInput\Mask $mask) => $mask->money('Rp ', '.', 0))->nullable(),
                    TextInput::make('bpjs_kes_amount')->label('BPJS Kesehatan (nominal)')->numeric()->mask(fn (TextInput\Mask $mask) => $mask->money('Rp ', '.', 0))->nullable(),
                    TextInput::make('bpjs_kenaker_amount')->label('BPJS Ketenagakerjaan (nominal)')->numeric()->mask(fn (TextInput\Mask $mask) => $mask->money('Rp ', '.', 0))->nullable(),
                    TextInput::make('tax_amount')->label('Pajak (nominal)')->mask(fn (TextInput\Mask $mask) => $mask->money('Rp ', '.', 0))->nullable(),
                ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('phone')
                    ->hidden(fn (User $user): bool => auth()->user()->hasRole('Viewer')),
                Tables\Columns\TextColumn::make('positionName.name'),
                Tables\Columns\TextColumn::make('created_at')->since(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn (User $user): bool => auth()->user()->hasRole('Viewer')),
            ])
            ->bulkActions([]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployees::route('/create'),
            'edit' => Pages\EditEmployees::route('/{record}/edit'),
        ];
    }    
}
