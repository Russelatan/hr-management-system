<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        User::firstOrCreate(
            ['email' => 'admin@hrsystem.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'employee_id' => null,
                'phone' => '+1-555-0100',
                'address' => '123 Admin Street, Admin City',
                'date_of_birth' => '1980-01-01',
                'hire_date' => '2020-01-01',
                'employment_status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user created: admin@hrsystem.com / admin123');
    }
}
