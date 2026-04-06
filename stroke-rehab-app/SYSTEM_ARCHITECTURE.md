# Stroke Rehabilitation Web Application - System Architecture

## Overview
The Stroke Rehabilitation Web Application is a comprehensive healthcare management system designed to facilitate rehabilitation planning and patient progress tracking. The system integrates machine learning capabilities for intelligent exercise recommendations based on patient clinical data.

---

## System Architecture Layers

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          PRESENTATION LAYER (Frontend)                       │
│  ┌──────────────────────┐  ┌──────────────────────┐  ┌──────────────────┐  │
│  │  Clinician Portal    │  │  Patient Portal      │  │  Admin Dashboard │  │
│  │  (Blade Templates)   │  │  (Blade Templates)   │  │  (Blade Templates)│  │
│  └──────────────────────┘  └──────────────────────┘  └──────────────────┘  │
│                                                                               │
│  Technologies: Laravel Blade, Tailwind CSS, JavaScript                       │
└─────────────────────────────────────────────────────────────────────────────┘
                                    ▲
                                    │ HTTP/AJAX
                                    ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                      APPLICATION LAYER (Backend)                             │
│                                                                               │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │                    Laravel Framework (PHP)                          │   │
│  │                                                                     │   │
│  │  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐ │   │
│  │  │   Controllers    │  │     Routes       │  │   Middleware     │ │   │
│  │  │                  │  │                  │  │                  │ │   │
│  │  │ • AuthController │  │ • web.php        │  │ • Auth           │ │   │
│  │  │ • PatientMgmt    │  │ • API routes     │  │ • Patient        │ │   │
│  │  │ • PlanGenerator  │  │ • Clinician      │  │ • Clinician      │ │   │
│  │  │ • Dashboard      │  │   routes         │  │ • Role-based     │ │   │
│  │  │ • Feedback       │  │                  │  │                  │ │   │
│  │  └──────────────────┘  └──────────────────┘  └──────────────────┘ │   │
│  │                                                                     │   │
│  │  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐ │   │
│  │  │     Models       │  │    Services      │  │  Validations     │ │   │
│  │  │                  │  │                  │  │                  │ │   │
│  │  │ • User           │  │ • MLPrediction   │  │ • Form Rules     │ │   │
│  │  │ • Patient        │  │   Service        │  │ • Request        │ │   │
│  │  │ • RehabPlan      │  │ • Auth Service   │  │   Validation     │ │   │
│  │  │ • Exercise       │  │ • Mail Service   │  │                  │ │   │
│  │  │ • PlanExercise   │  │                  │  │                  │ │   │
│  │  │ • PatientFeedback│  │                  │  │                  │ │   │
│  │  │ • ClinicianMsg   │  │                  │  │                  │ │   │
│  │  └──────────────────┘  └──────────────────┘  └──────────────────┘ │   │
│  │                                                                     │   │
│  └─────────────────────────────────────────────────────────────────────┘   │
│                                                                               │
│  Technologies: Laravel 10+, PHP 8.1+, Eloquent ORM                          │
└─────────────────────────────────────────────────────────────────────────────┘
                    ▲                                    ▲
                    │ Database Queries                   │ HTTP Requests
                    ▼                                    ▼
┌──────────────────────────────────┐  ┌──────────────────────────────────────┐
│   DATABASE LAYER                 │  │  INTELLIGENCE ENGINE LAYER           │
│                                  │  │                                      │
│  ┌────────────────────────────┐  │  │  ┌──────────────────────────────┐   │
│  │    MySQL Database          │  │  │  │  ML Prediction Service       │   │
│  │                            │  │  │  │  (Python Flask/FastAPI)      │   │
│  │  Tables:                   │  │  │  │                              │   │
│  │  • users                   │  │  │  │  ┌────────────────────────┐  │   │
│  │  • patients                │  │  │  │  │ IST Dataset Analysis   │  │   │
│  │  • rehab_plans             │  │  │  │ │ • Age                  │  │   │
│  │  • exercises               │  │  │  │ │ • Gender               │  │   │
│  │  • plan_exercises          │  │  │  │ │ • RSBP                 │  │   │
│  │  • patient_feedback        │  │  │  │ │ • Stroke Subtype       │  │   │
│  │  • clinician_messages      │  │  │  │ │ • Consciousness State  │  │   │
│  │                            │  │  │  │ │ • Deficits (rdef1-8)   │  │   │
│  │  Relationships:            │  │  │  │ └────────────────────────┘  │   │
│  │  • User ← → Patient        │  │  │  │                              │   │
│  │  • Patient ← → RehabPlan   │  │  │  │ ┌────────────────────────┐  │   │
│  │  • RehabPlan ← → Exercise  │  │  │  │ │ ML Models              │  │   │
│  │  • Patient ← → Feedback    │  │  │  │ │ • Recovery Prediction  │  │   │
│  │                            │  │  │  │ │ • Exercise Recommend   │  │   │
│  │  Indexes:                  │  │  │  │ │ • Confidence Scoring   │  │   │
│  │  • user_id, clinician_id   │  │  │  │ └────────────────────────┘  │   │
│  │  • patient_id, plan_id     │  │  │  │                              │   │
│  │  • exercise_id             │  │  │  │ ┌────────────────────────┐  │   │
│  │                            │  │  │  │ │ Output Generation      │  │   │
│  │                            │  │  │  │ │ • Recommended Exercises│  │   │
│  │                            │  │  │  │ │ • Recovery Probability │  │   │
│  │                            │  │  │  │ │ • Confidence Score     │  │   │
│  │                            │  │  │  │ │ • Safety Notes         │  │   │
│  │                            │  │  │  │ └────────────────────────┘  │   │
│  └────────────────────────────┘  │  │  │                              │   │
│                                  │  │  │  Port: 8001                  │   │
│  Port: 3306                      │  │  │  Protocol: HTTP/JSON         │   │
│  Protocol: MySQL                 │  │  │                              │   │
│                                  │  │  │  Technologies:               │   │
│                                  │  │  │  • Python 3.8+              │   │
│                                  │  │  │  • Flask/FastAPI            │   │
│                                  │  │  │  • Scikit-learn             │   │
│                                  │  │  │  • Pandas/NumPy             │   │
│                                  │  │  │  • TensorFlow/PyTorch       │   │
│                                  │  │  └──────────────────────────────┘   │
└──────────────────────────────────┘  └──────────────────────────────────────┘
```

---

## Detailed Component Architecture

### 1. PRESENTATION LAYER (Frontend)

#### 1.1 Clinician Portal
**Views & Components:**
- Dashboard (`clinician/dashboard.blade.php`)
  - Statistics overview
  - Messages section
  - Appointment reminders
  
- Patient Management (`clinician/patients/`)
  - Patient list with search
  - Create new patient
  - Edit patient clinical info
  - Patient details view
  
- Rehabilitation Plans (`clinician/plans/`)
  - Plan list
  - Create plan with ML recommendations
  - Edit and customize exercises
  - Schedule management
  
- Profile Management (`clinician/profile.blade.php`)
  - Personal information
  - Password change

**Layout:** `layouts/clinician.blade.php`
- Sidebar navigation
- Top bar with user info and profile link
- Responsive design with Tailwind CSS

#### 1.2 Patient Portal
**Views & Components:**
- Dashboard (`patient/dashboard.blade.php`)
  - Active plan overview
  - Next 24-hour exercises
  
- Schedule (`patient/schedule.blade.php`)
  - Weekly calendar view
  - Time-slot based schedule
  - Exercise details
  
- Details (`patient/details.blade.php`)
  - Personal information
  - Medical information
  - Functional deficits
  
- Appointments (`patient/appointments.blade.php`)
  - Appointment list
  
- Profile Management (`patient/profile.blade.php`)
  - Personal information
  - Password change

**Layout:** `layouts/patient.blade.php`
- Sidebar navigation
- Top bar with user info and profile link
- Responsive design with Tailwind CSS

#### 1.3 Technologies
- **Framework:** Laravel Blade Templating
- **Styling:** Tailwind CSS
- **Interactivity:** JavaScript (Vanilla JS, AJAX)
- **Icons:** Lucide Icons (SVG)
- **Responsive Design:** Mobile-first approach

---

### 2. APPLICATION LAYER (Backend)

#### 2.1 Controllers

**Authentication & Authorization**
- `AuthController` - User login, registration, logout

**Clinician Functions**
- `Clinician\DashboardController`
  - Dashboard overview
  - Profile management
  - Message management
  - Appointment viewing
  
- `Clinician\PatientManagementController`
  - Create patient accounts
  - Assign/remove patients
  - Edit patient clinical information
  - Search patients
  
- `Clinician\PlanGeneratorController`
  - Create rehabilitation plans
  - Add/remove exercises
  - Update exercise details
  - Publish plans
  - Delete plans

**Patient Functions**
- `Patient\DashboardController`
  - Dashboard display
  - Schedule building
  - Patient details
  - Appointments
  - Feedback submission
  - Profile management

#### 2.2 Models (Eloquent ORM)

```
User
├── Patient (hasOne)
├── RehabPlans (hasMany) - as clinician
├── ClinicianMessages (hasMany)
└── PatientProfiles (hasMany)

Patient
├── User (belongsTo)
├── Clinician (belongsTo) - User model
├── RehabPlans (hasMany)
├── PatientFeedback (hasMany)
└── ClinicianMessages (hasMany)

RehabPlan
├── Patient (belongsTo)
├── Clinician (belongsTo) - User model
└── PlanExercises (hasMany)

Exercise
└── PlanExercises (hasMany)

PlanExercise
├── RehabPlan (belongsTo)
├── Exercise (belongsTo)
└── PatientFeedback (hasMany)

PatientFeedback
├── Patient (belongsTo)
└── PlanExercise (belongsTo)

ClinicianMessage
├── Clinician (belongsTo) - User model
└── Patient (belongsTo)
```

#### 2.3 Routes

**Authentication Routes**
```
POST   /login                 - User login
POST   /logout                - User logout
GET    /register              - Registration form
POST   /register              - Store registration
```

**Clinician Routes** (Prefix: `/clinician`)
```
GET    /dashboard             - Dashboard
GET    /profile               - Profile page
PUT    /profile/update        - Update profile
PUT    /profile/change-password - Change password

GET    /patients              - Patient list
POST   /patients              - Create patient
GET    /patients/{id}/edit    - Edit patient form
PUT    /patients/{id}         - Update patient
DELETE /patients/{id}         - Remove patient
GET    /patients/{id}         - Show patient details

GET    /plans                 - Plan list
GET    /plans/create/{patientId} - Create plan form
POST   /plans/{patientId}     - Store plan
GET    /plans/{id}/edit       - Edit plan
PUT    /plans/{id}/exercise   - Add exercise
PUT    /plans/{id}/exercise/{exerciseId} - Update exercise
DELETE /plans/{id}/exercise/{exerciseId} - Remove exercise
DELETE /plans/{id}            - Delete plan
PUT    /plans/{id}/publish    - Publish plan

DELETE /messages/{id}         - Delete message

GET    /appointments          - Appointment list
```

**Patient Routes** (Prefix: `/patient`)
```
GET    /dashboard             - Dashboard
GET    /details               - Patient details
GET    /schedule              - Exercise schedule
GET    /appointments          - Appointments
POST   /feedback/{exerciseId} - Submit feedback

GET    /profile               - Profile page
PUT    /profile/update        - Update profile
PUT    /profile/change-password - Change password
```

#### 2.4 Services

**MLPredictionService**
- `predictRecoveryWithISTData(array $clinicalData)` - Get ML predictions
- `isServiceAvailable()` - Check ML service health
- `getServiceStatus()` - Get service status

#### 2.5 Middleware
- `Authenticate` - Verify user is logged in
- `Patient` - Verify user is patient role
- `Clinician` - Verify user is clinician role
- `VerifyCsrfToken` - CSRF protection

#### 2.6 Validation Rules
- User registration and login validation
- Patient clinical data validation
- Plan creation and exercise assignment validation
- Feedback submission validation
- Profile update validation

#### 2.7 Technologies
- **Framework:** Laravel 10+
- **Language:** PHP 8.1+
- **ORM:** Eloquent
- **Database Driver:** MySQL
- **Authentication:** Laravel Auth
- **Validation:** Laravel Validator
- **HTTP Client:** Laravel HTTP Client

---

### 3. DATABASE LAYER

#### 3.1 Database Schema

**users table**
```sql
id (PK)
name
email (UNIQUE)
password
role (patient, clinician, admin)
created_at
updated_at
```

**patients table**
```sql
id (PK)
user_id (FK → users)
clinician_id (FK → users)
age
gender (0=Female, 1=Male)
rsbp (Systolic Blood Pressure)
stroke_subtype (TACS, PACS, LACS, POCS, OTH)
conscious_state (Alert, Drowsy, Unconscious)
recovery_status (new, in_progress, completed, paused)
rdef1 - rdef8 (Rehabilitation Deficits - boolean)
created_at
updated_at
```

**rehab_plans table**
```sql
id (PK)
patient_id (FK → patients)
clinician_id (FK → users)
plan_name
description
recovery_probability (0-1)
ml_confidence_score (0-1)
difficulty_level (1-5)
start_date
end_date
status (draft, active, completed)
ml_metadata (JSON)
created_at
updated_at
```

**exercises table**
```sql
id (PK)
name
description
difficulty_level (1-5)
target_area
duration_minutes
repetitions
instructions
image_url
video_url
created_at
updated_at
```

**plan_exercises table**
```sql
id (PK)
rehab_plan_id (FK → rehab_plans)
exercise_id (FK → exercises)
day_of_week (Monday-Sunday)
frequency_per_week (1-7)
scheduled_time (HH:mm)
scheduled_times (JSON)
custom_repetitions
custom_duration_minutes
notes
is_completed (boolean)
created_at
updated_at
```

**patient_feedback table**
```sql
id (PK)
patient_id (FK → patients)
plan_exercise_id (FK → plan_exercises)
pain_level (0-10)
difficulty_rating (1-5)
mood_rating (1-5)
comments
completed_exercise (boolean)
feedback_date
created_at
updated_at
```

**clinician_messages table**
```sql
id (PK)
clinician_id (FK → users)
patient_id (FK → patients, nullable)
message (TEXT)
type (success, warning, error, info)
created_at
updated_at
```

#### 3.2 Indexes
- `users.email` - UNIQUE
- `patients.user_id` - FK
- `patients.clinician_id` - FK
- `rehab_plans.patient_id` - FK
- `rehab_plans.clinician_id` - FK
- `plan_exercises.rehab_plan_id` - FK
- `plan_exercises.exercise_id` - FK
- `patient_feedback.patient_id` - FK
- `patient_feedback.plan_exercise_id` - FK
- `clinician_messages.clinician_id` - FK
- `clinician_messages.patient_id` - FK

#### 3.3 Technologies
- **DBMS:** MySQL 8.0+
- **Connection:** PDO (PHP Data Objects)
- **ORM:** Eloquent
- **Migrations:** Laravel Migrations

---

### 4. INTELLIGENCE ENGINE LAYER

#### 4.1 ML Service Architecture

**Service Type:** RESTful API (Python-based)

**Endpoints:**
```
POST /predict
  Input: Clinical data (IST features)
  Output: Recovery prediction, exercise recommendations

GET /health
  Output: Service status

GET /
  Output: Service information
```

#### 4.2 Input Data (IST Clinical Features)
```json
{
  "age": integer,
  "gender": 0|1,
  "rsbp": integer,
  "stroke_subtype": "TACS|PACS|LACS|POCS|OTH",
  "conscious_state": "Alert|Drowsy|Unconscious",
  "rdef1": boolean,
  "rdef2": boolean,
  "rdef3": boolean,
  "rdef4": boolean,
  "rdef5": boolean,
  "rdef6": boolean,
  "rdef7": boolean,
  "rdef8": boolean
}
```

#### 4.3 Output Data
```json
{
  "recovery_probability": 0.0-1.0,
  "confidence_score": 0.0-1.0,
  "recommended_exercises": [
    {
      "name": "Exercise Name",
      "frequency_per_week": 1-7,
      "progression_reps": "3 sets of 10 reps",
      "safety_notes": "Safety precautions"
    }
  ],
  "risk_factors": ["factor1", "factor2"],
  "recommendations": ["recommendation1"]
}
```

#### 4.4 ML Models
- **Recovery Prediction Model:** Predicts recovery probability based on IST dataset
- **Exercise Recommendation Model:** Suggests appropriate exercises based on deficits
- **Confidence Scoring:** Provides confidence metrics for predictions

#### 4.5 Technologies
- **Language:** Python 3.8+
- **Framework:** Flask or FastAPI
- **ML Libraries:** Scikit-learn, TensorFlow, PyTorch
- **Data Processing:** Pandas, NumPy
- **Deployment:** Docker, Gunicorn
- **Port:** 8001

#### 4.6 Integration Points
- Called from `MLPredictionService` in Laravel backend
- Triggered during plan creation
- Provides exercise recommendations
- Calculates recovery probability

---

## Data Flow Diagrams

### 4.1 User Authentication Flow
```
User (Browser)
    │
    ├─→ POST /login
    │
    └─← Session/Token
    
User (Authenticated)
    │
    ├─→ GET /clinician/dashboard (if clinician)
    │   └─→ Controller → Model → Database
    │   └─← View (Blade Template)
    │
    └─← Rendered HTML
```

### 4.2 Rehabilitation Plan Creation Flow
```
Clinician (Browser)
    │
    ├─→ GET /clinician/plans/create/{patientId}
    │   └─← Form View
    │
    ├─→ POST /clinician/plans/{patientId}
    │   │
    │   ├─→ PlanGeneratorController
    │   │
    │   ├─→ MLPredictionService
    │   │   │
    │   │   └─→ HTTP POST localhost:8001/predict
    │   │       (Clinical Data)
    │   │   └─← JSON Response
    │   │       (Recommendations)
    │   │
    │   ├─→ Create RehabPlan (Database)
    │   │
    │   ├─→ Generate PlanExercises (Database)
    │   │   (Based on ML recommendations)
    │   │
    │   └─← Redirect to Edit Plan
    │
    └─← Plan Created Successfully
```

### 4.3 Patient Exercise Feedback Flow
```
Patient (Browser)
    │
    ├─→ GET /patient/schedule
    │   └─← Exercise Schedule View
    │
    ├─→ POST /patient/feedback/{exerciseId}
    │   │
    │   ├─→ DashboardController
    │   │
    │   ├─→ Create PatientFeedback (Database)
    │   │   (Pain, Difficulty, Mood, Comments)
    │   │
    │   └─← Success Message
    │
    └─← Feedback Submitted
```

---

## System Communication Protocols

### 4.1 Frontend ↔ Backend
- **Protocol:** HTTP/HTTPS
- **Methods:** GET, POST, PUT, DELETE
- **Format:** HTML (Forms), JSON (AJAX)
- **Authentication:** Session-based (Laravel Session)

### 4.2 Backend ↔ Database
- **Protocol:** MySQL Protocol
- **Connection:** PDO/Eloquent
- **Port:** 3306
- **Authentication:** MySQL credentials

### 4.3 Backend ↔ Intelligence Engine
- **Protocol:** HTTP/JSON
- **Methods:** POST (predictions), GET (health check)
- **Port:** 8001
- **Timeout:** 30 seconds
- **Error Handling:** Graceful fallback if service unavailable

---

## Deployment Architecture

### Development Environment
```
Local Machine
├── IDE (VS Code, PhpStorm)
├── Laravel Development Server (php artisan serve)
├── MySQL Server (Local)
└── ML Service (localhost:8001)
```

### Production Environment
```
Web Server (Nginx/Apache)
├── Laravel Application
├── MySQL Database Server
├── ML Service (Separate Container/Server)
└── Static Assets (CSS, JS, Images)
```

---

## Security Architecture

### 4.1 Authentication
- Laravel Authentication Guard
- Password hashing (bcrypt)
- Session management
- CSRF token protection

### 4.2 Authorization
- Role-based access control (RBAC)
- Route middleware for role verification
- Model-level authorization checks

### 4.3 Data Protection
- SQL injection prevention (Prepared statements via Eloquent)
- XSS prevention (Blade escaping)
- HTTPS/SSL encryption (production)
- Password validation rules

### 4.4 API Security
- ML Service authentication (if needed)
- Request validation
- Rate limiting (optional)

---

## Performance Considerations

### 4.1 Database Optimization
- Proper indexing on foreign keys
- Query optimization with eager loading
- Connection pooling

### 4.2 Caching
- View caching (Laravel)
- Query result caching (optional)
- Session caching

### 4.3 Frontend Optimization
- Minified CSS/JS
- Image optimization
- Lazy loading

### 4.4 Backend Optimization
- Efficient ML service calls
- Async processing for heavy operations
- Database query optimization

---

## System Dependencies

### Backend Dependencies
- Laravel Framework
- MySQL Driver
- HTTP Client
- Validation Framework
- Authentication System

### Frontend Dependencies
- Tailwind CSS
- Laravel Blade
- JavaScript (Vanilla)

### Intelligence Engine Dependencies
- Python 3.8+
- Scikit-learn
- TensorFlow/PyTorch
- Pandas
- NumPy
- Flask/FastAPI

---

## Summary

This 5-layer architecture provides:
- **Separation of Concerns:** Each layer has distinct responsibilities
- **Scalability:** Independent scaling of components
- **Maintainability:** Clear structure and organization
- **Security:** Multiple layers of protection
- **Flexibility:** Easy to modify or extend individual layers
- **Integration:** Seamless communication between layers

The system successfully integrates machine learning capabilities with a comprehensive web application for stroke rehabilitation management.
