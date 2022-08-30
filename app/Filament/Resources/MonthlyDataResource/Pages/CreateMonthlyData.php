<?php

namespace App\Filament\Resources\MonthlyDataResource\Pages;

use App\Filament\Resources\MonthlyDataResource;
use App\Models\User;
use App\Models\Employee;
use App\Models\Deduction;
use App\Models\MonthlyData;
use App\Models\AchievementType;
use Illuminate\Database\Eloquent\Model;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMonthlyData extends CreateRecord
{
    protected static string $resource = MonthlyDataResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->user()->name;
        return $data;
    }
    
    protected function handleRecordCreation(array $data): Model
    {
        $achType = AchievementType::select('type', 'percentage', 'top_limit', 'bottom_limit')->get();
        $empDetails = Employee::where('id', $data['employee_id'])->first();
        $empDailySalary = $empDetails->main_salary / $empDetails->workdays;
        $empActualWorkdays = $empDetails->workdays - $data['leave_days'];
        $empTempSalary = round($empDailySalary * $empActualWorkdays);        

        /**
         * Filter for Desk Collector only
         * 14 is the id on 'positions' table
         */
        if ($empDetails->position_id == 14) {
            foreach ($achType as $ach) {
                $empAch = ($data['achievements'] != null) ? $data['achievements'] : 0;
                if ($empAch <= $ach->top_limit && $empAch >= $ach->bottom_limit) {
                    $empAchPercentage = $ach->percentage;
                }
            }
            $empTempSalary = $empTempSalary * $empAchPercentage / 100;
        }

        /**
         * Additionals
         */
        $allowance = (int) $data['allowance'];
        $additionalInsentives = (int) $data['additional_insentives'];

        $empTotalSalary = $empTempSalary + $allowance + $additionalInsentives;

        
        /**
         * Deductions
         * App\Models\Deduction;
         */
        $ded = Deduction::pluck('value','name');
        
        $empLateMin = (array_key_exists('late_minutes', $data)) ? ((int) $data['late_minutes'] * $ded['late_minutes']) : 0;
        $empMorningAbs = (array_key_exists('late_morning', $data)) ? ((int) $data['late_morning'] * $ded['late_morning']) : 0;        
        $empEveningAbs = (array_key_exists('late_evening', $data)) ? ((int) $data['late_evening'] * $ded['late_evening']) : 0;
        $empTaxAdditional = (int) $data['additional_insentives_tax'];
        $empBpjsKes = (int) $data['bpjs_kes_amount'];
        $empBpjsTK = (int) $data['bpjs_kenaker_amount'];
        $empLoan = (int) $data['loan'];
        $empOtherDeduct = (int) $data['other_deduction'];
        $empTax = (int) $data['tax_amount'];

        $deductions = array_sum([$empLateMin,$empMorningAbs,$empEveningAbs,$empTaxAdditional,$empBpjsKes,$empBpjsTK,$empLoan,$empOtherDeduct,$empTax]);


        /**
         * Total Salary
         */
        $totalSalary = $empTotalSalary - $deductions;
        dd($totalSalary, $empTotalSalary, $deductions);

        $name = MonthlyData::where('id', $data['id'])->first()->employeeName()->name;

        activity('monthly-update')
            ->causedBy(auth()->user())
            ->log(\Str::title(auth()->user()->name) . ' has create monthly update for ' . $name);

        return static::getModel()::create($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
