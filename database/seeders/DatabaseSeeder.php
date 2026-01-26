<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            EmployeeSeeder::class,
            LeaveBalanceSeeder::class,
            PaySlipSeeder::class,
            LeaveRequestSeeder::class,
            AttendanceRecordSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('  Database seeding completed successfully!');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('Admin Login Credentials:');
        $this->command->info('  Email: admin@hrsystem.com');
        $this->command->info('  Password: admin123');
        $this->command->info('');
        $this->command->info('Employee Login Credentials (sample):');
        $this->command->info('  Email: john.doe@hrsystem.com');
        $this->command->info('  Password: password123');
        $this->command->info('');
        $this->command->info('All employees use password: password123');
        $this->command->info('');
    }
}
