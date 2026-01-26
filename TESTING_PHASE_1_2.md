# Testing Guide - Phase 1 & 2

## Phase 1: Authentication & Authorization - Testing Checklist

### ✅ Code Structure Verified
- [x] LoginController created with showLoginForm, login, and logout methods
- [x] RegisterController created with showRegistrationForm and register methods
- [x] EnsureUserIsAdmin middleware created and registered
- [x] EnsureUserIsEmployee middleware created and registered
- [x] Routes properly configured:
  - GET `/login` → Login form
  - POST `/login` → Login handler
  - POST `/logout` → Logout handler
  - GET `/admin/register` → Registration form (admin only)
  - POST `/admin/register` → Registration handler (admin only)
- [x] Middleware aliases registered in `bootstrap/app.php`
- [x] User model updated with role and employee fields
- [x] Auth views created (`login.blade.php`, `register.blade.php`)

### 🧪 Manual Testing Steps

1. **Test Database Connection**
   ```bash
   php artisan migrate:status
   ```
   - ⚠️ **Note**: Currently getting PostgreSQL connection error. Ensure:
     - PostgreSQL is running
     - Database `hr_management_system` exists
     - `.env` has correct DB credentials

2. **Run Migrations**
   ```bash
   php artisan migrate
   ```
   This will create:
   - Extended users table with role, employee_id, phone, address, etc.
   - Sessions table (already exists)
   - Password reset tokens table (already exists)

3. **Test Login Page**
   - Start server: `php artisan serve`
   - Visit: `http://localhost:8000/login`
   - Should see login form
   - Try logging in (will fail until users exist)

4. **Test Registration (Admin Only)**
   - Visit: `http://localhost:8000/admin/register`
   - Should redirect to login (no auth)
   - After creating admin user, login and try again
   - Should see registration form

5. **Test Middleware Protection**
   - Try accessing `/admin/register` without login → Should redirect
   - Login as employee → Try `/admin/register` → Should get 403 error
   - Login as admin → Try `/admin/register` → Should work

## Phase 2: Database Migrations - Testing Checklist

### ✅ Migration Files Created
- [x] `add_role_and_employee_fields_to_users_table.php`
- [x] `create_pay_slips_table.php`
- [x] `create_leave_requests_table.php`
- [x] `create_leave_balances_table.php`
- [x] `create_attendance_records_table.php`

### 🧪 Testing Steps

1. **Check Migration Status**
   ```bash
   php artisan migrate:status
   ```

2. **Run Migrations**
   ```bash
   php artisan migrate
   ```
   Expected output:
   - Users table extended with new columns
   - Pay slips table created
   - Leave requests table created
   - Leave balances table created
   - Attendance records table created

3. **Verify Database Schema**
   ```bash
   php artisan db:show
   ```
   Or check directly in PostgreSQL:
   ```sql
   \d users
   \d pay_slips
   \d leave_requests
   \d leave_balances
   \d attendance_records
   ```

4. **Test Rollback (Optional)**
   ```bash
   php artisan migrate:rollback --step=5
   php artisan migrate
   ```

## Phase 3: Models & Relationships - Testing Checklist

### ✅ Models Created
- [x] PaySlip model with relationships
- [x] LeaveRequest model with relationships
- [x] LeaveBalance model with relationships
- [x] AttendanceRecord model with relationships
- [x] User model updated with relationships and helper methods

### 🧪 Testing Steps

1. **Test Model Relationships**
   ```bash
   php artisan tinker
   ```
   Then test:
   ```php
   $user = App\Models\User::first();
   $user->paySlips; // Should return collection
   $user->leaveRequests; // Should return collection
   $user->leaveBalances; // Should return collection
   $user->attendanceRecords; // Should return collection
   $user->isAdmin(); // Should return boolean
   $user->isEmployee(); // Should return boolean
   ```

## Current Issues to Resolve

1. **Database Connection**: PostgreSQL authentication failing
   - Check `.env` file for correct credentials
   - Ensure PostgreSQL service is running
   - Verify database `hr_management_system` exists

## Next Steps After Testing

Once Phase 1 & 2 are verified working:
- Phase 4: Admin Dashboard & Features
- Phase 5: Employee Dashboard & Features
- Phase 6: UI Components & Layout
- Phase 7: File Storage
- Phase 8: Seeders & Factories
