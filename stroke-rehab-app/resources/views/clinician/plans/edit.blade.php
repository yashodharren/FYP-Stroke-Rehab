@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('clinician.patients.show', $plan->patient_id) }}" class="text-blue-600 hover:text-blue-800 font-medium mb-4 inline-block">← Back</a>
            <h1 class="text-4xl font-bold text-gray-900">Edit Rehabilitation Plan</h1>
            <p class="text-gray-600 mt-2">{{ $plan->plan_name }}</p>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Exercises in Plan</h2>
                    <div class="divide-y divide-gray-200">
                        @forelse($groupedExercises as $grouped)
                        <div class="py-4 first:pt-0 last:pb-0">
                            <div class="flex justify-between items-start gap-4">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $grouped['exercise']->name }}</h3>
                                    <p class="text-gray-600 text-sm mt-1">{{ $grouped['exercise']->description }}</p>
                                    <div class="mt-2 flex gap-4 text-sm text-gray-600 flex-wrap">
                                        <span>📅 {{ implode(', ', $grouped['days']) }}</span>
                                        <span>🔄 {{ $grouped['frequency_per_week'] }}x/week</span>
                                        <span>⏱️ {{ $grouped['custom_duration_minutes'] ?? $grouped['exercise']->duration_minutes }} min</span>
                                        <span>💪 {{ $grouped['custom_repetitions'] ?? $grouped['exercise']->repetitions }} reps</span>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" class="edit-btn text-blue-600 hover:text-blue-800 font-medium text-sm"
                                        data-exercise-id="{{ $grouped['exercise_id'] }}"
                                        data-first-plan-exercise-id="{{ $grouped['plan_exercises'][0]['id'] }}"
                                        data-days="{{ json_encode($grouped['days']) }}"
                                        data-frequency="{{ $grouped['frequency_per_week'] }}"
                                        data-scheduled-time="{{ $grouped['scheduled_time'] }}"
                                        data-custom-reps="{{ $grouped['custom_repetitions'] }}"
                                        data-custom-duration="{{ $grouped['custom_duration_minutes'] }}">Edit</button>
                                    <div class="relative group">
                                        <button type="button" class="text-red-600 hover:text-red-800 font-medium text-sm">Remove</button>
                                        <div class="hidden group-hover:block absolute right-0 bg-white border border-gray-300 rounded shadow-lg p-2 z-10 whitespace-nowrap">
                                            <p class="text-xs text-gray-600 mb-2">Remove all instances?</p>
                                            @foreach($grouped['plan_exercises'] as $pe)
                                            <form method="POST" action="{{ route('clinician.plans.remove-exercise', $pe['id']) }}" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs block w-full text-left px-2 py-1">{{ $pe['day_of_week'] }}</button>
                                            </form>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-600 py-4">No exercises added yet. Add exercises using the form below.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4" id="formTitle">Add Exercises to Plan</h2>
                    <form id="exerciseForm" method="POST" action="{{ route('clinician.plans.add-exercise', $plan->id) }}" class="space-y-4" onsubmit="return setFormMethod()">
                        @csrf
                        <input type="hidden" id="form_method" name="_method" value="POST">

                        <div>
                            <label for="exercise_id" class="block text-sm font-medium text-gray-700 mb-2">Select Exercise *</label>
                            <select id="exercise_id" name="exercise_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Choose an exercise...</option>
                                @foreach($exercises as $exercise)
                                <option value="{{ $exercise->id }}">{{ $exercise->name }} (Level {{ $exercise->difficulty_level }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="frequency_per_week" class="block text-sm font-medium text-gray-700 mb-2">Frequency per Week *</label>
                            <input type="number" id="frequency_per_week" name="frequency_per_week" min="1" max="7" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="1" onchange="updateDaySelection()">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Days of Week *</label>
                            <p class="text-xs text-gray-600 mb-3">Select the days this exercise should be performed</p>
                            <div id="daysContainer" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="days_of_week[]" value="Monday" class="day-checkbox rounded">
                                    <span class="ml-2 text-sm text-gray-700">Monday</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="days_of_week[]" value="Tuesday" class="day-checkbox rounded">
                                    <span class="ml-2 text-sm text-gray-700">Tuesday</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="days_of_week[]" value="Wednesday" class="day-checkbox rounded">
                                    <span class="ml-2 text-sm text-gray-700">Wednesday</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="days_of_week[]" value="Thursday" class="day-checkbox rounded">
                                    <span class="ml-2 text-sm text-gray-700">Thursday</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="days_of_week[]" value="Friday" class="day-checkbox rounded">
                                    <span class="ml-2 text-sm text-gray-700">Friday</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="days_of_week[]" value="Saturday" class="day-checkbox rounded">
                                    <span class="ml-2 text-sm text-gray-700">Saturday</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="days_of_week[]" value="Sunday" class="day-checkbox rounded">
                                    <span class="ml-2 text-sm text-gray-700">Sunday</span>
                                </label>
                            </div>
                            <div id="daysError" class="text-red-600 text-sm mt-2" style="display:none;"></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Scheduled Times (per day)</label>
                            <p class="text-xs text-gray-600 mb-3">Set specific times for each selected day. Leave blank for auto-assignment.</p>
                            <div id="timesContainer" class="space-y-2 mb-4">
                                <!-- Times will be dynamically added here based on selected days -->
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="custom_repetitions" class="block text-sm font-medium text-gray-700 mb-2">Custom Repetitions</label>
                                <input type="number" id="custom_repetitions" name="custom_repetitions" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Leave blank to use default">
                            </div>

                            <div>
                                <label for="custom_duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">Custom Duration (minutes)</label>
                                <input type="number" id="custom_duration_minutes" name="custom_duration_minutes" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Leave blank to use default">
                            </div>
                        </div>

                        <button type="submit" id="submitBtn" class="w-full bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium">
                            Add Exercise to Plan
                        </button>
                    </form>
                </div>
            </div>

            <div>
                <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Plan Summary</h2>
                    <div class="space-y-3 mb-6">
                        <div>
                            <p class="text-gray-600 text-sm">Status</p>
                            <p class="font-semibold text-gray-900">{{ ucfirst($plan->status) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Difficulty Level</p>
                            <p class="font-semibold text-gray-900">{{ $plan->difficulty_level }}/5</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Total Exercises</p>
                            <p class="font-semibold text-gray-900">{{ $planExercises->count() }}</p>
                        </div>
                    </div>

                    @if($plan->status === 'draft' && $planExercises->count() > 0)
                    <form method="POST" action="{{ route('clinician.plans.publish', $plan->id) }}">
                        @csrf
                        <button type="submit" class="w-full bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 font-medium">
                            Publish Plan
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let isEditMode = false;

    // Initialize edit button event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const exerciseId = this.dataset.exerciseId;
                const daysJson = this.dataset.days;
                const frequency = this.dataset.frequency;
                const scheduledTime = this.dataset.scheduledTime;
                const customReps = this.dataset.customReps;
                const customDuration = this.dataset.customDuration;

                editExercise(exerciseId, daysJson, frequency, scheduledTime, customReps, customDuration);
            });
        });
    });

    function updateDaySelection() {
        const frequency = parseInt(document.getElementById('frequency_per_week').value) || 1;
        const checkboxes = document.querySelectorAll('.day-checkbox');

        // Uncheck all first
        checkboxes.forEach(cb => cb.checked = false);

        // Auto-select suggested days based on frequency
        const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        const suggestedDays = [];

        if (frequency === 1) {
            suggestedDays.push('Monday');
        } else if (frequency === 2) {
            suggestedDays.push('Monday', 'Thursday');
        } else if (frequency === 3) {
            suggestedDays.push('Monday', 'Wednesday', 'Friday');
        } else if (frequency === 4) {
            suggestedDays.push('Monday', 'Tuesday', 'Thursday', 'Friday');
        } else if (frequency === 5) {
            suggestedDays.push('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday');
        } else if (frequency === 6) {
            suggestedDays.push('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        } else if (frequency === 7) {
            suggestedDays = days;
        }

        // Check the suggested days
        checkboxes.forEach(cb => {
            if (suggestedDays.includes(cb.value)) {
                cb.checked = true;
            }
        });

        // Update per-day time inputs
        updateTimeInputs();
    }

    function updateTimeInputs() {
        const selectedDays = Array.from(document.querySelectorAll('.day-checkbox:checked')).map(cb => cb.value);
        const timesContainer = document.getElementById('timesContainer');
        timesContainer.innerHTML = '';

        selectedDays.forEach(day => {
            const timeInput = document.createElement('div');
            timeInput.className = 'flex items-center gap-2';
            timeInput.innerHTML = `
                <label class="w-24 text-sm font-medium text-gray-700">${day}:</label>
                <input type="time" name="scheduled_times[${day}]" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            `;
            timesContainer.appendChild(timeInput);
        });
    }

    // Add event listeners to day checkboxes
    document.addEventListener('DOMContentLoaded', function() {
        const dayCheckboxes = document.querySelectorAll('.day-checkbox');
        dayCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateTimeInputs);
        });
    });

    function setFormMethod() {
        // Validate that at least one day is selected
        const selectedDays = document.querySelectorAll('.day-checkbox:checked');
        const frequency = parseInt(document.getElementById('frequency_per_week').value) || 1;
        const daysError = document.getElementById('daysError');

        if (selectedDays.length === 0) {
            daysError.textContent = 'Please select at least one day of the week';
            daysError.style.display = 'block';
            return false;
        }

        if (selectedDays.length !== frequency) {
            daysError.textContent = `Please select exactly ${frequency} day(s) matching the frequency`;
            daysError.style.display = 'block';
            return false;
        }

        daysError.style.display = 'none';

        // This function is called on form submission to ensure _method is set correctly
        const methodInput = document.getElementById('form_method');
        if (isEditMode) {
            methodInput.value = 'PUT';
        } else {
            methodInput.value = 'POST';
        }
        console.log('Form submission - isEditMode:', isEditMode, '_method value:', methodInput.value);
        console.log('Form action:', document.getElementById('exerciseForm').action);
        return true;
    }

    function editExercise(exerciseId, daysJson, frequency, scheduledTime, customReps, customDuration) {
        // Parse the days array from JSON
        let days = [];
        try {
            days = JSON.parse(daysJson);
        } catch (e) {
            console.error('Error parsing days:', e);
            days = [];
        }

        // Populate the form with existing values
        document.getElementById('exercise_id').value = exerciseId;
        document.getElementById('frequency_per_week').value = frequency;
        document.getElementById('scheduled_time').value = scheduledTime || '';
        document.getElementById('custom_repetitions').value = customReps || '';
        document.getElementById('custom_duration_minutes').value = customDuration || '';

        // Set the day checkboxes for all selected days
        const checkboxes = document.querySelectorAll('.day-checkbox');
        checkboxes.forEach(cb => cb.checked = false);
        days.forEach(day => {
            const dayCheckbox = document.querySelector(`.day-checkbox[value="${day}"]`);
            if (dayCheckbox) {
                dayCheckbox.checked = true;
            }
        });

        // Set edit mode flag
        isEditMode = true;

        // Get the plan ID from the current URL more reliably
        const pathParts = window.location.pathname.split('/').filter(part => part !== '');
        // pathParts will be: ['clinician', 'plans', '9', 'edit']
        const planId = pathParts[2];

        console.log('Edit Exercise Debug Info:');
        console.log('  exerciseId:', exerciseId);
        console.log('  days:', days);
        console.log('  frequency:', frequency);
        console.log('  pathParts:', pathParts);
        console.log('  planId:', planId);

        // For edit mode, we need to use the first exercise ID to update
        // Store the exercise IDs for later use
        window.editingExerciseIds = days.map((day, index) => {
            // This will be populated by data attributes
            return null;
        });

        // Change form action to update endpoint - use first day's exercise ID
        const form = document.getElementById('exerciseForm');
        // We'll need to get the first plan exercise ID from the button
        const firstExerciseId = document.querySelector(`.edit-btn[data-exercise-id="${exerciseId}"]`)?.dataset.firstPlanExerciseId;
        const newAction = `/clinician/plans/${planId}/exercises/${firstExerciseId}/update`;
        console.log('  newAction:', newAction);
        form.action = newAction;

        // Change button text and styling
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.textContent = 'Update Exercise';
        submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        submitBtn.classList.add('bg-green-600', 'hover:bg-green-700');

        // Change form title
        document.getElementById('formTitle').textContent = 'Edit Exercise';

        // Scroll to form
        form.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    function resetForm() {
        const form = document.getElementById('exerciseForm');
        form.reset();
        document.getElementById('form_method').value = 'POST';
        form.action = '{{ route("clinician.plans.add-exercise", $plan->id) }}';
        isEditMode = false;

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.textContent = 'Add Exercise to Plan';
        submitBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
        submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');

        document.getElementById('formTitle').textContent = 'Add Exercises to Plan';
    }
</script>
</div>
</div>
@endsection