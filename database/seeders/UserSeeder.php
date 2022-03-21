<?php

namespace Database\Seeders;

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
        User::create([
            'name' => 'Admin User',
            'phone' => '5454545454',
            'email' => 'adminsuser@chulapi.com',
            'profile' => 'ADMIN',
            'status' => 'ACTIVE',
            'password' => bcrypt('123')
        ]);

        User::create([
                    'name' => 'Employee User',
                    'phone' => '5522525',
                    'email' => 'employeeuser@chulapi.com',
                    'profile' => 'EMPLOYEE',
                    'status' => 'ACTIVE',
                    'password' => bcrypt('123')
                ]);
    }
}
