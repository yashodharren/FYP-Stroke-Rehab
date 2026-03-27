# Stroke Rehabilitation ML Microservice

FastAPI microservice for predicting stroke patient recovery probability and recommending rehabilitation plans.

## Setup

### 1. Create Virtual Environment
```bash
python -m venv venv
venv\Scripts\activate
```

### 2. Install Dependencies
```bash
pip install -r requirements.txt
```

### 3. Add Your ML Model
Place your `stroke_recovery_model.joblib` file in the `models/` directory:
```
ml_service/
├── models/
│   └── stroke_recovery_model.joblib
├── main.py
├── requirements.txt
└── README.md
```

### 4. Run the Service
```bash
python main.py
```

The service will start on `http://localhost:8001`

## API Endpoints

### Health Check
```
GET /
GET /health
```

### Single Patient Prediction
```
POST /predict
Content-Type: application/json

{
  "age": 70,
  "stroke_type": "ischemic",
  "deficit_area": "leg",
  "medical_history": "Hypertension, Diabetes Type 2"
}
```

Response:
```json
{
  "recovery_probability": 0.65,
  "difficulty_level": 3,
  "recommended_exercises": ["Leg Lifts", "Walking with Support"],
  "confidence_score": 0.80
}
```

### Batch Prediction
```
POST /batch-predict
Content-Type: application/json

[
  {
    "age": 70,
    "stroke_type": "ischemic",
    "deficit_area": "leg"
  },
  {
    "age": 65,
    "stroke_type": "hemorrhagic",
    "deficit_area": "arm"
  }
]
```

## Demo Mode

If the ML model file is not found, the service runs in **demo mode** using simulated predictions based on:
- Patient age
- Stroke type
- Deficit area

This allows testing the API without the actual ML model.

## Integration with Laravel

The Laravel application calls this service from the plan generator:

```php
$response = Http::post('http://localhost:8001/predict', [
    'age' => $patient->age,
    'stroke_type' => $patient->stroke_type,
    'deficit_area' => $patient->deficit_area,
    'medical_history' => $patient->medical_history,
]);

$prediction = $response->json();
```

## Model Features

The ML model expects the following input features:
1. **Age** (integer): Patient age in years
2. **Stroke Type** (encoded): 0=ischemic, 1=hemorrhagic, 2=tia
3. **Deficit Area** (encoded): 0=arm, 1=leg, 2=both, 3=speech, 4=cognitive

## Output

- **recovery_probability**: Float between 0 and 1 (0=low recovery, 1=high recovery)
- **difficulty_level**: Integer 1-5 (1=very easy, 5=very hard)
- **recommended_exercises**: List of exercise names recommended for the patient
- **confidence_score**: Float between 0 and 1 indicating prediction confidence
