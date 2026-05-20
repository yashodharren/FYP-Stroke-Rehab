# Database Design — ERD & Data Dictionary
## Intelligent Post-Stroke Patient Rehabilitation Plan Generator

---

## ERD (Entity Relationship Diagram)

```
┌─────────────────┐         ┌──────────────────┐
│     users        │         │    clinicians     │
│─────────────────│         │──────────────────│
│ PK id           │◄────────│ PK id            │
│    name         │  1   1  │ FK user_id       │
│    email        │         │    specialization │
│    password     │         │    hospital_aff.. │
│    role (enum)  │         │    phone          │
│    created_at   │         │    license_number │
│    updated_at   │         │    bio            │
└────────┬────────┘         │    is_verified    │
         │1                 └──────────────────┘
         │
         │1                 ┌──────────────────┐
    ┌────▼────────────┐     │clinician_messages│
    │    patients     │     │──────────────────│
    │─────────────────│     │ PK id            │
    │ PK id           │     │ FK clinician_id──┼──► users.id
    │ FK user_id      │◄────┤ FK patient_id    │
    │ FK clinician_id─┼──►users.id             │
    │    age          │1    │    message        │
    │    gender       │     │    type           │
    │    rsbp         │     │    created_at     │
    │    stroke_subtype│    └──────────────────┘
    │    conscious_state│
    │    recovery_status│
    │    rdef1..rdef8 │
    └────────┬────────┘
             │1
             │
    ┌────────▼────────────────────────────────┐
    │              rehab_plans                 │
    │─────────────────────────────────────────│
    │ PK id                                    │
    │ FK patient_id                            │
    │ FK clinician_id ──────────────────► users.id
    │    plan_name                             │
    │    description                           │
    │    recovery_probability                  │
    │    ml_confidence_score                   │
    │    difficulty_level (enum 1-5)           │
    │    start_date                            │
    │    end_date                              │
    │    status (enum)                         │
    │    ml_metadata (JSON)                    │
    │    feedback_requested                    │
    │    feedback_requested_at                 │
    └──────────────┬──────────────────────────┘
                   │1
                   │
    ┌──────────────▼──────────────┐    ┌─────────────────────┐
    │       plan_exercises         │    │      exercises       │
    │─────────────────────────────│    │─────────────────────│
    │ PK id                        │    │ PK id               │
    │ FK rehab_plan_id             │    │    name             │
    │ FK exercise_id ──────────────┼───►│    description      │
    │    day_of_week (enum)        │    │    difficulty_level │
    │    frequency_per_week        │    │    target_area      │
    │    scheduled_time            │    │    duration_minutes │
    │    scheduled_times (JSON)    │    │    repetitions      │
    │    custom_repetitions        │    │    instructions     │
    │    custom_duration_minutes   │    │    image_url        │
    │    notes                     │    │    video_url        │
    │    is_completed              │    └─────────────────────┘
    │    completed_at              │
    └──────────────┬──────────────┘
                   │1
                   │
    ┌──────────────▼──────────────────────────┐
    │            patient_feedback              │
    │─────────────────────────────────────────│
    │ PK id                                    │
    │ FK patient_id ──────────────────────────► patients.id
    │ FK plan_exercise_id ────────────────────► plan_exercises.id
    │ FK rehab_plan_id ───────────────────────► rehab_plans.id
    │    pain_level (0-10)                     │
    │    difficulty_rating (1-5)               │
    │    mood_rating                           │
    │    comments                              │
    │    overall_comments                      │
    │    is_plan_feedback                      │
    │    completed_exercise                    │
    │    feedback_date                         │
    └──────────────────────────────────────────┘
```

### Relationships Summary

- `users` **1:1** `clinicians` — each clinician user has one profile record
- `users` **1:1** `patients` — each patient user has one profile record
- `users` (clinician) **1:M** `patients` — a clinician manages many patients
- `patients` **1:M** `rehab_plans` — a patient can have multiple plans over time
- `users` (clinician) **1:M** `rehab_plans` — a clinician authors many plans
- `rehab_plans` **1:M** `plan_exercises` — a plan contains many scheduled exercise entries
- `exercises` **1:M** `plan_exercises` — an exercise can appear in many plans
- `plan_exercises` **1:M** `patient_feedback` — each scheduled exercise can receive feedback
- `rehab_plans` **1:M** `patient_feedback` — plan-level feedback linked to a plan
- `users` (clinician) **1:M** `clinician_messages` — in-app notifications per clinician
- `patients` **1:M** `clinician_messages` — messages can reference a patient

---

## Data Dictionary

### Table: `users`

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Unique user identifier |
| `name` | VARCHAR(255) | NOT NULL | Full display name |
| `email` | VARCHAR(255) | NOT NULL, UNIQUE | Login email address |
| `email_verified_at` | TIMESTAMP | NULL | Email verification timestamp |
| `password` | VARCHAR(255) | NOT NULL | Bcrypt hashed password |
| `role` | ENUM | NOT NULL, DEFAULT `patient` | User role: `admin`, `clinician`, `patient` |
| `remember_token` | VARCHAR(100) | NULL | "Remember me" session token |
| `created_at` | TIMESTAMP | NULL | Record creation time |
| `updated_at` | TIMESTAMP | NULL | Last update time |

---

### Table: `clinicians`

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Clinician profile ID |
| `user_id` | BIGINT UNSIGNED | FK → `users.id`, CASCADE | Linked user account |
| `specialization` | VARCHAR(255) | NULL | Medical specialization (e.g., Physiotherapy) |
| `hospital_affiliation` | VARCHAR(255) | NULL | Hospital or clinic name |
| `phone` | VARCHAR(255) | NULL | Contact phone number |
| `license_number` | VARCHAR(255) | UNIQUE, NULL | Professional licence number |
| `bio` | TEXT | NULL | Professional biography |
| `is_verified` | BOOLEAN | DEFAULT `false` | Admin approval status |
| `created_at` | TIMESTAMP | NULL | Record creation time |
| `updated_at` | TIMESTAMP | NULL | Last update time |

---

### Table: `patients`

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Patient profile ID |
| `user_id` | BIGINT UNSIGNED | FK → `users.id`, CASCADE | Linked user account |
| `clinician_id` | BIGINT UNSIGNED | FK → `users.id`, SET NULL, NULL | Assigned clinician |
| `age` | INTEGER | NULL | Patient age in years |
| `gender` | INTEGER | NULL | `0` = Female, `1` = Male |
| `rsbp` | INTEGER | NULL | Resting systolic blood pressure (mmHg) |
| `stroke_subtype` | VARCHAR(255) | NULL | IST stroke classification: `TACS`, `PACS`, `LACS`, `POCS`, `OTH` |
| `conscious_state` | VARCHAR(255) | NULL | Consciousness on admission: `Alert`, `Drowsy`, `Unconscious` |
| `recovery_status` | ENUM | DEFAULT `new` | `new`, `in_progress`, `completed`, `paused` |
| `rdef1` | BOOLEAN | DEFAULT `false` | Face deficit |
| `rdef2` | BOOLEAN | DEFAULT `false` | Arm/hand deficit |
| `rdef3` | BOOLEAN | DEFAULT `false` | Leg/foot deficit |
| `rdef4` | BOOLEAN | DEFAULT `false` | Dysphasia (speech deficit) |
| `rdef5` | BOOLEAN | DEFAULT `false` | Hemianopia (vision deficit) |
| `rdef6` | BOOLEAN | DEFAULT `false` | Visuospatial disorder |
| `rdef7` | BOOLEAN | DEFAULT `false` | Brainstem/cerebellar signs |
| `rdef8` | BOOLEAN | DEFAULT `false` | Other deficits |
| `created_at` | TIMESTAMP | NULL | Record creation time |
| `updated_at` | TIMESTAMP | NULL | Last update time |

---

### Table: `exercises`

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Exercise ID |
| `name` | VARCHAR(255) | NOT NULL | Exercise name |
| `description` | TEXT | NOT NULL | Brief description |
| `difficulty_level` | ENUM | NOT NULL | `1` (Very Easy) to `5` (Very Hard) |
| `target_area` | ENUM | NOT NULL | Body region: `upper_limb`, `lower_limb`, `face`, `coordination`, `core/upper`, `general`, etc. |
| `duration_minutes` | INTEGER | DEFAULT `15` | Default session duration |
| `repetitions` | INTEGER | DEFAULT `10` | Default repetition count |
| `instructions` | TEXT | NULL | Step-by-step exercise instructions |
| `image_url` | VARCHAR(255) | NULL | URL to exercise image |
| `video_url` | VARCHAR(255) | NULL | URL to exercise video |
| `created_at` | TIMESTAMP | NULL | Record creation time |
| `updated_at` | TIMESTAMP | NULL | Last update time |

---

### Table: `rehab_plans`

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Plan ID |
| `patient_id` | BIGINT UNSIGNED | FK → `patients.id`, CASCADE | Owning patient |
| `clinician_id` | BIGINT UNSIGNED | FK → `users.id`, CASCADE | Authoring clinician |
| `plan_name` | VARCHAR(255) | NOT NULL | Plan title |
| `description` | TEXT | NULL | Plan description/goals |
| `recovery_probability` | DECIMAL(3,2) | NULL | ML-predicted recovery probability (0.00–1.00) |
| `ml_confidence_score` | DECIMAL(3,2) | NULL | ML model confidence score (0.00–1.00) |
| `difficulty_level` | ENUM | DEFAULT `1` | Plan intensity: `1`–`5` |
| `start_date` | DATE | NOT NULL | Plan start date |
| `end_date` | DATE | NULL | Plan end date |
| `status` | ENUM | DEFAULT `draft` | `draft`, `active`, `completed`, `paused` |
| `ml_metadata` | JSON | NULL | Raw ML response payload stored for audit |
| `feedback_requested` | BOOLEAN | DEFAULT `false` | Whether patient has submitted end-of-plan feedback |
| `feedback_requested_at` | TIMESTAMP | NULL | Timestamp of feedback submission |
| `created_at` | TIMESTAMP | NULL | Record creation time |
| `updated_at` | TIMESTAMP | NULL | Last update time |

---

### Table: `plan_exercises`

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Scheduled exercise entry ID |
| `rehab_plan_id` | BIGINT UNSIGNED | FK → `rehab_plans.id`, CASCADE | Parent plan |
| `exercise_id` | BIGINT UNSIGNED | FK → `exercises.id`, CASCADE | Exercise reference |
| `day_of_week` | ENUM | NOT NULL | Scheduled day: `Monday`–`Sunday` |
| `frequency_per_week` | INTEGER | DEFAULT `3` | How many times per week prescribed |
| `scheduled_time` | TIME | NULL | Default time for this exercise |
| `scheduled_times` | JSON | NULL | Per-day time overrides (e.g. `{"Monday":"09:00","Wednesday":"10:00"}`) |
| `custom_repetitions` | INTEGER | NULL | Overrides exercise default repetitions |
| `custom_duration_minutes` | INTEGER | NULL | Overrides exercise default duration |
| `notes` | TEXT | NULL | Safety notes or clinician instructions |
| `is_completed` | BOOLEAN | DEFAULT `false` | Whether patient has marked as done |
| `| `completed_at` | TIMESTAMP | NULL | Timestamp when marked done |
` | TIMESTAMP | NULL | Timestamp when marked done |
| `created_at` | TIMESTAMP | NULL | Record creation time |
| `updated_at` | TIMESTAMP | NULL | Last update time |

---

### Table: `patient_feedback`

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Feedback record ID |
| `patient_id` | BIGINT UNSIGNED | FK → `patients.id`, CASCADE | Submitting patient |
| `plan_exercise_id` | BIGINT UNSIGNED | FK → `plan_exercises.id`, CASCADE | Exercise being rated |
| `rehab_plan_id` | BIGINT UNSIGNED | FK → `rehab_plans.id`, CASCADE, NULL | Plan this feedback belongs to |
| `pain_level` | INTEGER | NULL | Pain experienced (0–10) |
| `difficulty_rating` | INTEGER | NULL | Perceived difficulty (1–5) |
| `mood_rating` | INTEGER | NULL | Patient mood rating |
| `comments` | TEXT | NULL | Per-exercise free-text comments |
| `overall_comments` | VARCHAR(255) | NULL | Overall session free-text comments |
| `is_plan_feedback` | BOOLEAN | DEFAULT `false` | `true` = end-of-plan feedback session |
| `completed_exercise` | BOOLEAN | DEFAULT `false` | Whether exercise was completed |
| `feedback_date` | TIMESTAMP | NOT NULL | When feedback was submitted |
| `created_at` | TIMESTAMP | NULL | Record creation time |
| `updated_at` | TIMESTAMP | NULL | Last update time |

---

### Table: `clinician_messages`

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Message ID |
| `clinician_id` | BIGINT UNSIGNED | FK → `users.id`, CASCADE | Receiving clinician |
| `patient_id` | BIGINT UNSIGNED | FK → `patients.id`, SET NULL, NULL | Related patient (optional) |
| `message` | TEXT | NOT NULL | Message content |
| `type` | VARCHAR(255) | DEFAULT `info` | Message type: `info`, `success`, `warning` |
| `created_at` | TIMESTAMP | NULL | Record creation time |
| `updated_at` | TIMESTAMP | NULL | Last update time |
