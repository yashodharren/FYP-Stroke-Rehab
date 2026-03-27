from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import Optional
import joblib
import numpy as np
import os

app = FastAPI(title="Stroke Rehabilitation ML Service", version="1.0.0")

# Enable CORS for Laravel frontend
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Model path - will be updated when you add your .joblib file
MODEL_PATH = "models/stroke_recovery_model.joblib"

# Load model if it exists
model = None
if os.path.exists(MODEL_PATH):
    try:
        model = joblib.load(MODEL_PATH)
        print(f"Model loaded successfully from {MODEL_PATH}")
    except Exception as e:
        print(f"Error loading model: {e}")
else:
    print(f"Model file not found at {MODEL_PATH}")
    print("The service will run in demo mode without the ML model")


class PatientData(BaseModel):
    age: int
    stroke_type: str  # 'ischemic', 'hemorrhagic', 'tia'
    deficit_area: str  # 'arm', 'leg', 'both', 'speech', 'cognitive'
    medical_history: Optional[str] = None


class RecoveryPrediction(BaseModel):
    recovery_probability: float
    difficulty_level: int
    recommended_exercises: list
    confidence_score: float


def encode_stroke_type(stroke_type: str) -> int:
    """Encode stroke type to numeric value"""
    mapping = {'ischemic': 0, 'hemorrhagic': 1, 'tia': 2}
    return mapping.get(stroke_type, 0)


def encode_deficit_area(deficit_area: str) -> int:
    """Encode deficit area to numeric value"""
    mapping = {'arm': 0, 'leg': 1, 'both': 2, 'speech': 3, 'cognitive': 4}
    return mapping.get(deficit_area, 0)


def determine_difficulty_level(recovery_probability: float) -> int:
    """Determine difficulty level based on recovery probability"""
    if recovery_probability < 0.2:
        return 1  # Very Easy
    elif recovery_probability < 0.4:
        return 2  # Easy
    elif recovery_probability < 0.6:
        return 3  # Moderate
    elif recovery_probability < 0.8:
        return 4  # Hard
    else:
        return 5  # Very Hard


def get_recommended_exercises(difficulty_level: int, deficit_area: str) -> list:
    """Get recommended exercises based on difficulty and deficit area"""
    exercise_mapping = {
        ('arm', 1): ['Hand Grip Exercises', 'Arm Raises'],
        ('arm', 2): ['Arm Raises', 'Resistance Band Exercises'],
        ('arm', 3): ['Resistance Band Exercises'],
        ('arm', 4): ['Resistance Band Exercises'],
        ('arm', 5): ['Resistance Band Exercises'],
        ('leg', 1): ['Seated Marching', 'Walking with Support'],
        ('leg', 2): ['Leg Lifts', 'Walking with Support'],
        ('leg', 3): ['Leg Lifts'],
        ('leg', 4): ['Leg Lifts'],
        ('leg', 5): ['Leg Lifts'],
        ('both', 1): ['Seated Marching', 'Arm Raises'],
        ('both', 2): ['Leg Lifts', 'Arm Raises'],
        ('both', 3): ['Leg Lifts', 'Resistance Band Exercises'],
        ('both', 4): ['Leg Lifts', 'Resistance Band Exercises'],
        ('both', 5): ['Leg Lifts', 'Resistance Band Exercises'],
        ('speech', 1): ['Speech Exercises'],
        ('speech', 2): ['Speech Exercises'],
        ('speech', 3): ['Speech Exercises'],
        ('speech', 4): ['Speech Exercises'],
        ('speech', 5): ['Speech Exercises'],
        ('cognitive', 1): ['Cognitive Exercises'],
        ('cognitive', 2): ['Cognitive Exercises'],
        ('cognitive', 3): ['Cognitive Exercises'],
        ('cognitive', 4): ['Cognitive Exercises'],
        ('cognitive', 5): ['Cognitive Exercises'],
    }
    
    key = (deficit_area, difficulty_level)
    return exercise_mapping.get(key, ['Seated Marching', 'Hand Grip Exercises'])


@app.get("/")
async def root():
    """Health check endpoint"""
    return {
        "status": "running",
        "service": "Stroke Rehabilitation ML Service",
        "version": "1.0.0",
        "model_loaded": model is not None
    }


@app.post("/predict", response_model=RecoveryPrediction)
async def predict_recovery(patient_data: PatientData):
    """
    Predict recovery probability and recommend rehabilitation plan
    
    Args:
        patient_data: Patient information including age, stroke type, and deficit area
    
    Returns:
        RecoveryPrediction with probability, difficulty level, and recommended exercises
    """
    
    if model is None:
        # Demo mode: return simulated predictions
        recovery_probability = simulate_prediction(patient_data)
    else:
        # Use actual ML model
        try:
            # Prepare features for model
            features = np.array([[
                patient_data.age,
                encode_stroke_type(patient_data.stroke_type),
                encode_deficit_area(patient_data.deficit_area)
            ]])
            
            # Get prediction
            recovery_probability = float(model.predict_proba(features)[0][1])
        except Exception as e:
            raise HTTPException(status_code=500, detail=f"Prediction error: {str(e)}")
    
    # Determine difficulty level based on recovery probability
    difficulty_level = determine_difficulty_level(recovery_probability)
    
    # Get recommended exercises
    recommended_exercises = get_recommended_exercises(difficulty_level, patient_data.deficit_area)
    
    # Calculate confidence score
    confidence_score = abs(recovery_probability - 0.5) * 2 + 0.5
    
    return RecoveryPrediction(
        recovery_probability=round(recovery_probability, 2),
        difficulty_level=difficulty_level,
        recommended_exercises=recommended_exercises,
        confidence_score=round(confidence_score, 2)
    )


def simulate_prediction(patient_data: PatientData) -> float:
    """
    Simulate ML prediction when model is not available
    Used for demo purposes
    """
    # Base probability
    base_prob = 0.5
    
    # Age factor (younger = better recovery)
    age_factor = max(0, (80 - patient_data.age) / 80) * 0.3
    
    # Stroke type factor
    stroke_factor = 0.1 if patient_data.stroke_type == 'ischemic' else -0.1
    
    # Deficit area factor
    deficit_factor = {
        'arm': 0.15,
        'leg': 0.1,
        'both': -0.05,
        'speech': 0.05,
        'cognitive': 0.0
    }.get(patient_data.deficit_area, 0)
    
    # Calculate final probability
    probability = base_prob + age_factor + stroke_factor + deficit_factor
    
    # Clamp between 0 and 1
    return max(0.0, min(1.0, probability))


@app.post("/batch-predict")
async def batch_predict(patients: list[PatientData]):
    """
    Predict recovery for multiple patients
    """
    predictions = []
    for patient in patients:
        prediction = await predict_recovery(patient)
        predictions.append(prediction)
    return {"predictions": predictions}


@app.get("/health")
async def health_check():
    """Detailed health check"""
    return {
        "status": "healthy",
        "model_loaded": model is not None,
        "service_version": "1.0.0"
    }


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8001)
