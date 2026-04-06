from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import Optional, List
import joblib
import numpy as np
import pandas as pd
import os
import warnings

# Suppress scikit-learn version warnings
warnings.filterwarnings('ignore', category=UserWarning)

app = FastAPI(title="Stroke Rehabilitation ML Service", version="1.0.0")

# Enable CORS for Laravel frontend
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Model path - use absolute path
MODEL_PATH = os.path.join(os.path.dirname(__file__), "stroke_recovery_model.joblib")

# Load model if it exists
model = None
if os.path.exists(MODEL_PATH):
    try:
        model = joblib.load(MODEL_PATH)
        print(f"✓ Model loaded successfully from {MODEL_PATH}")
    except Exception as e:
        print(f"✗ Error loading model: {e}")
else:
    print(f"✗ Model file not found at {MODEL_PATH}")
    print("The service will run in demo mode without the ML model")

# Load exercise library - use absolute path
EXERCISE_LIBRARY_PATH = os.path.join(os.path.dirname(__file__), "Exercise library.csv")
exercise_library = None
if os.path.exists(EXERCISE_LIBRARY_PATH):
    try:
        exercise_library = pd.read_csv(EXERCISE_LIBRARY_PATH)
        print(f"✓ Exercise library loaded successfully ({len(exercise_library)} exercises)")
        print(f"✓ Columns: {list(exercise_library.columns)}")
        print(f"✓ Sample Target Deficits: {exercise_library['Target Deficit'].unique()[:10]}")
    except Exception as e:
        print(f"✗ Error loading exercise library: {e}")
else:
    print(f"✗ Exercise library not found at {EXERCISE_LIBRARY_PATH}")


# IST Clinical Features Data Model
class ISTClinicalData(BaseModel):
    age: int
    gender: int  # 0=Female, 1=Male
    rsbp: Optional[int] = None  # Systolic Blood Pressure
    stroke_subtype: str  # TACS, PACS, LACS, POCS, OTH
    conscious_state: str  # Alert, Drowsy, Unconscious
    rdef1: bool = False  # Face Deficit
    rdef2: bool = False  # Arm/Hand Deficit
    rdef3: bool = False  # Leg/Foot Deficit
    rdef4: bool = False  # Dysphasia (Speech)
    rdef5: bool = False  # Hemianopia (Vision)
    rdef6: bool = False  # Visuospatial Disorder
    rdef7: bool = False  # Brainstem/Cerebellar Signs
    rdef8: bool = False  # Other Deficits


# Legacy PatientData model for backward compatibility
class PatientData(BaseModel):
    age: int
    stroke_type: str  # 'ischemic', 'hemorrhagic', 'tia'
    deficit_area: str  # 'arm', 'leg', 'both', 'speech', 'cognitive'
    medical_history: Optional[str] = None


# Exercise response model
class ExerciseRecommendation(BaseModel):
    exercise_id: str
    name: str
    target_deficit: str
    body_region: str
    difficulty: int
    instructions: str
    progression_reps: str
    safety_notes: str


# Recovery Prediction response model
class RecoveryPrediction(BaseModel):
    recovery_probability: float
    difficulty_level: int
    recommended_exercises: List[ExerciseRecommendation]
    confidence_score: float
    clinical_notes: str
    model_version: str = "1.0"


# IST Feature Encoding Functions
def encode_stroke_subtype_onehot(stroke_subtype: str) -> dict:
    """Encode IST stroke subtype to one-hot encoding (matches training data)"""
    # Initialize all to 0
    encoding = {
        'TYPE_TACS': 0,
        'TYPE_PACS': 0,
        'TYPE_LACS': 0,
        'TYPE_POCS': 0,
        'TYPE_OTH': 0
    }
    
    # Set the appropriate type to 1
    mapping = {
        'TACS': 'TYPE_TACS',
        'PACS': 'TYPE_PACS',
        'LACS': 'TYPE_LACS',
        'POCS': 'TYPE_POCS',
        'OTH': 'TYPE_OTH'
    }
    
    key = mapping.get(stroke_subtype, 'TYPE_OTH')
    encoding[key] = 1
    return encoding


def encode_conscious_state_onehot(conscious_state: str) -> dict:
    """Encode conscious state to one-hot encoding (matches training data)"""
    # Initialize all to 0
    encoding = {
        'CONSC_F': 0,  # Fully alert
        'CONSC_D': 0,  # Drowsy
        'CONSC_U': 0   # Unconscious
    }
    
    # Set the appropriate state to 1
    mapping = {
        'Alert': 'CONSC_F',
        'Drowsy': 'CONSC_D',
        'Unconscious': 'CONSC_U'
    }
    
    key = mapping.get(conscious_state, 'CONSC_F')
    encoding[key] = 1
    return encoding


def determine_difficulty_level(recovery_probability: float, rsbp: Optional[int] = None) -> int:
    """Determine difficulty level based on recovery probability and blood pressure"""
    # Base difficulty from recovery probability
    if recovery_probability < 0.2:
        base_difficulty = 1  # Very Easy
    elif recovery_probability < 0.4:
        base_difficulty = 2  # Easy
    elif recovery_probability < 0.6:
        base_difficulty = 3  # Moderate
    elif recovery_probability < 0.8:
        base_difficulty = 4  # Hard
    else:
        base_difficulty = 5  # Very Hard
    
    # Apply blood pressure safety rule
    if rsbp and rsbp > 160:
        # Cap difficulty at 2 for high blood pressure
        return min(base_difficulty, 2)
    
    return base_difficulty


def get_deficit_to_exercise_mapping() -> dict:
    """Map functional deficits to specific target deficits in exercise library"""
    return {
        'rdef1': ['Facial Exercises', 'Speech/Swallowing'],  # Face Deficit
        'rdef2': ['Strength/ROM', 'Fine Motor', 'Grip Strength'],  # Arm/Hand Deficit
        'rdef3': ['Mobility', 'Balance', 'Functional Strength'],  # Leg/Foot Deficit
        'rdef4': ['Speech Therapy', 'Speech/Swallowing'],  # Dysphasia (Speech)
        'rdef5': ['Vision'],  # Hemianopia (Vision)
        'rdef6': ['Coordination', 'Balance'],  # Visuospatial Disorder
        'rdef7': ['Balance', 'Coordination'],  # Brainstem/Cerebellar Signs
        'rdef8': ['General', 'Coordination', 'Functional Mobility']  # Other Deficits
    }


def select_exercises_from_library(clinical_data: ISTClinicalData, difficulty_level: int) -> List[ExerciseRecommendation]:
    """
    Select personalized exercises from library based on deficits and difficulty level.
    Ensures diverse exercises are recommended for each specific deficit.
    """
    if exercise_library is None:
        return []
    
    selected_exercises = []
    selected_exercise_ids = set()  # Track selected exercises to avoid duplicates
    deficit_mapping = get_deficit_to_exercise_mapping()
    
    # Identify active deficits with their descriptions
    deficits = []
    if clinical_data.rdef1:
        deficits.append(('rdef1', 'Face Deficit'))
    if clinical_data.rdef2:
        deficits.append(('rdef2', 'Arm/Hand Deficit'))
    if clinical_data.rdef3:
        deficits.append(('rdef3', 'Leg/Foot Deficit'))
    if clinical_data.rdef4:
        deficits.append(('rdef4', 'Dysphasia (Speech)'))
    if clinical_data.rdef5:
        deficits.append(('rdef5', 'Hemianopia (Vision)'))
    if clinical_data.rdef6:
        deficits.append(('rdef6', 'Visuospatial Disorder'))
    if clinical_data.rdef7:
        deficits.append(('rdef7', 'Brainstem/Cerebellar Signs'))
    if clinical_data.rdef8:
        deficits.append(('rdef8', 'Other Deficits'))
    
    # If no deficits, recommend general exercises
    if not deficits:
        deficits = [('general', 'General Recovery')]
    
    # Select exercises for each deficit
    for deficit_key, deficit_name in deficits:
        target_deficits = deficit_mapping.get(deficit_key, ['General'])
        exercises_found_for_deficit = 0
        
        print(f"DEBUG: Processing deficit {deficit_key} ({deficit_name}), looking for target deficits: {target_deficits}")
        
        # Try to find exercises matching the target deficits
        for target_deficit in target_deficits:
            if exercises_found_for_deficit >= 1:  # Max 1 exercise per deficit
                break
            
            # Filter exercises by target deficit and difficulty
            matching_exercises = exercise_library[
                (exercise_library['Target Deficit'].str.contains(target_deficit, case=False, na=False)) &
                (exercise_library['Difficulty'] <= difficulty_level)
            ]
            
            print(f"DEBUG: Found {len(matching_exercises)} exercises for target deficit '{target_deficit}' at difficulty <= {difficulty_level}")
            
            # Select exercises that haven't been selected yet
            for _, exercise in matching_exercises.iterrows():
                exercise_id = exercise.get('Exercise ID', '')
                
                # Skip if already selected
                if exercise_id in selected_exercise_ids:
                    continue
                
                try:
                    selected_exercises.append(ExerciseRecommendation(
                        exercise_id=exercise_id,
                        name=exercise.get('Name', 'Unknown Exercise'),
                        target_deficit=deficit_name,
                        body_region=exercise.get('Body Region', 'General'),
                        difficulty=int(exercise.get('Difficulty', difficulty_level)),
                        instructions=exercise.get('Instructions', 'Follow standard protocol'),
                        progression_reps=exercise.get('Progression Reps', '3 sets of 10 reps'),
                        safety_notes=exercise.get('Safety Notes', 'Follow safety guidelines')
                    ))
                    selected_exercise_ids.add(exercise_id)
                    exercises_found_for_deficit += 1
                    break
                except Exception as e:
                    print(f"Error processing exercise: {e}")
                    continue
        
        # If no specific exercises found, try broader search
        if exercises_found_for_deficit == 0:
            matching_exercises = exercise_library[
                (exercise_library['Difficulty'] <= difficulty_level)
            ]
            
            for _, exercise in matching_exercises.iterrows():
                exercise_id = exercise.get('Exercise ID', '')
                
                # Skip if already selected
                if exercise_id in selected_exercise_ids:
                    continue
                
                try:
                    selected_exercises.append(ExerciseRecommendation(
                        exercise_id=exercise_id,
                        name=exercise.get('Name', 'Unknown Exercise'),
                        target_deficit=deficit_name,
                        body_region=exercise.get('Body Region', 'General'),
                        difficulty=int(exercise.get('Difficulty', difficulty_level)),
                        instructions=exercise.get('Instructions', 'Follow standard protocol'),
                        progression_reps=exercise.get('Progression Reps', '3 sets of 10 reps'),
                        safety_notes=exercise.get('Safety Notes', 'Follow safety guidelines')
                    ))
                    selected_exercise_ids.add(exercise_id)
                    break
                except Exception as e:
                    print(f"Error processing exercise: {e}")
                    continue
    
    # Return up to 5 unique exercises total
    return selected_exercises[:5]


def generate_clinical_notes(clinical_data: ISTClinicalData, recovery_probability: float, difficulty_level: int) -> str:
    """Generate clinical notes based on patient data and predictions"""
    notes = []
    
    # Recovery probability assessment
    if recovery_probability > 0.8:
        notes.append("Patient shows excellent recovery potential.")
    elif recovery_probability > 0.6:
        notes.append("Patient shows good recovery potential.")
    elif recovery_probability > 0.4:
        notes.append("Patient shows moderate recovery potential.")
    else:
        notes.append("Patient shows limited recovery potential. Conservative approach recommended.")
    
    # Blood pressure assessment
    if clinical_data.rsbp and clinical_data.rsbp > 160:
        notes.append("⚠️ High systolic blood pressure (>160 mmHg). Conservative, low-intensity plan recommended. Frequent monitoring advised.")
    
    # Consciousness state assessment
    if clinical_data.conscious_state == 'Unconscious':
        notes.append("Patient is unconscious. Focus on passive exercises and caregiver involvement.")
    elif clinical_data.conscious_state == 'Drowsy':
        notes.append("Patient is drowsy. Shorter sessions with frequent breaks recommended.")
    
    # Stroke severity assessment
    if clinical_data.stroke_subtype == 'TACS':
        notes.append("Total Anterior Circulation Stroke (high severity). Extended rehabilitation period expected.")
    elif clinical_data.stroke_subtype == 'LACS':
        notes.append("Lacunar Stroke (small vessel). Better prognosis expected with appropriate rehabilitation.")
    
    # Deficit assessment
    deficit_count = sum([
        clinical_data.rdef1, clinical_data.rdef2, clinical_data.rdef3,
        clinical_data.rdef4, clinical_data.rdef5, clinical_data.rdef6,
        clinical_data.rdef7, clinical_data.rdef8
    ])
    
    if deficit_count > 5:
        notes.append(f"Multiple deficits detected ({deficit_count}). Comprehensive rehabilitation plan required.")
    
    return " ".join(notes) if notes else "Standard rehabilitation protocol recommended."


@app.get("/")
async def root():
    """Health check endpoint"""
    return {
        "status": "running",
        "service": "Stroke Rehabilitation ML Service",
        "version": "1.0.0",
        "model_loaded": model is not None,
        "exercise_library_loaded": exercise_library is not None
    }


@app.post("/predict", response_model=RecoveryPrediction)
async def predict_recovery_ist(clinical_data: ISTClinicalData):
    """
    Predict recovery probability and recommend personalized rehabilitation plan
    using IST clinical features
    
    Args:
        clinical_data: IST dataset clinical features
    
    Returns:
        RecoveryPrediction with probability, difficulty level, exercises, and clinical notes
    """
    
    try:
        if model is None:
            # Demo mode: return simulated predictions
            recovery_probability = simulate_ist_prediction(clinical_data)
            confidence_score = 0.75
        else:
            # Use actual ML model
            try:
                # Encode categorical features to match training data format
                stroke_encoding = encode_stroke_subtype_onehot(clinical_data.stroke_subtype)
                conscious_encoding = encode_conscious_state_onehot(clinical_data.conscious_state)
                
                # Prepare features in the exact order expected by the trained model
                # Order: AGE, SEX, RSBP, RDEF1-8, TYPE_LACS, TYPE_OTH, TYPE_PACS, TYPE_POCS, TYPE_TACS, CONSC_D, CONSC_F, CONSC_U
                features = np.array([[
                    clinical_data.age,
                    clinical_data.gender,
                    clinical_data.rsbp if clinical_data.rsbp else 0,
                    int(clinical_data.rdef1),
                    int(clinical_data.rdef2),
                    int(clinical_data.rdef3),
                    int(clinical_data.rdef4),
                    int(clinical_data.rdef5),
                    int(clinical_data.rdef6),
                    int(clinical_data.rdef7),
                    int(clinical_data.rdef8),
                    stroke_encoding['TYPE_LACS'],
                    stroke_encoding['TYPE_OTH'],
                    stroke_encoding['TYPE_PACS'],
                    stroke_encoding['TYPE_POCS'],
                    stroke_encoding['TYPE_TACS'],
                    conscious_encoding['CONSC_D'],
                    conscious_encoding['CONSC_F'],
                    conscious_encoding['CONSC_U'],
                ]])
                
                # Get prediction from Random Forest model
                recovery_probability = float(model.predict_proba(features)[0][1])
                
                # Get confidence score (max probability from the model)
                confidence_score = float(max(model.predict_proba(features)[0]))
                
            except Exception as e:
                print(f"Model prediction error: {e}")
                # Fallback to simulation
                recovery_probability = simulate_ist_prediction(clinical_data)
                confidence_score = 0.75
        
        # Determine difficulty level based on recovery probability and blood pressure
        difficulty_level = determine_difficulty_level(recovery_probability, clinical_data.rsbp)
        
        # Select personalized exercises from library
        recommended_exercises = select_exercises_from_library(clinical_data, difficulty_level)
        
        # Generate clinical notes
        clinical_notes = generate_clinical_notes(clinical_data, recovery_probability, difficulty_level)
        
        return RecoveryPrediction(
            recovery_probability=round(recovery_probability, 3),
            difficulty_level=difficulty_level,
            recommended_exercises=recommended_exercises,
            confidence_score=round(confidence_score, 3),
            clinical_notes=clinical_notes,
            model_version="1.0"
        )
    
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Prediction error: {str(e)}")


def simulate_ist_prediction(clinical_data: ISTClinicalData) -> float:
    """
    Simulate ML prediction when model is not available
    Used for demo purposes with IST clinical features
    """
    # Base probability
    base_prob = 0.5
    
    # Age factor (younger = better recovery)
    age_factor = max(0, (80 - clinical_data.age) / 80) * 0.25
    
    # Stroke subtype factor
    stroke_factors = {
        'TACS': -0.15,  # High severity
        'PACS': -0.05,  # Partial
        'LACS': 0.15,   # Better prognosis
        'POCS': -0.05,  # Posterior
        'OTH': 0.0      # Other
    }
    stroke_factor = stroke_factors.get(clinical_data.stroke_subtype, 0)
    
    # Conscious state factor
    conscious_factors = {
        'Alert': 0.1,
        'Drowsy': -0.05,
        'Unconscious': -0.15
    }
    conscious_factor = conscious_factors.get(clinical_data.conscious_state, 0)
    
    # Blood pressure factor
    bp_factor = 0
    if clinical_data.rsbp:
        if clinical_data.rsbp > 160:
            bp_factor = -0.1
        elif clinical_data.rsbp < 120:
            bp_factor = 0.05
    
    # Functional deficit factor (fewer deficits = better recovery)
    deficit_count = sum([
        clinical_data.rdef1, clinical_data.rdef2, clinical_data.rdef3,
        clinical_data.rdef4, clinical_data.rdef5, clinical_data.rdef6,
        clinical_data.rdef7, clinical_data.rdef8
    ])
    deficit_factor = -0.05 * deficit_count
    
    # Calculate final probability
    probability = base_prob + age_factor + stroke_factor + conscious_factor + bp_factor + deficit_factor
    
    # Clamp between 0 and 1
    return max(0.0, min(1.0, probability))


# Legacy endpoint for backward compatibility
@app.post("/predict-legacy", response_model=RecoveryPrediction)
async def predict_recovery_legacy(patient_data: PatientData):
    """
    Legacy prediction endpoint (deprecated)
    Use /predict with ISTClinicalData instead
    """
    # Convert legacy format to IST format
    clinical_data = ISTClinicalData(
        age=patient_data.age,
        gender=0,  # Default
        rsbp=None,
        stroke_subtype='OTH',  # Default
        conscious_state='Alert',  # Default
        rdef1=False,
        rdef2=patient_data.deficit_area in ['arm', 'both'],
        rdef3=patient_data.deficit_area in ['leg', 'both'],
        rdef4=patient_data.deficit_area == 'speech',
        rdef5=False,
        rdef6=False,
        rdef7=False,
        rdef8=patient_data.deficit_area == 'cognitive'
    )
    
    return await predict_recovery_ist(clinical_data)


@app.post("/batch-predict")
async def batch_predict(patients: list[ISTClinicalData]):
    """
    Predict recovery for multiple patients
    """
    predictions = []
    for patient in patients:
        prediction = await predict_recovery_ist(patient)
        predictions.append(prediction)
    return {"predictions": predictions, "count": len(predictions)}


@app.get("/health")
async def health_check():
    """Detailed health check"""
    return {
        "status": "healthy",
        "model_loaded": model is not None,
        "exercise_library_loaded": exercise_library is not None,
        "service_version": "1.0.0",
        "endpoints": {
            "predict": "POST /predict (IST clinical features)",
            "batch_predict": "POST /batch-predict",
            "health": "GET /health",
            "root": "GET /"
        }
    }


@app.get("/model-info")
async def model_info():
    """Get information about the loaded model"""
    if model is None:
        return {
            "status": "not_loaded",
            "message": "ML model not loaded. Running in demo mode.",
            "demo_mode": True
        }
    
    return {
        "status": "loaded",
        "model_type": str(type(model).__name__),
        "model_path": MODEL_PATH,
        "demo_mode": False,
        "features_expected": [
            "age", "gender", "rsbp", "stroke_subtype_encoded",
            "conscious_state_encoded", "rdef1", "rdef2", "rdef3",
            "rdef4", "rdef5", "rdef6", "rdef7", "rdef8"
        ]
    }


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8001)
