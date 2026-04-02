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

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="day_of_week" class="block text-sm font-medium text-gray-700 mb-2">Day of Week *</label>
                                <select id="day_of_week" name="day_of_week" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select day</option>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday">Saturday</option>
                                    <option value="Sunday">Sunday</option>
                                </select>
                            </div>

                            <div>
                                <label for="frequency_per_week" class="block text-sm font-medium text-gray-700 mb-2">Frequency per Week *</label>
                                <input type="number" id="frequency_per_week" name="frequency_per_week" min="1" max="7" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="1">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="scheduled_time" class="block text-sm font-medium text-gray-700 mb-2">Scheduled Time</label>
                                <input type="time" id="scheduled_time" name="scheduled_time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label for="custom_repetitions" class="block text-sm font-medium text-gray-700 mb-2">Custom Repetitions</label>
                                <input type="number" id="custom_repetitions" name="custom_repetitions" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Leave blank to use default">
                            </div>
                        </div>

                        <div>
                            <label for="custom_duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">Custom Duration (minutes)</label>
                            <input type="number" id="custom_duration_minutes" name="custom_duration_minutes" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Leave blank to use default">
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

        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Exercises in Plan</h2>
            <div class="divide-y divide-gray-200">
                @forelse($planExercises as $planExercise)
                <div class="py-4 first:pt-0 last:pb-0">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ $planExercise->exercise->name }}</h3>
                            <p class="text-gray-600 text-sm mt-1">{{ $planExercise->exercise->description }}</p>
                            <div class="mt-2 flex gap-4 text-sm text-gray-600">
                                <span>📅 {{ $planExercise->day_of_week }}</span>
                                <span>🔄 {{ $planExercise->frequency_per_week }}x/week</span>
                                <span>⏱️ {{ $planExercise->custom_duration_minutes ?? $planExercise->exercise->duration_minutes }} min</span>
                                <span>💪 {{ $planExercise->custom_repetitions ?? $planExercise->exercise->repetitions }} reps</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" class="edit-btn text-blue-600 hover:text-blue-800 font-medium text-sm"
                                data-plan-exercise-id="{{ $planExercise->id }}"
                                data-exercise-id="{{ $planExercise->exercise->id }}"
                                data-day-of-week="{{ $planExercise->day_of_week }}"
                                data-frequency="{{ $planExercise->frequency_per_week }}"
                                data-scheduled-time="{{ $planExercise->scheduled_time }}"
                                data-custom-reps="{{ $planExercise->custom_repetitions }}"
                                data-custom-duration="{{ $planExercise->custom_duration_minutes }}">Edit</button>
                            <form method="POST" action="{{ route('clinician.plans.remove-exercise', $planExercise->id) }}" style="display:inline;" onsubmit="return confirm('Remove this exercise from the plan?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">Remove</button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-gray-600 py-4">No exercises added yet. Add exercises using the form above.</p>
                @endforelse
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
                const planExerciseId = this.dataset.planExerciseId;
                const exerciseId = this.dataset.exerciseId;
                const dayOfWeek = this.dataset.dayOfWeek;
                const frequency = this.dataset.frequency;
                const scheduledTime = this.dataset.scheduledTime;
                const customReps = this.dataset.customReps;
                const customDuration = this.dataset.customDuration;

                editExercise(planExerciseId, exerciseId, dayOfWeek, frequency, scheduledTime, customReps, customDuration);
            });
        });
    });

    function setFormMethod() {
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

    function editExercise(planExerciseId, exerciseId, dayOfWeek, frequency, scheduledTime, customReps, customDuration) {
        // Populate the form with existing values
        document.getElementById('exercise_id').value = exerciseId;
        document.getElementById('day_of_week').value = dayOfWeek;
        document.getElementById('frequency_per_week').value = frequency;
        document.getElementById('scheduled_time').value = scheduledTime || '';
        document.getElementById('custom_repetitions').value = customReps || '';
        document.getElementById('custom_duration_minutes').value = customDuration || '';

        // Set edit mode flag
        isEditMode = true;

        // Get the plan ID from the current URL more reliably
        const pathParts = window.location.pathname.split('/').filter(part => part !== '');
        // pathParts will be: ['clinician', 'plans', '9', 'edit']
        const planId = pathParts[2];

        console.log('Edit Exercise Debug Info:');
        console.log('  planExerciseId:', planExerciseId);
        console.log('  exerciseId:', exerciseId);
        console.log('  pathParts:', pathParts);
        console.log('  planId:', planId);

        // Change form action to update endpoint
        const form = document.getElementById('exerciseForm');
        const newAction = `/clinician/plans/${planId}/exercises/${planExerciseId}/update`;
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