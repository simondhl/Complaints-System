<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->create([
          'email' => 'admin@gmail.com',
          'password' => '123sS123##',
          'phone_number' => '0937523553',
          'first_name' => 'admin',
          'last_name' => 'admin',
          'location' => 'Damascus',
          'email_verified_at' => now(),
          'role_id' => '1',
        ]);
        User::query()->create([
          'email' => 'employeeKahraba@gmail.com',
          'password' => '123sS123##',
          'phone_number' => '091238791',
          'first_name' => 'employee',
          'last_name' => 'employee',
          'location' => 'Damascus',
          'email_verified_at' => now(),
          'role_id' => '3',
        ]);
        User::query()->create([
          'email' => 'EmployeeWater@gmail.com',
          'password' => '123sS123##',
          'phone_number' => '0937523553',
          'first_name' => 'employee',
          'last_name' => 'employee',
          'location' => 'Damascus',
          'email_verified_at' => now(),
          'role_id' => '3',
        ]);

        Employee::query()->create([
          'user_id' => '2',
          'government_sector_id' => '1',
          'employee_number' => '10196240',
        ]);
        Employee::query()->create([
          'user_id' => '3',
          'government_sector_id' => '2',
          'employee_number' => '20231594',
        ]);
    }
}
