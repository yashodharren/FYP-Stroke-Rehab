# Stroke Rehabilitation Web Application - Technology Stack

## Overview
The Stroke Rehabilitation Web Application is built using a modern, scalable technology stack designed for healthcare management with integrated machine learning capabilities. This document outlines the specific technologies, frameworks, and tools used in each layer of the application.

---

## Technology Stack by Layer

### **FRONTEND: Laravel Blade + Tailwind CSS**

#### Framework & Templating
- **Laravel Blade Templating Engine**
  - Server-side template rendering
  - Dynamic view generation
  - Template inheritance and components
  - Built-in security features (CSRF protection, XSS prevention)

#### Styling & UI
- **Tailwind CSS 3.x**
  - Utility-first CSS framework
  - Responsive design system
  - Pre-built components
  - Dark mode support
  - Custom color schemes (Green for patient, Sky for clinician)

#### JavaScript & Interactivity
- **Vanilla JavaScript (ES6+)**
  - DOM manipulation
  - Event handling
  - AJAX requests for dynamic updates
  - Form validation
  - Calendar/schedule interactions

#### UI Components & Icons
- **Lucide Icons**
  - SVG-based icon library
  - Consistent iconography
  - Lightweight and scalable

#### Responsive Design
- **Mobile-First Approach**
  - Tailwind breakpoints (sm, md, lg, xl, 2xl)
  - Flexible layouts with flexbox and grid
  - Touch-friendly interfaces
  - Optimized for tablets and desktops

#### Key Features
- **Clinician Dashboard**
  - Patient management interface
  - Rehabilitation plan creation and editing
  - Exercise assignment and scheduling
  - Message and notification center
  - Statistics and overview cards

- **Patient Dashboard**
  - Exercise schedule visualization
  - Next 24-hour exercise display
  - Weekly calendar view
  - Feedback submission forms
  - Personal health information display

- **Responsive Layouts**
  - Sidebar navigation (collapsible on mobile)
  - Top navigation bar with user profile
  - Adaptive grid layouts
  - Mobile-optimized forms

#### Performance Optimizations
- View caching
- Minified CSS/JS in production
- Lazy loading of images
- Efficient DOM updates

---

### **BACKEND: Laravel Framework (PHP)**

#### Core Framework
- **Laravel 10.x (PHP 8.1+)**
  - Full-stack web framework
  - MVC architecture
  - Eloquent ORM for database abstraction
  - Built-in authentication system
  - Middleware pipeline
  - Service container and dependency injection

#### Architecture Components

**Controllers**
```
app/Http/Controllers/
├── AuthController                    # Authentication logic
├── Clinician/
│   ├── DashboardController          # Clinician dashboard
│   ├── PatientManagementController  # Patient CRUD operations
│   └── PlanGeneratorController      # Plan creation and management
└── Patient/
    └── DashboardController          # Patient dashboard and feedback
```

**Models (Eloquent ORM)**
```
app/Models/
├── User                    # User authentication and profiles
├── Patient                 # Patient records and clinical data
├── RehabPlan              # Rehabilitation plan management
├── Exercise               # Exercise library
├── PlanExercise           # Exercise-plan relationships
├── PatientFeedback        # Exercise feedback tracking
└── ClinicianMessage       # System messages and notifications
```

**Routes**
```
routes/web.php
├── Authentication routes
├── Clinician routes (prefix: /clinician)
│   ├── Dashboard
│   ├── Patient management
│   ├── Plan management
│   ├── Profile management
│   └── Message management
└── Patient routes (prefix: /patient)
    ├── Dashboard
    ├── Schedule
    ├── Details
    ├── Appointments
    ├── Feedback
    └── Profile management
```

**Middleware**
```
app/Http/Middleware/
├── Authenticate          # Verify user is logged in
├── Patient               # Verify user is patient role
├── Clinician             # Verify user is clinician role
└── VerifyCsrfToken       # CSRF protection
```

**Services**
```
app/Services/
└── MLPredictionService   # Integration with ML service
    ├── predictRecoveryWithISTData()
    ├── isServiceAvailable()
    └── getServiceStatus()
```

#### Database Features
- **Eloquent ORM**
  - Relationship management (hasMany, belongsTo, etc.)
  - Query builder
  - Eager loading
  - Lazy loading
  - Model events

- **Migrations**
  - Version control for database schema
  - Rollback capabilities
  - Seed data for testing

#### Authentication & Authorization
- **Laravel Authentication**
  - User registration and login
  - Password hashing (bcrypt)
  - Session management
  - Remember me functionality

- **Authorization**
  - Role-based access control (RBAC)
  - Route middleware for role verification
  - Model-level authorization checks

#### Validation
- **Form Validation**
  - Request validation rules
  - Custom validation messages
  - Real-time validation feedback
  - Complex validation scenarios

#### HTTP Client
- **Laravel HTTP Client**
  - RESTful API communication
  - ML service integration
  - Timeout handling
  - Error handling and logging

#### Logging & Debugging
- **Laravel Logging**
  - Application logs
  - Error tracking
  - Debug information
  - Performance monitoring

#### Key Features
- Secure user management
- Patient data encryption
- CSRF token protection
- SQL injection prevention
- XSS attack prevention
- Rate limiting (optional)
- API rate limiting

#### Performance Features
- Query optimization
- Database connection pooling
- View caching
- Configuration caching
- Route caching

---

### **INTELLIGENCE ENGINE: FastAPI (Python) + Random Forest Model**

#### Framework
- **FastAPI 0.95+**
  - Modern, fast web framework
  - Automatic API documentation (Swagger UI, ReDoc)
  - Built-in data validation (Pydantic)
  - Async/await support
  - Type hints for better code quality

#### Machine Learning Stack
- **Scikit-learn**
  - Random Forest classifier/regressor
  - Data preprocessing
  - Model training and evaluation
  - Feature scaling and normalization

- **Pandas**
  - Data manipulation and analysis
  - IST dataset processing
  - Feature engineering
  - Data cleaning

- **NumPy**
  - Numerical computations
  - Array operations
  - Mathematical functions

#### Model Architecture

**Input Features (IST Clinical Dataset)**
```python
{
    "age": int,                          # Patient age
    "gender": int,                       # 0=Female, 1=Male
    "rsbp": int,                         # Systolic Blood Pressure
    "stroke_subtype": str,               # TACS, PACS, LACS, POCS, OTH
    "conscious_state": str,              # Alert, Drowsy, Unconscious
    "rdef1": bool,                       # Motor Deficit (Right)
    "rdef2": bool,                       # Sensory Deficit (Right)
    "rdef3": bool,                       # Vision Deficit
    "rdef4": bool,                       # Speech Deficit
    "rdef5": bool,                       # Cognitive Deficit
    "rdef6": bool,                       # Emotional Deficit
    "rdef7": bool,                       # Swallowing Deficit
    "rdef8": bool                        # Urinary Deficit
}
```

**Random Forest Model**
- **Type:** Ensemble learning method
- **Trees:** Multiple decision trees
- **Features:** 13 clinical features
- **Output:** Recovery probability (0-1)
- **Confidence Score:** Model confidence (0-1)

**Model Capabilities**
1. **Recovery Prediction**
   - Predicts patient recovery probability
   - Based on IST clinical dataset
   - Confidence scoring

2. **Exercise Recommendation**
   - Suggests appropriate exercises
   - Based on patient deficits
   - Frequency and intensity recommendations
   - Safety notes and precautions

3. **Risk Assessment**
   - Identifies risk factors
   - Provides clinical recommendations
   - Personalized recovery insights

#### API Endpoints

**POST /predict**
```
Request:
{
    "age": 65,
    "gender": 1,
    "rsbp": 150,
    "stroke_subtype": "TACS",
    "conscious_state": "Alert",
    "rdef1": true,
    "rdef2": false,
    ...
}

Response:
{
    "recovery_probability": 0.75,
    "confidence_score": 0.92,
    "recommended_exercises": [
        {
            "name": "Arm Flexion",
            "frequency_per_week": 3,
            "progression_reps": "3 sets of 10 reps",
            "safety_notes": "Avoid overexertion"
        },
        ...
    ],
    "risk_factors": ["High blood pressure", "Motor deficit"],
    "recommendations": ["Gradual progression", "Monitor vital signs"]
}
```

**GET /health**
```
Response:
{
    "status": "healthy",
    "model_loaded": true,
    "version": "1.0.0"
}
```

**GET /**
```
Response:
{
    "service": "Stroke Rehabilitation ML Service",
    "version": "1.0.0",
    "status": "running"
}
```

#### Model Training Pipeline
- Data preprocessing and cleaning
- Feature engineering
- Train/test split
- Model training with cross-validation
- Hyperparameter tuning
- Model evaluation and validation
- Model persistence (pickle/joblib)

#### Performance Metrics
- Accuracy
- Precision and Recall
- F1 Score
- ROC-AUC
- Confusion Matrix

#### Deployment
- **Uvicorn Server**
  - ASGI server
  - Port: 8001
  - Multi-worker support
  - Hot reload for development

- **Docker Support**
  - Containerized deployment
  - Environment consistency
  - Easy scaling

#### Key Features
- Fast inference (< 100ms per prediction)
- Automatic API documentation
- Request validation
- Error handling
- Logging and monitoring
- Health check endpoint

---

### **DATABASE: MySQL**

#### Database Management System
- **MySQL 8.0+**
  - Relational database
  - ACID compliance
  - Transaction support
  - Full-text search
  - JSON data type support

#### Database Schema

**Core Tables**

1. **users**
   - User authentication and profiles
   - Columns: id, name, email, password, role, created_at, updated_at
   - Indexes: email (UNIQUE)

2. **patients**
   - Patient records and clinical data
   - Columns: id, user_id, clinician_id, age, gender, rsbp, stroke_subtype, conscious_state, recovery_status, rdef1-8, created_at, updated_at
   - Indexes: user_id (FK), clinician_id (FK)

3. **rehab_plans**
   - Rehabilitation plans
   - Columns: id, patient_id, clinician_id, plan_name, description, recovery_probability, ml_confidence_score, difficulty_level, start_date, end_date, status, ml_metadata (JSON), created_at, updated_at
   - Indexes: patient_id (FK), clinician_id (FK)

4. **exercises**
   - Exercise library
   - Columns: id, name, description, difficulty_level, target_area, duration_minutes, repetitions, instructions, image_url, video_url, created_at, updated_at
   - Indexes: difficulty_level

5. **plan_exercises**
   - Exercise-plan relationships
   - Columns: id, rehab_plan_id, exercise_id, day_of_week, frequency_per_week, scheduled_time, scheduled_times (JSON), custom_repetitions, custom_duration_minutes, notes, is_completed, created_at, updated_at
   - Indexes: rehab_plan_id (FK), exercise_id (FK)

6. **patient_feedback**
   - Exercise feedback and progress
   - Columns: id, patient_id, plan_exercise_id, pain_level, difficulty_rating, mood_rating, comments, completed_exercise, feedback_date, created_at, updated_at
   - Indexes: patient_id (FK), plan_exercise_id (FK)

7. **clinician_messages**
   - System messages and notifications
   - Columns: id, clinician_id, patient_id, message, type, created_at, updated_at
   - Indexes: clinician_id (FK), patient_id (FK)

#### Data Relationships
```
users (1) ──→ (many) patients
users (1) ──→ (many) rehab_plans (as clinician)
patients (1) ──→ (many) rehab_plans
patients (1) ──→ (many) patient_feedback
rehab_plans (1) ──→ (many) plan_exercises
exercises (1) ──→ (many) plan_exercises
plan_exercises (1) ──→ (many) patient_feedback
```

#### Storage & Performance
- **Connection Pooling**
  - Efficient database connections
  - Connection reuse
  - Timeout management

- **Indexing Strategy**
  - Primary keys on all tables
  - Foreign key indexes
  - Composite indexes for common queries
  - Unique constraints on email

- **Query Optimization**
  - Eager loading with Eloquent
  - Query caching
  - Batch operations
  - Efficient pagination

#### Backup & Recovery
- Regular automated backups
- Point-in-time recovery
- Replication support
- Transaction logging

#### Security
- User authentication credentials
- Encrypted sensitive data
- Access control via application layer
- SQL injection prevention (Prepared statements)

---

## Technology Integration Points

### Frontend ↔ Backend
```
HTTP/HTTPS
├── GET/POST/PUT/DELETE requests
├── HTML form submissions
├── AJAX requests (JSON)
└── Session-based authentication
```

### Backend ↔ Database
```
MySQL Protocol
├── PDO connections
├── Eloquent ORM queries
├── Prepared statements
└── Transaction management
```

### Backend ↔ Intelligence Engine
```
HTTP/JSON
├── POST /predict (clinical data)
├── GET /health (status check)
├── Timeout: 30 seconds
└── Error handling with fallback
```

---

## Development Tools & Environment

### Version Control
- **Git**
  - GitHub repository
  - Branch management
  - Commit history

### IDE & Editors
- **VS Code / PhpStorm**
  - Code editing
  - Debugging
  - Extensions for Laravel, Python, etc.

### Package Managers
- **Composer (PHP)**
  - Laravel dependencies
  - Package management

- **pip (Python)**
  - Python packages
  - ML libraries

- **npm (JavaScript)**
  - Frontend dependencies
  - Build tools

### Development Servers
- **Laravel Development Server**
  - `php artisan serve`
  - Port: 8000

- **Uvicorn (Python)**
  - FastAPI server
  - Port: 8001

- **MySQL Server**
  - Local database
  - Port: 3306

### Testing & Debugging
- **Laravel Testing**
  - PHPUnit
  - Feature and unit tests

- **Python Testing**
  - pytest
  - Model validation tests

- **Browser DevTools**
  - Chrome/Firefox developer tools
  - Network inspection
  - Console debugging

---

## Deployment Architecture

### Development Environment
```
Local Machine
├── Laravel App (localhost:8000)
├── MySQL Database (localhost:3306)
├── ML Service (localhost:8001)
└── IDE (VS Code/PhpStorm)
```

### Production Environment
```
Web Server (Nginx/Apache)
├── Laravel Application
├── Tailwind CSS (compiled)
├── JavaScript (minified)
├── Static Assets
│
Database Server (MySQL)
├── Patient Records
├── Exercise Library
├── Rehabilitation Plans
└── Feedback Data
│
ML Service Server (Separate)
├── FastAPI Application
├── Random Forest Model
└── Prediction Engine
```

---

## Security Stack

### Authentication & Authorization
- Laravel Authentication Guard
- bcrypt password hashing
- Session management
- CSRF token protection
- Role-based access control (RBAC)

### Data Protection
- SQL injection prevention (Prepared statements)
- XSS prevention (Blade escaping)
- HTTPS/SSL encryption (production)
- Secure password policies
- Data validation and sanitization

### API Security
- Request validation (Pydantic)
- Error handling
- Logging and monitoring
- Rate limiting (optional)

---

## Performance Stack

### Caching
- View caching (Laravel)
- Query result caching
- Session caching
- Browser caching

### Optimization
- Database indexing
- Query optimization
- Minified CSS/JS
- Image optimization
- Lazy loading

### Monitoring
- Application logging
- Error tracking
- Performance metrics
- Database query analysis

---

## Summary of Technology Stack

| Layer | Technology | Purpose |
|-------|-----------|---------|
| **Frontend** | Laravel Blade + Tailwind CSS | Responsive medical dashboard |
| **Backend** | Laravel 10 (PHP 8.1+) | Secure user management and business logic |
| **Intelligence Engine** | FastAPI (Python) + Random Forest | ML-powered recovery prediction and exercise recommendations |
| **Database** | MySQL 8.0+ | Patient records and exercise library storage |
| **Version Control** | Git/GitHub | Code management and collaboration |
| **Deployment** | Nginx/Apache + Docker | Production hosting and scaling |

---

## Key Advantages

✅ **Security:** Laravel's built-in security features + encrypted data storage
✅ **Scalability:** Modular architecture allows independent scaling
✅ **Performance:** Optimized queries, caching, and ML inference
✅ **Maintainability:** Clear separation of concerns across layers
✅ **Integration:** Seamless communication between frontend, backend, and ML service
✅ **Healthcare Compliance:** Secure patient data handling
✅ **User Experience:** Responsive design with Tailwind CSS
✅ **Intelligence:** ML-powered personalized recommendations

This technology stack provides a robust, secure, and scalable solution for stroke rehabilitation management with integrated machine learning capabilities.
