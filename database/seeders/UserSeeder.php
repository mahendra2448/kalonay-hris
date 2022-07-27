<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Super HR',
                'email'=> 'super@hr.com',
                'password'=> \Hash::make('depok2022'),
                'created_by'=> 'Administrator'
            ],
            [
                'name' => 'Admin HR',
                'email'=> 'admin@hr.com',
                'password'=> \Hash::make('depok2022'),
                'created_by'=> 'Administrator'
            ]
        ];
        foreach ($users as $u) {
            User::create($u);
        }
    }
}
