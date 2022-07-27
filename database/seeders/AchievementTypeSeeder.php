<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AchievementType;

class AchievementTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            [
                'type'          => 'A',
                'percentage'    => 100,
                'top_limit'     => 200,
                'bottom_limit'  => 51,
                'created_by'    => 'Administrator',
            ],
            [
                'type'          => 'B',
                'percentage'    => 75,
                'top_limit'     => 50,
                'bottom_limit'  => 41,
                'created_by'    => 'Administrator',
            ],
            [
                'type'          => 'C',
                'percentage'    => 50,
                'top_limit'     => 40,
                'bottom_limit'  => 21,
                'created_by'    => 'Administrator',
            ],
            [
                'type'          => 'D',
                'percentage'    => 35,
                'top_limit'     => 20,
                'bottom_limit'  => 0,
                'created_by'    => 'Administrator',
            ],
        ];

        foreach ($types as $type) {
            AchievementType::create($type);
        }
    }
}
