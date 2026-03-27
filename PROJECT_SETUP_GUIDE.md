# Stroke Rehabilitation FYP - Complete Setup Guide

## Project Overview
Intelligent Post Stroke Patient Rehabilitation Plan Generator with role-based dashboards (Admin, Clinician, Patient) and ML-powered recovery predictions.

## Architecture
- **Frontend**: Laravel 13 + FilamentPHP (Admin Panel) + Blade Templates (Clinician/Patient Dashboards)
- **Backend**: Laravel API + MySQL Database
- **ML Service**: FastAPI Microservice (Python)
- **Database**: MySQL

---

## Part 1: Laravel Web Application Setup

### Prerequisites
- PHP 8.3+
- Composer
- MySQL
- Node.js & npm

### Installation Steps

#### 1. Navigate to Project Directory
```bash
cd "c:\Users\dharr\Desktop\University Files\Uni Sem 8\CAT405\FYP\stroke-rehab-app"
```

#### 2. Install Dependencies (Already Done)
```bash
composer install
```

#### 3. Configure Database (Already Done)
Update `.env` file with MySQL credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stroke_rehab_db
DB_USERNAME=root
DB_PASSWORD=
```

#### 4. Run Migrations (Already Done)
```bash
php artisan migrate
```

#### 5. Seed Initial Data (Already Done)
```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=ExerciseSeeder
```

#### 6. Start Laravel Server
```bash
php artisan serve
```
Server runs on: `http://127.0.0.1:8000`

---

## Part 2: FastAPI ML Microservice Setup

### Prerequisites
- Python 3.8+
- pip

### Installation Steps

#### 1. Navigate to ML Service Directory
```bash
cd "c:\Users\dharr\Desktop\University Files\Uni Sem 8\CAT405\FYP\ml_service"
```

#### 2. Create Virtual Environment
```bash
python -m venv venv
venv\Scripts\activate
```

#### 3. Install Dependencies
```bash
pip install -r requirements.txt
```

#### 4. Add Your ML Model (Optional)
Place your `stroke_recovery_model.joblib` file in:
```
ml_service/models/stroke_recovery_model.joblib
```

If not provided, the service runs in **demo mode** with simulated predictions.

#### 5. Start FastAPI Service
```bash
python main.py
```
Service runs on: `http://localhost:8001`

---

## Test Credentials

### Admin Account
- **Email**: admin@rehab.local
- **Password**: password
- **Access**: Admin panel at `/admin`

### Clinician Account
- **Email**: clinician@rehab.local
- **Password**: password
- **Access**: Clinician dashboard at `/clinician/dashboard`

### Patient Accounts
- **Patient 1 Email**: patient1@rehab.local
- **Patient 2 Email**: patient2@rehab.local
- **Password**: password (both)
- **Access**: Patient dashboard at `/patient/dashboard`

---

## Application Features

### Admin Dashboard (FilamentPHP)
- Manage patients, clinicians, exercises, and rehabilitation plans
- View system analytics
- User management

### Clinician Dashboard
- **Patient Overview**: View assigned patients with recovery status
- **Plan Generator**: 
  - Step 1: Input patient data
  - Step 2: AI recommends difficulty level based on ML prediction
  - Step 3: Add exercises to plan
  - Step 4: Publish plan to patient
- **Patient Management**: View patient details and medical history

### Patient Dashboard
- **View Active Plan**: See assigned rehabilitation plan
- **Weekly Schedule**: Calendar view of exercises by day
- **Exercise Feedback**: Submit pain level, difficulty, mood, and comments
- **Exercise Tracking**: Mark exercises as completed

---

## Database Schema

### Core Tables
1. **users** - Authentication and user roles
2. **patients** - Patient information and medical history
3. **clinicians** - Clinician details and specialization
4. **rehab_plans** - Rehabilitation plans with ML metadata
5. **exercises** - Exercise library with difficulty levels
6. **plan_exercises** - Junction table linking plans to exercises
7. **patient_feedback** - Patient feedback on exercises

---

## API Integration

### ML Service Endpoints

#### Health Check
```
GET http://localhost:8001/
GET http://localhost:8001/health
```

#### Single Patient Prediction
```
POST http://localhost:8001/predict
Content-Type: application/json

{
  "age": 70,
  "stroke_type": "ischemic",
  "deficit_area": "leg",
  "medical_history": "Hypertension, Diabetes Type 2"
}
```

**Response:**
```json
{
  "recovery_probability": 0.65,
  "difficulty_level": 3,
  "recommended_exercises": ["Leg Lifts", "Walking with Support"],
  "confidence_score": 0.80
}
```

---

## Workflow Example

### Creating a Rehabilitation Plan

1. **Clinician logs in** → `/clinician/dashboard`
2. **View patients** → Click "View" on patient
3. **Create plan** → Click "Create New Plan"
4. **AI recommendations appear** (if ML service is running)
   - Shows recovery probability
   - Suggests difficulty level
   - Recommends exercises
5. **Clinician adjusts** → Can override AI suggestions
6. **Add exercises** → Select exercises for each day
7. **Publish plan** → Plan becomes active for patient
8. **Patient receives plan** → Views in `/patient/dashboard`
9. **Patient completes exercises** → Submits feedback
10. **Clinician monitors progress** → Reviews feedback and adjusts plan

---

## Troubleshooting

### Laravel Issues

**"Route [login] not defined"**
- Ensure routes are properly registered in `routes/web.php`
- Clear route cache: `php artisan route:clear`

**"No application encryption key"**
- Run: `php artisan key:generate`

**Database connection errors**
- Verify MySQL is running
- Check `.env` database credentials
- Run: `php artisan migrate`

### FastAPI Issues

**"Connection refused" when calling ML service**
- Ensure FastAPI service is running: `python main.py`
- Check service is on `http://localhost:8001`
- Verify firewall allows port 8001

**"Model not found" warning**
- This is normal if `.joblib` file not provided
- Service runs in demo mode with simulated predictions

---

## File Structure

```
FYP/
├── stroke-rehab-app/          # Laravel Application
│   ├── app/
│   │   ├── Models/            # Eloquent Models
│   │   ├── Controllers/       # Route Controllers
│   │   ├── Services/          # Business Logic (MLPredictionService)
│   │   └── Http/Middleware/   # Role-based Middleware
│   ├── resources/views/       # Blade Templates
│   ├── routes/                # Web Routes
│   ├── database/
│   │   ├── migrations/        # Database Migrations
│   │   └── seeders/           # Database Seeders
│   ├── .env                   # Environment Configuration
│   └── composer.json          # PHP Dependencies
│
└── ml_service/                # FastAPI Microservice
    ├── main.py                # FastAPI Application
    ├── requirements.txt       # Python Dependencies
    ├── models/                # ML Models Directory
    │   └── stroke_recovery_model.joblib (to be added)
    └── README.md              # ML Service Documentation
```

---

## Next Steps

1. **Test the application** with provided credentials
2. **Add your ML model** to `ml_service/models/stroke_recovery_model.joblib`
3. **Customize exercises** in the admin panel
4. **Create test rehabilitation plans** as a clinician
5. **Test patient dashboard** and feedback submission
6. **Deploy to production** when ready

---

## Support

For issues or questions:
1. Check the troubleshooting section
2. Review logs: `storage/logs/laravel.log`
3. Verify all services are running (Laravel + FastAPI)
4. Ensure database is properly configured

---

## Project Status

✅ Database schema and models
✅ Role-based authentication
✅ Admin dashboard (FilamentPHP)
✅ Clinician dashboard with plan generator
✅ Patient dashboard with schedule and feedback
✅ FastAPI ML microservice
✅ ML integration in plan creation
✅ Exercise library with difficulty levels
✅ Patient feedback tracking

🔄 Ready for testing and ML model integration
