# 🚀 Quick Start for Frontend Developer

## Run These 3 Commands to Get Test Data

```bash
# 1. Navigate to project
cd TIMSDDC

# 2. Reset database and seed with test data
php artisan migrate:fresh --seed

# 3. Start the server
php artisan serve
```

**Done!** Now go to http://localhost:8000

---

## 📋 Test Credentials

Copy-paste these to test different dashboards:

### Admin Dashboard
```
Email: admin@test.com
Password: password
URL: http://localhost:8000/admin/dashboard
```

### Taxpayer Dashboard (choose any)
```
Email: ahmed@test.com
Password: password
URL: http://localhost:8000/taxpayer/dashboard
```

### Interviewer Dashboard
```
Email: interviewer@test.com
Password: password
URL: http://localhost:8000/interviewer/dashboard
```

---

## 📦 What You Get

✅ **8 test users** (1 admin, 5 taxpayers, 2 interviewers)  
✅ **Sample payments** with random amounts  
✅ **5 news items** about taxes  
✅ **5 complaints** with different statuses  
✅ **All dashboards working** with real data  

---

## 🔄 Need Fresh Data?

Just run:
```bash
php artisan migrate:fresh --seed
```

---

## 📖 More Details

See `TESTING_GUIDE_FRONTEND.md` for complete testing documentation.

---

## ✨ What's Different Now?

- ✅ **Database seeders** provide real test data
- ✅ **Dashboards show real data** from database
- ✅ **No backend needed** - everything works with seeded data
- ✅ **Easy to reset** - just re-seed when you need fresh data
- ✅ **Multiple test users** for testing different roles

Happy coding! 🎉


