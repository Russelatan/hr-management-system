# Guide: Disable Seeding & Create Real Admin

## Option 1: Run Migrations Without Seeding (Recommended)

### Step 1: Run migrations only (no seeders)
```bash
php artisan migrate
```

This will:
- ✅ Create all database tables
- ❌ Skip all seeders (no sample data)

### Step 2: Create admin using Artisan command
```bash
php artisan admin:create
```

The command will prompt you for:
- Admin Name
- Email Address
- Password (with confirmation)
- Optional: Phone, Address, Date of Birth, Hire Date

**Example:**
```bash
php artisan admin:create
# Follow the prompts
```

**Or use flags for non-interactive:**
```bash
php artisan admin:create --name="John Admin" --email="admin@company.com" --password="SecurePassword123"
```

---

## Option 2: Use Laravel Tinker

### Step 1: Run migrations
```bash
php artisan migrate
```

### Step 2: Open tinker
```bash
php artisan tinker
```

### Step 3: Create admin user
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Your Admin Name',
    'email' => 'admin@yourcompany.com',
    'password' => Hash::make('YourSecurePassword'),
    'role' => 'admin',
    'employee_id' => null,
    'phone' => '+1-555-0100', // optional
    'address' => 'Your Address', // optional
    'date_of_birth' => '1980-01-01', // optional, format: YYYY-MM-DD
    'hire_date' => '2020-01-01', // optional, format: YYYY-MM-DD
    'employment_status' => 'active',
    'email_verified_at' => now(),
]);
```

### Step 4: Exit tinker
```php
exit
```

---

## Option 3: Modify DatabaseSeeder (Skip Seeders)

### Option 3a: Comment out seeders in DatabaseSeeder
Edit `database/seeders/DatabaseSeeder.php`:

```php
public function run(): void
{
    // Comment out all seeders
    // $this->call([
    //     AdminSeeder::class,
    //     EmployeeSeeder::class,
    //     LeaveBalanceSeeder::class,
    //     PaySlipSeeder::class,
    //     LeaveRequestSeeder::class,
    //     AttendanceRecordSeeder::class,
    // ]);
    
    $this->command->info('Seeders disabled. Use "php artisan admin:create" to create admin.');
}
```

Then run:
```bash
php artisan migrate:fresh --seed
```

### Option 3b: Create admin-only seeder
Create a new seeder that only creates admin:

```bash
php artisan make:seeder AdminOnlySeeder
```

Then modify it to only create admin, and update `DatabaseSeeder` to call only that seeder.

---

## Option 4: Use Registration Route (After First Admin)

If you already have one admin account, you can:
1. Log in as that admin
2. Visit `/admin/register`
3. Create additional admin accounts through the web interface

---

## Quick Reference

### Run migrations WITHOUT seeding:
```bash
php artisan migrate
```

### Create admin interactively:
```bash
php artisan admin:create
```

### Create admin with flags:
```bash
php artisan admin:create --name="Admin Name" --email="admin@example.com" --password="password123"
```

### Check if admin exists:
```bash
php artisan tinker
# Then in tinker:
User::where('role', 'admin')->get(['id', 'name', 'email']);
```

---

## Security Notes

⚠️ **Important:**
- Always use strong passwords (minimum 8 characters)
- Change default passwords immediately
- Never commit admin credentials to version control
- Use environment-specific admin accounts

---

## Troubleshooting

### "User already exists" error
- The email is already in use
- Use a different email or delete the existing user first

### "Password too short" error
- Password must be at least 8 characters
- Use a stronger password

### Can't log in after creating admin
- Verify the email and password are correct
- Check that `role` is set to `'admin'` (not `'employee'`)
- Ensure `email_verified_at` is not null
