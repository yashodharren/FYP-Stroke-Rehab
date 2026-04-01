# User Sign-Up Access Guide

## How Users Can Access the Sign-Up Feature

### Entry Point 1: Login Page
**URL**: `/login`

The main entry point for new users is the **Login Page**. At the bottom of the login form, there is a clear call-to-action:

```
Don't have an account?
Sign up here
```

This link directs users to the role selection page.

---

### Entry Point 2: Direct URL Access
Users can also directly navigate to the sign-up feature using these URLs:

**Role Selection Page**
```
http://localhost:8000/signup
```

**Patient Sign-Up (Direct)**
```
http://localhost:8000/signup/patient
```

**Clinician Sign-Up (Direct)**
```
http://localhost:8000/signup/clinician
```

---

## Complete Sign-Up User Journey

### Step 1: User Lands on Login Page
```
User visits: http://localhost:8000/login
```

**What they see:**
- StrokeRehab application logo
- Email and password login fields
- "Sign In" button
- "Don't have an account? Sign up here" link
- Demo credentials (for testing)

**User Action:**
- Click "Sign up here" link

---

### Step 2: Role Selection Page
```
Route: /signup
View: auth/signup-role.blade.php
```

**What they see:**
- Page title: "Stroke Rehab - Create your account"
- Subtitle: "Select whether you're a patient or a healthcare clinician"
- Two option cards:

**Option 1: Patient Card**
- Icon: Person icon
- Title: "I'm a Patient"
- Description: "Sign up to track your rehabilitation progress and view your personalized exercise plans"
- Link: "Sign up as Patient"

**Option 2: Clinician Card**
- Icon: Doctor/Medical icon
- Title: "I'm a Clinician"
- Description: "Sign up to create and manage personalized rehabilitation plans for your patients"
- Link: "Sign up as Clinician"

**User Action:**
- Click either "I'm a Patient" or "I'm a Clinician"

---

### Step 3A: Patient Sign-Up Form
```
Route: /signup/patient
View: auth/signup-patient.blade.php
Method: GET to display form, POST to submit
```

**Form Sections:**

#### Account Information
- Full Name (required)
- Email Address (required, must be unique)
- Password (required, minimum 8 characters)
- Confirm Password (required, must match)

#### Clinical Information
- Age (required, 0-150)
- Gender (required: Female/Male)
- Systolic Blood Pressure (optional, 0-300 mmHg)
- Stroke Type (required: TACS, PACS, LACS, POCS, OTH)
- Consciousness State (required: Alert, Drowsy, Unconscious)

#### Functional Deficits
- Checkboxes for 8 IST deficits:
  - [ ] Face Deficit
  - [ ] Arm/Hand Deficit
  - [ ] Leg/Foot Deficit
  - [ ] Speech Deficit
  - [ ] Vision Deficit
  - [ ] Visuospatial Deficit
  - [ ] Brainstem/Cerebellar Deficit
  - [ ] Other Deficits

**Buttons:**
- "Create Account" (submit form)
- "Back" (return to role selection)

**User Action:**
- Fill out all required fields
- Click "Create Account"

---

### Step 3B: Clinician Sign-Up Form
```
Route: /signup/clinician
View: auth/signup-clinician.blade.php
Method: GET to display form, POST to submit
```

**Form Sections:**

#### Account Information
- Full Name (required)
- Email Address (required, must be unique)
- Password (required, minimum 8 characters)
- Confirm Password (required, must match)

#### Professional Information
- License Number (required, must be unique)
- Specialization (required)
- Hospital/Clinic Affiliation (optional)
- Contact Phone (optional)

**Notice:**
- "Verification Required" banner explaining that account needs admin approval

**Buttons:**
- "Create Account" (submit form)
- "Back" (return to role selection)

**User Action:**
- Fill out all required fields
- Click "Create Account"

---

### Step 4: Account Created & Auto-Login
```
After successful form submission:
1. User account created in database
2. Patient/Clinician record created
3. User automatically logged in
4. Redirected to /dashboard
```

**What happens next:**
- Dashboard routes user based on role:
  - **Patient** → `/patient/dashboard`
  - **Clinician** → `/clinician/dashboard`
  - **Admin** → `/admin`

---

## Visual Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                      LOGIN PAGE                              │
│                    /login                                    │
│                                                              │
│  [Email input]                                              │
│  [Password input]                                           │
│  [Sign In button]                                           │
│                                                              │
│  "Don't have an account? Sign up here" ──────┐             │
└─────────────────────────────────────────────────┼───────────┘
                                                  │
                                                  ▼
┌─────────────────────────────────────────────────────────────┐
│                  ROLE SELECTION PAGE                         │
│                    /signup                                   │
│                                                              │
│  ┌──────────────────┐    ┌──────────────────┐              │
│  │  I'm a Patient   │    │ I'm a Clinician  │              │
│  │  [Patient Icon]  │    │ [Doctor Icon]    │              │
│  │  [Description]   │    │ [Description]    │              │
│  └────────┬─────────┘    └────────┬─────────┘              │
└───────────┼──────────────────────┼──────────────────────────┘
            │                      │
      ┌─────▼──────┐         ┌─────▼──────┐
      │             │         │             │
      ▼             ▼         ▼             ▼
┌──────────────┐ ┌──────────────────────────────┐
│   PATIENT    │ │      CLINICIAN SIGN-UP       │
│   SIGN-UP    │ │    /signup/clinician         │
│ /signup/     │ │                              │
│  patient     │ │ [Account Info Fields]        │
│              │ │ [Professional Info Fields]   │
│ [Account]    │ │ [Verification Notice]        │
│ [Clinical]   │ │ [Create Account Button]      │
│ [Deficits]   │ │                              │
│ [Create Btn] │ │                              │
└──────┬───────┘ └──────────────┬───────────────┘
       │                        │
       └────────────┬───────────┘
                    │
                    ▼
        ┌──────────────────────┐
        │   ACCOUNT CREATED    │
        │   AUTO-LOGIN         │
        │   REDIRECT TO         │
        │   DASHBOARD          │
        └──────────┬───────────┘
                   │
        ┌──────────┴──────────┐
        │                     │
        ▼                     ▼
   ┌─────────────┐    ┌──────────────┐
   │   PATIENT   │    │  CLINICIAN   │
   │  DASHBOARD  │    │  DASHBOARD   │
   │  /patient/  │    │  /clinician/ │
   │  dashboard  │    │  dashboard   │
   └─────────────┘    └──────────────┘
```

---

## Routes Summary

| Route | Method | Controller | Purpose |
|-------|--------|-----------|---------|
| `/login` | GET | Built-in | Display login form |
| `/signup` | GET | AuthController | Show role selection |
| `/signup/patient` | GET | AuthController | Show patient form |
| `/signup/patient` | POST | AuthController | Process patient registration |
| `/signup/clinician` | GET | AuthController | Show clinician form |
| `/signup/clinician` | POST | AuthController | Process clinician registration |

---

## Testing the Sign-Up Feature

### Test Patient Sign-Up
1. Go to `http://localhost:8000/login`
2. Click "Sign up here"
3. Select "I'm a Patient"
4. Fill in form:
   - Name: John Doe
   - Email: john@example.com
   - Password: password123
   - Age: 65
   - Gender: Male
   - Blood Pressure: 120
   - Stroke Type: LACS
   - Consciousness: Alert
   - Deficits: Check RDEF2 (Arm/Hand)
5. Click "Create Account"
6. Should be redirected to `/patient/dashboard`

### Test Clinician Sign-Up
1. Go to `http://localhost:8000/login`
2. Click "Sign up here"
3. Select "I'm a Clinician"
4. Fill in form:
   - Name: Dr. Jane Smith
   - Email: jane@example.com
   - Password: password123
   - License: LIC-123456
   - Specialization: Physiotherapy
   - Hospital: City General Hospital
   - Phone: +1-555-1234
5. Click "Create Account"
6. Should be redirected to `/clinician/dashboard`

---

## Error Handling

### Common Validation Errors

**Email Already Exists**
```
Error: The email has already been taken.
```
Solution: Use a different email address

**Duplicate License Number (Clinician)**
```
Error: The license number has already been taken.
```
Solution: Use a unique license number

**Password Too Short**
```
Error: The password must be at least 8 characters.
```
Solution: Use a password with 8+ characters

**Passwords Don't Match**
```
Error: The password confirmation does not match.
```
Solution: Ensure both password fields are identical

**Missing Required Fields**
```
Error: The [field name] field is required.
```
Solution: Fill in all required fields marked with *

---

## Security Features

✅ **CSRF Protection**: All forms include CSRF tokens
✅ **Password Hashing**: Passwords hashed with Laravel Hash
✅ **Email Validation**: Email format validated
✅ **Unique Constraints**: Email and license number must be unique
✅ **Password Confirmation**: Requires matching passwords
✅ **Guest Middleware**: Only accessible to non-authenticated users
✅ **Input Validation**: Server-side validation of all inputs

---

## After Sign-Up

### Patient User
After successful sign-up, patients can:
- View their rehabilitation dashboard
- Track their recovery progress
- View assigned rehabilitation plans
- Submit feedback on exercises
- View their exercise schedule

### Clinician User
After successful sign-up, clinicians can:
- View their dashboard (after admin verification)
- Manage patients
- Create rehabilitation plans using ML recommendations
- Add exercises to plans
- Track patient progress
- View appointments

---

## Summary

**Sign-Up is accessible from:**
1. ✅ Login page - "Sign up here" link
2. ✅ Direct URL - `/signup`
3. ✅ Direct URLs - `/signup/patient` or `/signup/clinician`

**Sign-Up process:**
1. User selects role (Patient or Clinician)
2. User fills out role-specific form
3. Form is validated
4. Account is created
5. User is automatically logged in
6. User is redirected to their dashboard

**Status**: ✅ FULLY IMPLEMENTED AND ACCESSIBLE
