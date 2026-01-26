<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create 
                            {--name= : Admin name}
                            {--email= : Admin email}
                            {--password= : Admin password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user account';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Creating Admin User...');
        $this->newLine();

        // Get or prompt for name
        $name = $this->option('name') ?: $this->ask('Admin Name');

        // Get or prompt for email
        $email = $this->option('email') ?: $this->ask('Email Address');

        // Validate email
        $validator = Validator::make(['email' => $email], [
            'email' => ['required', 'email', 'unique:users,email'],
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('  - ' . $error);
            }
            return Command::FAILURE;
        }

        // Check if email already exists
        if (User::where('email', $email)->exists()) {
            $this->error("User with email '{$email}' already exists!");
            return Command::FAILURE;
        }

        // Get or prompt for password
        $password = $this->option('password') ?: $this->secret('Password');

        // Confirm password
        if (!$this->option('password')) {
            $passwordConfirmation = $this->secret('Confirm Password');
            if ($password !== $passwordConfirmation) {
                $this->error('Passwords do not match!');
                return Command::FAILURE;
            }
        }

        // Validate password
        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters long!');
            return Command::FAILURE;
        }

        // Get optional fields
        $phone = $this->ask('Phone Number (optional)', null);
        $address = $this->ask('Address (optional)', null);
        $dateOfBirth = $this->ask('Date of Birth (YYYY-MM-DD, optional)', null);
        $hireDate = $this->ask('Hire Date (YYYY-MM-DD, optional)', null);

        // Create admin user
        try {
            $admin = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'employee_id' => null,
                'phone' => $phone,
                'address' => $address,
                'date_of_birth' => $dateOfBirth,
                'hire_date' => $hireDate,
                'employment_status' => 'active',
                'email_verified_at' => now(),
            ]);

            $this->newLine();
            $this->info('✅ Admin user created successfully!');
            $this->newLine();
            $this->table(
                ['Field', 'Value'],
                [
                    ['ID', $admin->id],
                    ['Name', $admin->name],
                    ['Email', $admin->email],
                    ['Role', $admin->role],
                    ['Status', $admin->employment_status],
                ]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to create admin user: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
