<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $roles = [
        //     [
        //         'name' => 'Super Admin',
        //         'guard_name' => 'web',
        //     ],
        //     [
        //         'name' => 'Admin',
        //         'guard_name' => 'web',
        //     ],
        //     [
        //         'name' => 'Viewer',
        //         'guard_name' => 'web',
        //     ]
        // ];

        $permissions = [
            'Create User',
            'Edit User',
            'View User',
            'Delete User'
        ];

        $permissionsAdmin = [
            'Create User',
            'Edit User',
            'View User',
        ];
        
        $permissionsViewer = ['View User'];

        foreach (\Spatie\Permission\Models\Role::get() as $role) {
            switch ($role->name) {
                case 'Super Admin':
                    $role->givePermissionTo($permissions);
                    break;
                
                case 'Admin':
                    $role->givePermissionTo($permissionsAdmin);
                    break;
                
                default:
                    $role->givePermissionTo($permissionsViewer);
                    break;
            }
        }


        foreach (\App\Models\User::get() as $u) {
            if ($u->id == 1) {
                $u->assignRole('Super Admin');
            } else {
                $u->assignRole('Admin');
            }
        }
    }
}
