<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Deduction;

class DeductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $deductions = [
            [
                'name'  => 'late_morning',
                'value' => 30000,
                'created_by' => 'Administrator'
            ],
            [
                'name'  => 'late_evening',
                'value' => 50000,
                'created_by' => 'Administrator'
            ],
            [
                'name'  => 'late_minutes',
                'value' => 3000,
                'created_by' => 'Administrator'
            ]
        ];

        foreach ($deductions as $deduct) {
            Deduction::create($deduct);
        }
    }
}
