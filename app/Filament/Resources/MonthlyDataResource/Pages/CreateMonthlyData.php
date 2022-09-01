<?php

namespace App\Filament\Resources\MonthlyDataResource\Pages;

use App\Filament\Resources\MonthlyDataResource;
use App\Models\User;
use App\Models\Employee;
use App\Models\History;
use App\Models\Deduction;
use App\Models\MonthlyData;
use App\Models\Application;
use App\Models\AchievementType;
use Illuminate\Database\Eloquent\Model;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateMonthlyData extends CreateRecord
{
    protected static string $resource = MonthlyDataResource::class;
    protected $dataInput = [];
    
    protected function handleRecordCreation(array $data): Model
    {
        $achType = AchievementType::select('type', 'percentage', 'top_limit', 'bottom_limit')->get();
        $empDetails = Employee::where('id', $data['employee_id'])->first();
        $empDailySalary = $empDetails->main_salary / $empDetails->workdays;
        $empTempSalary = $empDetails->main_salary;

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
         * Deductions from input and Employee data itself
         * App\Models\Deduction;
         */
        $ded = Deduction::pluck('value','name');
        
        $empLeaveDeduct = round($empDailySalary * (int) $data['leave_days']);
        $empLateMin = (array_key_exists('late_minutes', $data)) ? ((int) $data['late_minutes'] * $ded['late_minutes']) : 0;
        $empMorningAbs = (array_key_exists('late_morning', $data)) ? ((int) $data['late_morning'] * $ded['late_morning']) : 0;        
        $empEveningAbs = (array_key_exists('late_evening', $data)) ? ((int) $data['late_evening'] * $ded['late_evening']) : 0;
        $empTaxAdditional = (int) $data['additional_insentives_tax'];
        $empBpjsKes = (int) ($data['bpjs_kes_amount'] > 0) ? $data['bpjs_kes_amount'] : $empDetails->bpjs_kes_amount;
        $empBpjsTK = (int) ($data['bpjs_kenaker_amount'] > 0) ? $data['bpjs_kenaker_amount'] : $empDetails->bpjs_kenaker_amount;
        $empLoan = (int) $data['loan'];
        $empOtherDeduct = (int) $data['other_deduction'];
        $empTax = (int) ($data['tax_amount'] > 0) ? $data['tax_amount'] : $empDetails->tax_amount;
        $empInsurance = (int) $empDetails->insurance_amount;
        $empParking = (int) $empDetails->parking_amount;

        $deductions = array_sum([
            $empLeaveDeduct,$empLateMin,$empMorningAbs,$empEveningAbs,$empTaxAdditional,$empBpjsKes,$empBpjsTK,$empLoan,$empOtherDeduct,$empTax,$empInsurance,$empParking
        ]);
        $totalSalary = $empTotalSalary - $deductions;


        /**
         * Additional data to save
         */
        $apps = Application::whereIn('id', $data['application'])->pluck('name');
        $data['application'] = json_encode($apps);
        $data['total_salary'] = $totalSalary;
        $data['total_deduction'] = $deductions;
        $data['created_by'] = auth()->user()->name;

        if (array_sum([$empInsurance, $empParking]) > 0) {
            $data['other_deduction'] = array_sum([(int) $data['other_deduction'], $empInsurance, $empParking]);
            $data['notes'] = $data['notes'] . ' || Deduction lainnya --- Asuransi: Rp '. $empInsurance .',00. Parkir: Rp '. $empParking .',00';
        }

        $this->dataInput = $data; // override to global variable


        activity('monthly-update')
            ->causedBy(auth()->user())
            ->log(\Str::title(auth()->user()->name) . ' has create monthly update for ' . $empDetails->name);

        return static::getModel()::create($data);
    }

    protected function afterCreate(): void
    {
        /**
         * For History
         */
        $data = $this->dataInput;

        try {
            DB::beginTransaction();
            $history = History::create([
                'employee_id' => $data['employee_id'],
                'month_id' => $data['month_id'],
                'year' => $data['year'],
                'achievements' => $data['achievements'],
                'leave_days' => $data['leave_days'],
                'late_morning' => $data['late_morning'],
                'late_evening' => $data['late_evening'],
                'late_minutes' => $data['late_minutes'],
                'additional_insentives' => $data['additional_insentives'],
                'additional_insentives_tax' => $data['additional_insentives_tax'],
                'bpjs_kes_amount' => $data['bpjs_kes_amount'],
                'bpjs_kenaker_amount' => $data['bpjs_kenaker_amount'],
                'loan' => $data['loan'],
                'other_deduction' => $data['other_deduction'],
                'tax_amount' => $data['tax_amount'],
                'application' => $data['application'],
                'allowance' => $data['allowance'],
                'total_salary' => $data['allowance'],
                'total_deduction' => $data['total_deduction'],
                'notes' => $data['notes'],
                'created_by' => 'Auto System',
                'saved_date' => date('Y-m-d H:i:s')
            ]);
            DB::commit();

            activity('monthly-history')
                ->causedBy(auth()->user())
                ->log('History has been added for ' . $name);

        } catch (\Throwable $th) {
            DB::rollback();
            activity('monthly-history')
                ->causedBy(auth()->user())
                ->log('Failed to insert monthly history data. Error: ' . $th->getMessage());
        }

    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
