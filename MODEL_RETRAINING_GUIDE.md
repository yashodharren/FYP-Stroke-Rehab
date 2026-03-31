# Model Retraining Guide - scikit-learn 1.5.2 Compatibility

## Problem
Your trained Random Forest model (`stroke_recovery_model.joblib`) was created with scikit-learn 1.2.1, but the FastAPI service is running scikit-learn 1.5.2. The internal tree node structure changed between versions, causing a deserialization error.

## Solution
Retrain your Random Forest model with scikit-learn 1.5.2 using your original training code.

---

## Step 1: Prepare Your Environment

Ensure you have scikit-learn 1.5.2 installed:
```bash
pip install scikit-learn==1.5.2
```

Verify installation:
```bash
python -c "import sklearn; print(sklearn.__version__)"
```

Expected output: `1.5.2`

---

## Step 2: Retrain the Model

Use your original training code with the correct feature order. Here's the complete retraining script:

```python
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import classification_report, accuracy_score, confusion_matrix
import pandas as pd
import joblib

# Load your processed data
df_final = pd.read_csv('processed_stroke_data.csv')

# 1. Define Features (X) and Target (y)
# Drop the target and helper columns
X = df_final.drop(columns=['FRECOVER', 'FDENNIS', 'EXPDD', 'AGE_GROUP'])
y = df_final['FRECOVER']

print(f"Features shape: {X.shape}")
print(f"Feature names: {list(X.columns)}")
print(f"Target shape: {y.shape}")

# 2. Split the data: 80% train, 20% test
X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.2, random_state=42
)

# 3. Initialize and Train the Random Forest
rf_model = RandomForestClassifier(
    n_estimators=100,
    max_depth=10,
    random_state=42,
    n_jobs=-1  # Use all CPU cores for faster training
)

print("Training Random Forest model...")
rf_model.fit(X_train, y_train)
print("✓ Model training complete!")

# 4. Evaluate the Model
y_pred = rf_model.predict(X_test)
accuracy = accuracy_score(y_test, y_pred)

print(f"\n--- Machine Learning Results ---")
print(f"Model Accuracy: {accuracy:.2%}")
print("\nDetailed Performance Report:")
print(classification_report(y_test, y_pred))

# 5. Feature Importance
importances = pd.Series(
    rf_model.feature_importances_, 
    index=X.columns
).sort_values(ascending=False)

print("\nTop 10 Features Influencing the Prediction:")
print(importances.head(10))

# 6. Save the model with scikit-learn 1.5.2
model_path = 'stroke_recovery_model.joblib'
joblib.dump(rf_model, model_path)
print(f"\n✓ Model saved to {model_path}")

# 7. Verify the model can be loaded
print("\nVerifying model can be loaded...")
loaded_model = joblib.load(model_path)
print(f"✓ Model loaded successfully!")
print(f"  - Model type: {type(loaded_model).__name__}")
print(f"  - Number of features: {loaded_model.n_features_in_}")
print(f"  - Feature names: {list(X.columns)}")
```

---

## Step 3: Run the Retraining Script

1. **Save the script** as `retrain_model.py` in your project directory
2. **Run it**:
   ```bash
   python retrain_model.py
   ```
3. **Expected output**:
   ```
   Features shape: (14792, 19)
   Feature names: ['AGE', 'SEX', 'RSBP', 'RDEF1', 'RDEF2', 'RDEF3', 'RDEF4', 'RDEF5', 'RDEF6', 'RDEF7', 'RDEF8', 'TYPE_LACS', 'TYPE_OTH', 'TYPE_PACS', 'TYPE_POCS', 'TYPE_TACS', 'CONSC_D', 'CONSC_F', 'CONSC_U']
   Training Random Forest model...
   ✓ Model training complete!
   
   --- Machine Learning Results ---
   Model Accuracy: XX.XX%
   
   ✓ Model saved to stroke_recovery_model.joblib
   ✓ Model loaded successfully!
   ```

---

## Step 4: Replace the Old Model

1. **Backup the old model** (optional):
   ```bash
   mv stroke_recovery_model.joblib stroke_recovery_model.joblib.old
   ```

2. **The new model is automatically saved** as `stroke_recovery_model.joblib`

---

## Step 5: Verify FastAPI Can Load the Model

1. **Restart the FastAPI service**:
   ```bash
   # Stop the current service
   # Start it again
   python main.py
   ```

2. **Check model status**:
   ```bash
   curl http://localhost:8001/model-info
   ```

3. **Expected response** (model loaded):
   ```json
   {
     "status": "loaded",
     "model_type": "RandomForestClassifier",
     "model_path": "stroke_recovery_model.joblib",
     "demo_mode": false,
     "features_expected": [
       "age", "gender", "rsbp", "stroke_subtype_encoded",
       "conscious_state_encoded", "rdef1", "rdef2", "rdef3",
       "rdef4", "rdef5", "rdef6", "rdef7", "rdef8"
     ]
   }
   ```

---

## Step 6: Test the Model with FastAPI

Once the model is loaded, test a prediction:

```bash
curl -X POST http://localhost:8001/predict \
  -H "Content-Type: application/json" \
  -d '{
    "age": 65,
    "gender": 1,
    "rsbp": 145,
    "stroke_subtype": "LACS",
    "conscious_state": "Alert",
    "rdef1": false,
    "rdef2": true,
    "rdef3": true,
    "rdef4": false,
    "rdef5": false,
    "rdef6": false,
    "rdef7": false,
    "rdef8": false
  }'
```

Expected response:
```json
{
  "recovery_probability": 0.XXX,
  "difficulty_level": 4,
  "recommended_exercises": [...],
  "confidence_score": 0.XXX,
  "clinical_notes": "...",
  "model_version": "1.0"
}
```

---

## Feature Mapping Reference

Your trained model expects features in this exact order:

| Position | Feature | Type | Values |
|----------|---------|------|--------|
| 0 | AGE | int | 0-100 |
| 1 | SEX | int | 0 (Female), 1 (Male) |
| 2 | RSBP | int | Systolic BP in mmHg |
| 3-10 | RDEF1-8 | int | 0 (absent), 1 (present) |
| 11 | TYPE_LACS | int | 0 or 1 (one-hot) |
| 12 | TYPE_OTH | int | 0 or 1 (one-hot) |
| 13 | TYPE_PACS | int | 0 or 1 (one-hot) |
| 14 | TYPE_POCS | int | 0 or 1 (one-hot) |
| 15 | TYPE_TACS | int | 0 or 1 (one-hot) |
| 16 | CONSC_D | int | 0 or 1 (one-hot) |
| 17 | CONSC_F | int | 0 or 1 (one-hot) |
| 18 | CONSC_U | int | 0 or 1 (one-hot) |

**Total: 19 features**

---

## FastAPI Feature Conversion

The FastAPI service automatically converts IST clinical input to this format:

```
IST Input                  →  Model Features
age: 65                    →  AGE: 65
gender: 1                  →  SEX: 1
rsbp: 145                  →  RSBP: 145
stroke_subtype: "LACS"     →  TYPE_LACS: 1, TYPE_OTH: 0, TYPE_PACS: 0, TYPE_POCS: 0, TYPE_TACS: 0
conscious_state: "Alert"   →  CONSC_D: 0, CONSC_F: 1, CONSC_U: 0
rdef1-8: [bool]            →  RDEF1-8: [0 or 1]
```

---

## Troubleshooting

### Issue: "ModuleNotFoundError: No module named 'sklearn'"
**Solution**: Install scikit-learn
```bash
pip install scikit-learn==1.5.2
```

### Issue: "Model Accuracy: 0%"
**Solution**: Check that your data is loaded correctly and features are in the right format

### Issue: Model still won't load in FastAPI
**Solution**: Ensure the model file is in the `ml_service/` directory and scikit-learn 1.5.2 is installed

### Issue: Different accuracy than before
**Solution**: This is normal. Different scikit-learn versions may produce slightly different results due to internal algorithm changes. The model should still be clinically valid.

---

## Summary

1. Install scikit-learn 1.5.2
2. Run the retraining script with your original data
3. Replace the old model file
4. Restart FastAPI service
5. Verify with `/model-info` endpoint
6. Test with `/predict` endpoint

Once complete, your Random Forest model will be fully integrated with the FastAPI service and ready for Laravel integration!
