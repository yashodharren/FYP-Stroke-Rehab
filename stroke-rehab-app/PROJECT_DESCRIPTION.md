# Stroke Rehabilitation Web Application - Project Description

## Project Title
**Intelligent Stroke Rehabilitation Management System with ML-Powered Exercise Recommendations**

---

## Executive Summary

The Stroke Rehabilitation Web Application is a comprehensive healthcare management system designed to facilitate personalized rehabilitation planning and patient progress tracking for stroke survivors. The system integrates machine learning capabilities to provide intelligent, data-driven exercise recommendations based on patient clinical profiles, enabling clinicians to create optimized rehabilitation plans while allowing patients to track their recovery progress in real-time.

---

## Project Background

### Problem Statement
Stroke is a leading cause of disability worldwide, affecting millions of individuals annually. Post-stroke rehabilitation is critical for patient recovery, yet traditional rehabilitation planning often relies on generic exercise protocols that may not account for individual patient characteristics, clinical severity, or specific functional deficits.

**Key Challenges:**
- Lack of personalized rehabilitation planning based on patient clinical data
- Difficulty in tracking patient progress and exercise compliance
- Limited integration of clinical data with exercise recommendations
- Time-consuming manual plan creation by clinicians
- Insufficient real-time feedback mechanisms for patient engagement

### Solution Overview
This project addresses these challenges by developing an intelligent web-based platform that:
1. Leverages machine learning to predict recovery probability and recommend personalized exercises
2. Provides clinicians with a comprehensive patient management interface
3. Enables patients to track exercises and provide real-time feedback
4. Integrates clinical data (IST dataset features) for intelligent recommendations
5. Facilitates seamless communication between clinicians and patients

---

## Project Scope

### What is Included
✅ **User Authentication & Management**
- Multi-role authentication (Clinician, Patient)
- Secure profile management
- Password management

✅ **Patient Management Module**
- Patient account creation and assignment
- Clinical information management (age, gender, stroke type, deficits)
- Patient search and filtering
- Recovery status tracking

✅ **Rehabilitation Plan Management**
- Plan creation with ML-powered recommendations
- Exercise library with 50+ predefined exercises
- Exercise customization (repetitions, duration, scheduling)
- Plan publication and activation
- Plan status tracking (draft, active, completed)

✅ **Exercise Management**
- Comprehensive exercise library
- Exercise properties (difficulty, target area, instructions, media)
- Intelligent scheduling to avoid conflicts
- Day-of-week and time-slot based scheduling
- Frequency management (1-7 days per week)

✅ **Patient Dashboard & Progress Tracking**
- Real-time exercise schedule display
- Next 24-hour exercise notifications
- Weekly calendar view
- Exercise feedback submission (pain, difficulty, mood)
- Progress visualization

✅ **Clinician Dashboard**
- Patient statistics overview
- Plan management interface
- Message and notification center
- Appointment reminders
- Quick access to key functions

✅ **Machine Learning Integration**
- Recovery probability prediction
- Automated exercise recommendations
- Confidence scoring
- Risk factor identification
- Safety recommendations

### What is NOT Included
❌ Video streaming for exercise demonstrations (links provided instead)
❌ Telemedicine/video consultation features
❌ Mobile native applications (web-responsive only)
❌ Advanced analytics and reporting
❌ Insurance integration
❌ Prescription management

---

## Project Objectives

### Primary Objectives
1. **Develop an Intelligent Rehabilitation Management System**
   - Create a web-based platform for stroke rehabilitation management
   - Integrate machine learning for personalized exercise recommendations
   - Provide secure, user-friendly interfaces for clinicians and patients

2. **Implement ML-Powered Exercise Recommendations**
   - Train and deploy a Random Forest model using IST clinical dataset
   - Predict patient recovery probability based on clinical features
   - Generate personalized exercise recommendations based on patient deficits
   - Provide confidence scores and safety recommendations

3. **Enable Comprehensive Patient Management**
   - Allow clinicians to create and manage patient accounts
   - Store and manage patient clinical information
   - Track rehabilitation deficits and recovery status
   - Facilitate patient-clinician communication

4. **Facilitate Patient Engagement & Progress Tracking**
   - Provide patients with personalized exercise schedules
   - Enable real-time feedback submission
   - Display progress visualization
   - Support patient autonomy in rehabilitation

5. **Ensure System Security & Data Privacy**
   - Implement role-based access control
   - Protect sensitive patient health information
   - Ensure HIPAA-compliant data handling
   - Prevent unauthorized access

---

## Key Features

### For Clinicians
1. **Dashboard Overview**
   - Total patients count
   - Active and completed rehabilitation plans
   - System messages and notifications
   - Appointment reminders

2. **Patient Management**
   - Create new patient accounts with temporary passwords
   - Assign existing patients to care
   - Edit patient clinical information
   - Search and filter patients
   - View comprehensive patient profiles

3. **Rehabilitation Plan Creation**
   - Create plans with difficulty levels (1-5)
   - Receive ML-powered exercise recommendations
   - Customize exercises (repetitions, duration, scheduling)
   - Add/remove exercises from plans
   - Publish and activate plans
   - Track plan progress

4. **Exercise Management**
   - Access exercise library with 50+ exercises
   - Filter exercises by difficulty level
   - Assign exercises to specific days and times
   - Set custom parameters for each exercise
   - View exercise details and instructions

5. **Communication**
   - View system messages (e.g., temporary password notifications)
   - Delete messages
   - Track message timestamps

6. **Profile Management**
   - Update personal information
   - Change password securely

### For Patients
1. **Dashboard Overview**
   - Active rehabilitation plan status
   - Next 24-hour upcoming exercises
   - Quick access to schedule and appointments

2. **Exercise Schedule**
   - Weekly calendar view of exercises
   - Time-slot based schedule (9 AM - 5 PM)
   - Day-by-day exercise organization
   - Exercise details and instructions

3. **Exercise Feedback**
   - Submit feedback after completing exercises
   - Rate pain level (0-10 scale)
   - Rate difficulty (1-5 scale)
   - Rate mood (1-5 scale)
   - Add comments and notes
   - Track exercise completion

4. **Personal Information**
   - View personal details
   - View medical information
   - View functional deficits
   - View recovery status

5. **Profile Management**
   - Update personal information
   - Change password securely

---

## Technology Stack

### Frontend
- **Laravel Blade Templating** - Server-side template rendering
- **Tailwind CSS 3.x** - Responsive UI design
- **Vanilla JavaScript (ES6+)** - Interactive features
- **Lucide Icons** - UI components

### Backend
- **Laravel 10.x (PHP 8.1+)** - Web framework
- **Eloquent ORM** - Database abstraction
- **Laravel Authentication** - User management
- **Laravel Validation** - Form validation

### Intelligence Engine
- **FastAPI (Python)** - ML service framework
- **Scikit-learn** - Random Forest model
- **Pandas & NumPy** - Data processing
- **Uvicorn** - ASGI server (Port 8001)

### Database
- **MySQL 8.0+** - Relational database
- **7 Core Tables** - Users, Patients, Plans, Exercises, Feedback, Messages
- **Proper Indexing** - Performance optimization

---

## System Architecture

### 5-Layer Architecture
1. **Presentation Layer** - Laravel Blade + Tailwind CSS
2. **Application Layer** - Laravel Framework with Controllers, Models, Routes
3. **Database Layer** - MySQL with optimized schema
4. **Intelligence Engine Layer** - FastAPI with Random Forest ML model
5. **Integration Layer** - HTTP/JSON communication between layers

### Data Flow
```
Clinician/Patient (Browser)
    ↓
Laravel Application (Controllers, Models, Routes)
    ↓
MySQL Database (Patient Data, Plans, Exercises)
    ↓
ML Service (FastAPI - Recovery Prediction & Recommendations)
    ↓
Intelligent Recommendations & Predictions
```

---

## Clinical Data Integration

### IST Dataset Features
The system uses 13 clinical features from the International Stroke Trial (IST) dataset:

**Patient Demographics:**
- Age
- Gender (0=Female, 1=Male)

**Clinical Measurements:**
- RSBP (Systolic Blood Pressure)
- Stroke Subtype (TACS, PACS, LACS, POCS, OTH)
- Consciousness State (Alert, Drowsy, Unconscious)

**Functional Deficits (8 flags):**
- rdef1: Motor Deficit (Right)
- rdef2: Sensory Deficit (Right)
- rdef3: Vision Deficit
- rdef4: Speech Deficit
- rdef5: Cognitive Deficit
- rdef6: Emotional Deficit
- rdef7: Swallowing Deficit
- rdef8: Urinary Deficit

### ML Model Output
- **Recovery Probability:** 0-1 scale (likelihood of good recovery)
- **Confidence Score:** 0-1 scale (model confidence in prediction)
- **Recommended Exercises:** List of personalized exercises with:
  - Exercise name
  - Frequency per week (1-7)
  - Progression recommendations
  - Safety notes
- **Risk Factors:** Identified clinical risk factors
- **Recommendations:** Personalized clinical recommendations

---

## Implementation Status

### Completed Features ✅
- User authentication and authorization
- Clinician and patient dashboards
- Patient management (create, assign, edit)
- Rehabilitation plan creation and management
- Exercise library and assignment
- ML service integration
- Patient feedback system
- Profile management for both roles
- Messaging system
- Responsive UI design

### Current Development
- Advanced analytics and reporting
- Appointment scheduling refinement
- Performance optimization

### Future Enhancements
- Video streaming for exercise demonstrations
- Telemedicine consultation features
- Mobile native applications
- Advanced analytics and insights
- Integration with wearable devices
- Predictive alerts for patient risk

---

## Expected Outcomes

### For Clinicians
- Reduced time in plan creation (ML-powered recommendations)
- Better-informed clinical decisions (data-driven insights)
- Improved patient management (centralized dashboard)
- Enhanced patient engagement (real-time feedback)

### For Patients
- Personalized rehabilitation plans (tailored to their condition)
- Better exercise compliance (clear schedules and instructions)
- Progress tracking (real-time feedback and visualization)
- Improved recovery outcomes (evidence-based recommendations)

### For Healthcare System
- Standardized rehabilitation protocols (evidence-based)
- Better resource allocation (ML-optimized planning)
- Improved patient outcomes (personalized approach)
- Scalable solution (web-based platform)

---

## Project Timeline

| Phase | Duration | Status |
|-------|----------|--------|
| Requirements & Design | Week 1-2 | ✅ Completed |
| Backend Development | Week 3-6 | ✅ Completed |
| Frontend Development | Week 7-9 | ✅ Completed |
| ML Integration | Week 10-11 | ✅ Completed |
| Testing & Refinement | Week 12-13 | ✅ Completed |
| Documentation & Presentation | Week 14-15 | 🔄 In Progress |

---

## Team & Resources

### Development Team
- Full-stack developer (Laravel + Python)
- UI/UX designer (Tailwind CSS)
- ML engineer (Scikit-learn, FastAPI)

### Tools & Technologies
- IDE: VS Code / PhpStorm
- Version Control: Git/GitHub
- Database: MySQL
- ML Framework: Scikit-learn
- Web Framework: Laravel + FastAPI

### External Resources
- IST Clinical Dataset
- Exercise Database
- Medical Literature & Guidelines

---

## Conclusion

The Stroke Rehabilitation Web Application represents a significant advancement in post-stroke rehabilitation management by combining clinical expertise with machine learning capabilities. By providing clinicians with intelligent, data-driven tools and patients with personalized, engaging rehabilitation experiences, this system has the potential to improve recovery outcomes and quality of life for stroke survivors.

The integration of the IST clinical dataset with a Random Forest ML model enables the system to provide evidence-based, personalized exercise recommendations that account for individual patient characteristics and clinical severity. The comprehensive web-based platform ensures accessibility, scalability, and ease of use for both clinicians and patients.

This project demonstrates the practical application of machine learning in healthcare and the importance of user-centered design in medical technology development.
