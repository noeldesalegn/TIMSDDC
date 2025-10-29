# Frontend Testing Guide for Tax Management System

## Quick Start: Get Test Data in 3 Steps

### Step 1: Reset and Run Migrations
```bash
php artisan migrate:fresh
```

### Step 2: Seed the Database
```bash
php artisan db:seed
```

### Step 3: Start the Server
```bash
php artisan serve
```

That's it! Now you have test data to see all dashboards working.

---

## Test Users Credentials

### Admin User
- **Email:** `admin@test.com`
- **Password:** `password`
- **Dashboard:** `/admin/dashboard`

### Taxpayer Users (5 total)
1. **Email:** `ahmed@test.com` / **Password:** `password`
2. **Email:** `fatima@test.com` / **Password:** `password`
3. **Email:** `mohammed@test.com` / **Password:** `password`
4. **Email:** `aisha@test.com` / **Password:** `password`
5. **Email:** `hassan@test.com` / **Password:** `password`
- **Dashboard:** `/taxpayer/dashboard`

### Interviewer Users (2 total)
1. **Email:** `interviewer@test.com` / **Password:** `password`
2. **Email:** `sarah@test.com` / **Password:** `password`
- **Dashboard:** `/interviewer/dashboard`

---

## What Test Data Was Created?

### Users (8 total)
- 1 Admin
- 5 Taxpayers  
- 2 Interviewers

### Payments
- 2-5 completed payments per taxpayer
- 0-2 pending payments per taxpayer
- Random amounts between 1,000 - 15,000 ETB

### News (5 items)
- Tax Filing Deadline Reminder
- New Payment Channels Available
- Holiday Office Hours
- Tax Code Updates 2024
- Online Filing Now Available

### Complaints (5 total)
- Various complaint types
- Different statuses (submitted, in_progress, resolved)

---

## Testing the Dashboards

### 1. Admin Dashboard (`/admin/dashboard`)
**What to test:**
- ✅ Statistics cards show real data
  - Total Taxpayers: 5
  - Pending Payments: Variable
  - Recent Complaints: 5
  - Total Revenue: Calculated from completed payments
- ✅ Quick action buttons are visible
- ✅ Recent activity table shows real registrations and payments
- ✅ Revenue trends chart placeholder

**Login as:** `admin@test.com` / `password`

### 2. Taxpayer Dashboard (`/taxpayer/dashboard`)
**What to test:**
- ✅ Tax summary card shows current balance and due date
- ✅ Payment history table shows last 5 payments
- ✅ Quick action buttons work (Make Payment, View Tax Summary, Send Complaint)
- ✅ Recent news shows 3 latest items

**Login as:** Any taxpayer email (e.g., `ahmed@test.com` / `password`)

### 3. Interviewer Dashboard (`/interviewer/dashboard`)
**What to test:**
- ✅ Upload status cards (files uploaded, pending uploads)
- ✅ Schedule overview shows today's appointments
- ✅ Recent uploads list with status
- ✅ Quick action button to upload new file

**Login as:** `interviewer@test.com` / `password`

---

## Quick Commands Reference

### Fresh Start (Delete all data and re-seed)
```bash
php artisan migrate:fresh --seed
```

### Re-seed without deleting
```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=NewsSeeder
php artisan db:seed --class=PaymentSeeder
php artisan db:seed --class=ComplaintSeeder
```

### Check Database
```bash
php artisan tinker
# Then run:
User::count();
Payment::count();
News::count();
Complaint::count();
```

---

## Customizing Test Data

### Add More Users
Edit `database/seeders/UserSeeder.php`

### Add More Payments
Edit `database/seeders/PaymentSeeder.php`

### Add More News
Edit `database/seeders/NewsSeeder.php`

### Add More Complaints
Edit `database/seeders/ComplaintSeeder.php`

---

## Troubleshooting

### Issue: "Table doesn't exist"
**Solution:** Run migrations
```bash
php artisan migrate
```

### Issue: "No data showing"
**Solution:** Run seeders
```bash
php artisan db:seed
```

### Issue: "Can't login"
**Solution:** Reset password or create new user
```bash
# In php artisan tinker
$user = User::where('email', 'admin@test.com')->first();
$user->password = Hash::make('password');
$user->save();
```

### Issue: "Route not found"
**Solution:** Clear cache
```bash
php artisan route:clear
php artisan cache:clear
```

---

## Data to Verify in Dashboards

### Admin Dashboard Should Show:
- Total Taxpayers: **5**
- Pending Payments: **Count of pending payments**
- Recent Complaints: **5** (count) or list in table
- Total Revenue: **Sum of completed payments**
- Recent Activity: Latest 5 taxpayer registrations and payments

### Taxpayer Dashboard Should Show:
- Tax Summary: Current balance and due date
- Payment History: Last 5 payments for that specific taxpayer
- Quick Actions: Working buttons
- Recent News: 3 latest news items

### Interviewer Dashboard Should Show:
- Upload status (will be 0 until implemented)
- Today's schedule (demo data)
- Recent uploads (demo data)

---

## Notes for Frontend Developer

1. **All data is from the database now** - no more session storage for payments/news
2. **Each taxpayer sees only their own data** - payment history is filtered by user
3. **Admin sees all data** - statistics aggregate all users
4. **Interviewer data is still demo** - waiting for backend implementation
5. **You can modify seeders** to change data as needed for testing

---

## Running Individual Seeder Classes

If you want to test specific data in isolation:

```bash
# Seed only users
php artisan db:seed --class=UserSeeder

# Seed only payments
php artisan db:seed --class=PaymentSeeder

# Seed only news
php artisan db:seed --class=NewsSeeder

# Seed only complaints  
php artisan db:seed --class=ComplaintSeeder
```

---

## Tips

1. **Use different browsers/incognito** for testing different user roles simultaneously
2. **Modify seeder numbers** to get more/less test data
3. **Check database directly** using `php artisan tinker`
4. **Keep seeders as your test data source** - modify them as you build features
5. **Don't worry about data persistence** - just re-seed when you need fresh data

---

Happy Testing! 🎉


