<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 15 sample employees
        $employees = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@hrsystem.com',
                'employee_id' => 'EMP0001',
                'phone' => '+1-555-1001',
                'address' => '123 Main St, City, State 12345',
                'date_of_birth' => '1990-05-15',
                'hire_date' => '2022-01-15',
                'employment_status' => 'active',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@hrsystem.com',
                'employee_id' => 'EMP0002',
                'phone' => '+1-555-1002',
                'address' => '456 Oak Ave, City, State 12345',
                'date_of_birth' => '1988-08-22',
                'hire_date' => '2021-06-01',
                'employment_status' => 'active',
            ],
            [
                'name' => 'Michael Johnson',
                'email' => 'michael.johnson@hrsystem.com',
                'employee_id' => 'EMP0003',
                'phone' => '+1-555-1003',
                'address' => '789 Pine Rd, City, State 12345',
                'date_of_birth' => '1992-11-10',
                'hire_date' => '2023-03-10',
                'employment_status' => 'active',
            ],
            [
                'name' => 'Sarah Williams',
                'email' => 'sarah.williams@hrsystem.com',
                'employee_id' => 'EMP0004',
                'phone' => '+1-555-1004',
                'address' => '321 Elm St, City, State 12345',
                'date_of_birth' => '1985-03-25',
                'hire_date' => '2020-09-01',
                'employment_status' => 'active',
            ],
            [
                'name' => 'David Brown',
                'email' => 'david.brown@hrsystem.com',
                'employee_id' => 'EMP0005',
                'phone' => '+1-555-1005',
                'address' => '654 Maple Dr, City, State 12345',
                'date_of_birth' => '1991-07-18',
                'hire_date' => '2022-11-15',
                'employment_status' => 'active',
            ],
        ];

        foreach ($employees as $employee) {
            User::firstOrCreate(
                ['email' => $employee['email']],
                array_merge($employee, [
                    'password' => bcrypt('password123'),
                    'role' => 'employee',
                    'email_verified_at' => now(),
                ])
            );
        }

        // Create additional random employees using factory
        User::factory()
            ->count(10)
            ->employee()
            ->create();

        $this->command->info('Created 15 sample employees (5 predefined + 10 random)');
        $this->command->info('Default password for all employees: password123');
    }
}
