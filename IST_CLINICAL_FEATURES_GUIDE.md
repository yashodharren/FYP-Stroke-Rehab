# IST Clinical Features Integration Guide

## Overview
This guide documents the integration of IST (International Stroke Trial) dataset features into the FilamentPHP admin panel for ML model predictions.

## Database Schema Updates

### New Patient Table Columns

#### Demographics & Vitals
- **gender** (integer): 0=Female, 1=Male
- **rsbp** (integer): Systolic Blood Pressure in mmHg
  - Logic: If > 160 mmHg, system suggests conservative, low-intensity rehabilitation plan

#### Stroke Characterization
- **stroke_subtype** (string): Type of stroke
  - TACS: Total Anterior Circulation Stroke (High Severity)
  - PACS: Partial Anterior Circulation Stroke
  - LACS: Lacunar Stroke (Small vessel, usually better recovery)
  - POCS: Posterior Circulation Stroke
  - OTH: Other/Unclassified

- **conscious_state** (string): Patient's consciousness level at admission
  - Alert: Fully Alert
  - Drowsy: Drowsy/Lethargic
  - Unconscious: Unconscious

#### Functional Deficits (RDEF Fields)
All boolean fields (true/false):
- **rdef1**: Face Deficit - Facial weakness or asymmetry
- **rdef2**: Arm/Hand Deficit - Upper limb weakness or loss of function
- **rdef3**: Leg/Foot Deficit - Lower limb weakness or loss of function
- **rdef4**: Dysphasia (Speech) - Speech or language impairment
- **rdef5**: Hemianopia (Vision) - Loss of visual field
- **rdef6**: Visuospatial Disorder - Spatial awareness or coordination issues
- **rdef7**: Brainstem/Cerebellar Signs - Balance, coordination, or brainstem symptoms
- **rdef8**: Other Deficits - Any other neurological deficits

## FilamentPHP Form Structure

The Patient resource form is organized into 4 professional sections:

### 1. Patient Information
- User ID (disabled)
- Assigned Clinician ID (disabled)
- Age (years)
- Medical History (textarea)
- Recovery Status (dropdown)

### 2. Demographics & Vitals
- Gender (dropdown: Female/Male)
- Systolic Blood Pressure (numeric input with helper text)

### 3. Stroke Characterization
- Stroke Subtype (dropdown with descriptions)
- Conscious State (dropdown: Alert/Drowsy/Unconscious)

### 4. Functional Deficits
- 2-column grid of 8 toggle switches
- Each toggle has descriptive label and helper text
- Maps directly to exercise library recommendations

## ML Service Integration

### Method: `predictRecoveryWithISTData()`

**Location**: `app/Services/MLPredictionService.php`

**Parameters**:
```php
$clinicalData = [
    'age' => (int),
    'gender' => (int), // 0 or 1
    'rsbp' => (int),
    'stroke_subtype' => (string), // TACS, PACS, LACS, POCS, OTH
    'conscious_state' => (string), // Alert, Drowsy, Unconscious
    'rdef1' => (bool),
    'rdef2' => (bool),
    'rdef3' => (bool),
    'rdef4' => (bool),
    'rdef5' => (bool),
    'rdef6' => (bool),
    'rdef7' => (bool),
    'rdef8' => (bool),
];

$prediction = $mlService->predictRecoveryWithISTData($clinicalData);
```

**Returns**:
```php
[
    'recovery_probability' => (float), // 0.0 to 1.0
    'difficulty_level' => (int), // 1-5
    'recommended_exercises' => (array),
    'confidence_score' => (float), // 0.0 to 1.0
]
```

## Exercise Library Mapping

### Functional Deficit to Exercise Mapping

| Deficit | Recommended Exercises |
|---------|----------------------|
| Face Deficit (rdef1) | Facial exercises, mouth/lip movements |
| Arm/Hand Deficit (rdef2) | Upper limb exercises, fine motor tasks |
| Leg/Foot Deficit (rdef3) | Lower limb exercises, walking, balance |
| Dysphasia (rdef4) | Speech therapy exercises, verbal tasks |
| Hemianopia (rdef5) | Visual tracking, eye movement exercises |
| Visuospatial (rdef6) | Spatial awareness, coordination exercises |
| Brainstem/Cerebellar (rdef7) | Balance, coordination, stability exercises |
| Other Deficits (rdef8) | Customized based on specific deficit |

## Usage in Plan Generation

### In Clinician Plan Generator

```php
// Collect IST clinical data from patient
$clinicalData = [
    'age' => $patient->age,
    'gender' => $patient->gender,
    'rsbp' => $patient->rsbp,
    'stroke_subtype' => $patient->stroke_subtype,
    'conscious_state' => $patient->conscious_state,
    'rdef1' => $patient->rdef1,
    'rdef2' => $patient->rdef2,
    'rdef3' => $patient->rdef3,
    'rdef4' => $patient->rdef4,
    'rdef5' => $patient->rdef5,
    'rdef6' => $patient->rdef6,
    'rdef7' => $patient->rdef7,
    'rdef8' => $patient->rdef8,
];

// Get ML prediction
$mlService = new MLPredictionService();
$prediction = $mlService->predictRecoveryWithISTData($clinicalData);

// Use prediction for plan generation
$plan = RehabPlan::create([
    'patient_id' => $patient->id,
    'clinician_id' => auth()->id(),
    'difficulty_level' => $prediction['difficulty_level'],
    'recovery_probability' => $prediction['recovery_probability'],
    // ... other plan fields
]);
```

## Safety Rules

### Blood Pressure Logic
- If RSBP > 160 mmHg:
  - Recommend conservative, low-intensity rehabilitation plan
  - Suggest frequent monitoring
  - Consider medical consultation before intensive exercises

### Consciousness State Impact
- Unconscious patients: Limited rehabilitation, focus on passive exercises
- Drowsy patients: Shorter sessions, frequent breaks
- Alert patients: Full rehabilitation protocol

### Deficit-Specific Precautions
- Hemianopia (rdef5): Ensure safe environment, visual compensation training
- Brainstem/Cerebellar (rdef7): Focus on balance and fall prevention
- Dysphasia (rdef4): Include speech therapy, communication aids

## Testing the Integration

### Admin Panel Testing
1. Navigate to `/admin/patients`
2. Create or edit a patient
3. Fill in all IST clinical features
4. Save and verify data is stored correctly

### ML Prediction Testing
1. Ensure FastAPI service is running on `http://localhost:8001`
2. In plan generator, verify predictions are fetched
3. Check that difficulty level and exercises match deficits

### Exercise Mapping Testing
1. Create plans for patients with different deficit combinations
2. Verify recommended exercises match the deficits
3. Test edge cases (all deficits, no deficits, etc.)

## Model Training Compatibility

The IST clinical features exactly match the Random Forest model training data:
- **Input Features**: 22 columns (age, gender, rsbp, stroke_subtype, conscious_state, rdef1-8, etc.)
- **Output**: Recovery probability (0-1 scale)
- **Difficulty Recommendation**: Based on recovery probability and deficits
- **Exercise Recommendations**: Mapped from functional deficits

## Future Enhancements

1. **Longitudinal Tracking**: Track changes in functional deficits over time
2. **Predictive Analytics**: Show recovery trajectory based on historical data
3. **Comparative Analysis**: Compare patient's recovery to similar cases
4. **Risk Stratification**: Identify high-risk patients for early intervention
5. **Automated Plan Adjustment**: Modify plans based on actual vs predicted recovery

## References

- IST Dataset: International Stroke Trial
- Random Forest Model: `ml_service/models/stroke_recovery_model.joblib`
- FastAPI Service: `ml_service/main.py`
- Patient Model: `app/Models/Patient.php`
