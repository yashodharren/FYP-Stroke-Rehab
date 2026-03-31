import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import classification_report, accuracy_score, confusion_matrix
import joblib

print("=" * 60)
print("STROKE RECOVERY MODEL RETRAINING")
print("=" * 60)

# Step 1: Load the pre-processed dataset
print("\n[1/5] Loading pre-processed IST dataset...")
df_final = pd.read_csv('c:/Users/dharr/Desktop/University Files/Uni Sem 8/CAT405/FYP/ml_service/processed_stroke_data.csv')
print(f"✓ Dataset loaded: {df_final.shape[0]} rows, {df_final.shape[1]} columns")
print(f"✓ Feature names: {list(df_final.columns)}")

# Step 2: Prepare features and target
print("\n[2/5] Preparing training data...")
X = df_final.drop(columns=['FRECOVER', 'FDENNIS', 'EXPDD', 'AGE_GROUP'], errors='ignore')
y = df_final['FRECOVER']

print(f"✓ Features (X): {X.shape}")
print(f"✓ Target (y): {y.shape}")
print(f"✓ Feature order: {list(X.columns)}")

# Step 7: Split the data
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
print(f"✓ Train set: {X_train.shape[0]} samples")
print(f"✓ Test set: {X_test.shape[0]} samples")

# Step 8: Train the Random Forest model
print("\n[4/5] Training Random Forest model...")
print("  (This may take 1-2 minutes...)")

rf_model = RandomForestClassifier(
    n_estimators=100,
    max_depth=10,
    random_state=42,
    n_jobs=-1,  # Use all CPU cores
    verbose=0
)

rf_model.fit(X_train, y_train)
print("✓ Model training complete!")

# Step 9: Evaluate the model
print("\n[5/5] Evaluating model performance...")
y_pred = rf_model.predict(X_test)
accuracy = accuracy_score(y_test, y_pred)

print(f"\n{'=' * 60}")
print(f"MODEL ACCURACY: {accuracy:.2%}")
print(f"{'=' * 60}")

print("\nDetailed Classification Report:")
print(classification_report(y_test, y_pred))

print("\nConfusion Matrix:")
print(confusion_matrix(y_test, y_pred))

# Feature importance
importances = pd.Series(rf_model.feature_importances_, index=X.columns).sort_values(ascending=False)
print("\nTop 10 Most Important Features:")
for i, (feature, importance) in enumerate(importances.head(10).items(), 1):
    print(f"  {i}. {feature}: {importance:.4f}")

# Step 10: Save the model
print("\n" + "=" * 60)
print("SAVING MODEL")
print("=" * 60)

model_path = 'c:/Users/dharr/Desktop/University Files/Uni Sem 8/CAT405/FYP/ml_service/stroke_recovery_model.joblib'
features_path = 'c:/Users/dharr/Desktop/University Files/Uni Sem 8/CAT405/FYP/ml_service/model_features.joblib'

joblib.dump(rf_model, model_path)
joblib.dump(list(X.columns), features_path)

print(f"✓ Model saved to: {model_path}")
print(f"✓ Features saved to: {features_path}")

# Step 11: Verify the model can be loaded
print("\nVerifying model can be loaded...")
try:
    loaded_model = joblib.load(model_path)
    loaded_features = joblib.load(features_path)
    print(f"✓ Model loaded successfully!")
    print(f"  - Model type: {type(loaded_model).__name__}")
    print(f"  - Number of features: {loaded_model.n_features_in_}")
    print(f"  - Expected features: {len(loaded_features)}")
    print(f"  - Feature names match: {loaded_model.n_features_in_ == len(loaded_features)}")
except Exception as e:
    print(f"✗ Error loading model: {e}")

print("\n" + "=" * 60)
print("RETRAINING COMPLETE!")
print("=" * 60)
print("\nNext steps:")
print("1. Restart the FastAPI service")
print("2. Check http://localhost:8001/model-info")
print("3. Test with http://localhost:8001/predict")
print("\nThe model is now ready for Laravel integration!")
