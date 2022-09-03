<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonthlyDataResource\Pages;
use App\Filament\Resources\MonthlyDataResource\RelationManagers;
use App\Models\Application;
use App\Models\Employee;
use App\Models\Month;
use App\Models\MonthlyData;
use App\Models\User;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MonthlyDataResource extends Resource
{
    protected static ?string $model = MonthlyData::class;

    protected static ?string $navigationIcon = 'heroicon-o-lightning-bolt';
    protected static ?string $navigationLabel = 'Monthly Update';
    protected static ?string $modelLabel = 'Monthly Update';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Select::make('employee_id')->label('Nama Employee')->options(Employee::all()->pluck('name', 'id'))->searchable()->required(),
                    MultiSelect::make('application')->label('Aplikasi/Partner (untuk Desk Collector)')->options(Application::all()->pluck('name', 'id'))->nullable(),
                    Select::make('month_id')->label('Bulan')->options(Month::all()->pluck('name', 'id'))->searchable()->required(),
                    TextInput::make('year')->default(date('Y'))->required(),
                ])->columns(2),
                Card::make()->schema([
                    TextInput::make('achievements')->label('Achievements (%) (untuk Desk Collector)')->numeric()->nullable(),
                    TextInput::make('leave_days')->label('Tidak Hadir (hari dalam 1 bulan)')->numeric()->default(0)->required(),
                    TextInput::make('late_morning')->label('Tidak Presensi Pagi (dalam 1 bulan)')->numeric()->default(0)->required(),
                    TextInput::make('late_evening')->label('Tidak Presensi Sore (dalam 1 bulan)')->numeric()->default(0)->required(),
                    TextInput::make('late_minutes')->label('Keterlambatan (dalam 1 bulan)')->numeric()->default(0)->required(),
                    TextInput::make('allowance')->label('Allowance')->mask(fn (Mask $mask) => $mask->money('Rp ', '.', 0))->numeric()->default(0)->nullable()
                ])->columns(3),
                Card::make()->schema([
                    TextInput::make('additional_insentives')->label('Insentif Tambahan')->mask(fn (Mask $mask) => $mask->money('Rp ', '.', 0))->numeric()->default(0)->nullable(),
                    TextInput::make('additional_insentives_tax')->label('Pajak Insentif Tambahan')->mask(fn (Mask $mask) => $mask->money('Rp ', '.', 0))->numeric()->default(0)->nullable(),
                    TextInput::make('bpjs_kes_amount')->label('BPJS Kesehatan')->mask(fn (Mask $mask) => $mask->money('Rp ', '.', 0))->numeric()->default(0)->nullable(),
                    TextInput::make('bpjs_kenaker_amount')->label('BPJS Tenaga Kerja')->mask(fn (Mask $mask) => $mask->money('Rp ', '.', 0))->numeric()->default(0)->nullable(),
                    TextInput::make('loan')->label('Pinjaman')->mask(fn (Mask $mask) => $mask->money('Rp ', '.', 0))->numeric()->default(0)->nullable(),
                    TextInput::make('other_deduction')->label('Deduction Lainnya')->mask(fn (Mask $mask) => $mask->money('Rp ', '.', 0))->numeric()->default(0)->nullable(),
                    TextInput::make('tax_amount')->label('Pajak')->mask(fn (Mask $mask) => $mask->money('Rp ', '.', 0))->numeric()->default(0)->nullable()
                ])->columns(4),
                Card::make()->schema([
                    TextInput::make('notes')->label('Catatan')->default('-')->required(),
                    // TextInput::make('total_salary')->label('Total Salary')->mask(fn (Mask $mask) => $mask->money('Rp ', '.', 0))->disabled()->default('24090909090')->required(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name'),
                Tables\Columns\TextColumn::make('employee.phone')
                    ->hidden(fn (User $user): bool => auth()->user()->hasRole('Viewer')),
                Tables\Columns\TextColumn::make('employee.positionName.name'),
                Tables\Columns\TextColumn::make('month.name'),
                Tables\Columns\TextColumn::make('total_salary')->money('IDR', '.'),
                Tables\Columns\TextColumn::make('created_at')->since(),
                Tables\Columns\TextColumn::make('updated_at')->since(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn (User $user): bool => auth()->user()->hasRole('Viewer')),
                Tables\Actions\Action::make('trybutt')
                    ->label('To Google')->url('https://google.com')->color('success'),
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
            'index' => Pages\ListMonthlyData::route('/'),
            'create' => Pages\CreateMonthlyData::route('/create'),
            'edit' => Pages\EditMonthlyData::route('/{record}/edit'),
        ];
    }    
}
