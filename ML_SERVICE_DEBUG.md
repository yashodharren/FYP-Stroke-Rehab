# ML Service Debugging Guide

## Issue
The system is still recommending "Shoulder Shrug" and "Hand & Wrist Stretch" for patients with facial deficits, despite updates to the exercise library and recommendation algorithm.

## Root Cause
The ML service (FastAPI) loads the exercise library into memory when it starts. If the service hasn't been restarted since the CSV file was updated, it will still use the old exercise library.

## Solution Steps

### Step 1: Stop the ML Service
If the ML service is currently running on port 8001, you need to stop it:

**On Windows (PowerShell):**
```powershell
# Find the process using port 8001
Get-NetTCPConnection -LocalPort 8001 | Select-Object OwningProcess

# Kill the process (replace PID with actual process ID)
Stop-Process -Id <PID> -Force

# Or if running in terminal, press Ctrl+C
```

### Step 2: Verify the Exercise Library CSV
The exercise library should have been updated with 50 exercises including facial, speech, cognitive, and emotional exercises.

**Check the file:**
```
c:\Users\dharr\Desktop\University Files\Uni Sem 8\CAT405\FYP\ml_service\Exercise library.csv
```

**Expected content:**
- Row 1: Headers (Exercise ID, Name, Target Deficit, Body Region, Difficulty, Equipment, Instructions, Progression/Reps, Safety/Contraindications)
- Rows 2-11: Original exercises (Shoulder Shrug, Hand & Wrist Stretch, etc.)
- Rows 12-52: New exercises for facial, speech, cognitive, emotional, and additional limb exercises

**Key new exercises:**
- EXE-011: Facial Muscle Exercises (Target Deficit: "Facial Exercises")
- EXE-012: Cheek Puffing (Target Deficit: "Facial Exercises")
- EXE-013: Tongue Exercises (Target Deficit: "Speech/Swallowing")
- EXE-015: Speech Repetition (Target Deficit: "Speech Therapy")
- EXE-021: Attention Focus (Target Deficit: "Cognitive")
- EXE-026: Deep Breathing (Target Deficit: "Emotional Regulation")

### Step 3: Restart the ML Service
Navigate to the ML service directory and restart it:

**On Windows (PowerShell):**
```powershell
cd "c:\Users\dharr\Desktop\University Files\Uni Sem 8\CAT405\FYP\ml_service"

# Start the service
python main.py

# Or if using uvicorn directly:
uvicorn main:app --host 0.0.0.0 --port 8001 --reload
```

**Expected output when service starts:**
```
✓ Exercise library loaded successfully (50 exercises)
✓ Columns: ['Exercise ID', 'Name', 'Target Deficit', 'Body Region', 'Difficulty (1−5)', 'Equipment', 'Instructions', 'Progression/Reps', 'Safety/Contraindications']
✓ Sample Target Deficits: ['Strength/ROM', 'Flexibility', 'Mobility', 'Range of Motion', 'Functional Strength', 'Fine Motor', 'Strength', 'Grip Strength', 'Balance', 'Facial Exercises']
```

### Step 4: Test the Service
Once the service is running, test it with a facial deficit patient:

**Test patient data:**
```json
{
  "age": 50,
  "gender": 1,
  "rsbp": 134,
  "stroke_subtype": "LACS",
  "conscious_state": "Alert",
  "rdef1": true,
  "rdef2": false,
  "rdef3": false,
  "rdef4": false,
  "rdef5": false,
  "rdef6": false,
  "rdef7": false,
  "rdef8": false
}
```

**Expected response:**
The recommended exercises should include:
- Facial Muscle Exercises (EXE-011) - for rdef1 (Face Deficit)
- NOT Shoulder Shrug or Hand & Wrist Stretch

### Step 5: Create a New Rehabilitation Plan
In the web application:
1. Go to Clinician Dashboard → My Patients
2. Create a new patient with ONLY facial deficit checked
3. Create a new rehabilitation plan for this patient
4. Verify the recommended exercises include facial exercises

## Debug Output
When the service processes a request, you should see debug messages like:

```
DEBUG: Processing deficit rdef1 (Face Deficit), looking for target deficits: ['Facial Exercises', 'Speech/Swallowing']
DEBUG: Found 2 exercises for target deficit 'Facial Exercises' at difficulty <= 3
```

## Code Changes Made

### 1. Exercise Library Expanded (10 → 50 exercises)
- Added facial exercises (EXE-011, EXE-012)
- Added speech/swallowing exercises (EXE-013 to EXE-018)
- Added vision exercises (EXE-019, EXE-020)
- Added cognitive exercises (EXE-021 to EXE-024)
- Added emotional regulation exercises (EXE-025 to EXE-028)
- Added additional lower limb exercises (EXE-029 to EXE-036)
- Added additional upper limb exercises (EXE-037 to EXE-043)
- Added coordination exercises (EXE-048 to EXE-050)

### 2. ML Service Algorithm Fixed
**File:** `c:\Users\dharr\Desktop\University Files\Uni Sem 8\CAT405\FYP\ml_service\main.py`

**Changes:**
- Replaced `get_deficit_to_body_region_mapping()` with `get_deficit_to_exercise_mapping()`
- Updated `select_exercises_from_library()` to:
  - Filter by "Target Deficit" column instead of "Body Region"
  - Track selected exercise IDs to prevent duplicates
  - Select 1 unique exercise per deficit (max 5 total)
  - Fall back to broader search if specific exercises not found

**Deficit-to-Exercise Mapping:**
```python
{
    'rdef1': ['Facial Exercises', 'Speech/Swallowing'],
    'rdef2': ['Strength/ROM', 'Fine Motor', 'Grip Strength'],
    'rdef3': ['Mobility', 'Balance', 'Functional Strength'],
    'rdef4': ['Speech Therapy', 'Speech/Swallowing'],
    'rdef5': ['Vision'],
    'rdef6': ['Coordination', 'Balance'],
    'rdef7': ['Balance', 'Coordination'],
    'rdef8': ['General', 'Coordination', 'Functional Mobility']
}
```

## Troubleshooting

### Issue: Still seeing old exercises after restart
**Solution:** 
1. Check if the CSV file was properly saved (verify it has 50+ lines)
2. Check the debug output when service starts - it should show "50 exercises"
3. Clear any browser cache (Ctrl+Shift+Delete)
4. Create a completely new patient and plan

### Issue: Service won't start
**Solution:**
1. Check if port 8001 is already in use: `netstat -ano | findstr :8001`
2. Verify Python and required packages are installed: `pip list | findstr fastapi pandas`
3. Check the error message in the terminal

### Issue: Exercises still not matching deficits
**Solution:**
1. Check the "Target Deficit" column in the CSV file
2. Verify the deficit mapping in the code matches the CSV values
3. Check the debug output in the ML service terminal

## Verification Checklist

- [ ] ML service stopped
- [ ] Exercise library CSV has 50+ exercises
- [ ] Exercise library CSV has proper "Target Deficit" values
- [ ] ML service restarted and shows "50 exercises" loaded
- [ ] Debug output shows correct deficit processing
- [ ] New patient with facial deficit created
- [ ] New rehabilitation plan created for facial deficit patient
- [ ] Recommended exercises include facial exercises (not shoulder shrug)
- [ ] Plan published successfully
