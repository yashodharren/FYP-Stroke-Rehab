@extends('layouts.clinician')

@section('page_title', 'Create New Patient')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('clinician.patients.index') }}" class="text-blue-600 hover:text-blue-800 font-medium mb-4 inline-block">← Back to Patients</a>
        <h1 class="text-3xl font-bold text-gray-900">Create New Patient Account</h1>
        <p class="text-gray-600 mt-2">Fill in the patient details to create a new account. A temporary password will be generated.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            <p class="font-medium">Please fix the following errors:</p>
            <ul class="list-disc list-inside mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('clinician.patients.store') }}" class="bg-white rounded-lg shadow p-8">
        @csrf

        <!-- User Information Section -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6 pb-4 border-b border-gray-200">User Account Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="Enter patient's full name">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                           placeholder="Enter patient's email">
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded">
                <p class="text-sm"><strong>Note:</strong> A temporary password will be generated and displayed after creation. Share this with the patient during your face-to-face appointment.</p>
            </div>
        </div>

        <!-- Demographics & Vitals Section -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6 pb-4 border-b border-gray-200">Demographics & Vitals</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="age" class="block text-sm font-medium text-gray-700 mb-2">Age (years) *</label>
                    <input type="number" id="age" name="age" value="{{ old('age') }}" required min="0" max="150"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('age') border-red-500 @enderror"
                           placeholder="Enter patient's age">
                    @error('age')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                    <select id="gender" name="gender" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('gender') border-red-500 @enderror">
                        <option value="">Select gender</option>
                        <option value="0" {{ old('gender') === '0' ? 'selected' : '' }}>Female</option>
                        <option value="1" {{ old('gender') === '1' ? 'selected' : '' }}>Male</option>
                    </select>
                    @error('gender')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="rsbp" class="block text-sm font-medium text-gray-700 mb-2">Systolic Blood Pressure (mmHg)</label>
                    <input type="number" id="rsbp" name="rsbp" value="{{ old('rsbp') }}" min="0" max="300"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('rsbp') border-red-500 @enderror"
                           placeholder="e.g., 140">
                    <p class="text-xs text-gray-500 mt-1">If > 160 mmHg, system will suggest conservative rehabilitation plan</p>
                    @error('rsbp')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Stroke Characterization Section -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6 pb-4 border-b border-gray-200">Stroke Characterization</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="stroke_subtype" class="block text-sm font-medium text-gray-700 mb-2">Stroke Subtype *</label>
                    <select id="stroke_subtype" name="stroke_subtype" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('stroke_subtype') border-red-500 @enderror">
                        <option value="">Select stroke subtype</option>
                        <option value="TACS" {{ old('stroke_subtype') === 'TACS' ? 'selected' : '' }}>TACS - Total Anterior Circulation Stroke (High Severity)</option>
                        <option value="PACS" {{ old('stroke_subtype') === 'PACS' ? 'selected' : '' }}>PACS - Partial Anterior Circulation Stroke</option>
                        <option value="LACS" {{ old('stroke_subtype') === 'LACS' ? 'selected' : '' }}>LACS - Lacunar Stroke (Small vessel, better recovery)</option>
                        <option value="POCS" {{ old('stroke_subtype') === 'POCS' ? 'selected' : '' }}>POCS - Posterior Circulation Stroke</option>
                        <option value="OTH" {{ old('stroke_subtype') === 'OTH' ? 'selected' : '' }}>OTH - Other/Unclassified</option>
                    </select>
                    @error('stroke_subtype')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="conscious_state" class="block text-sm font-medium text-gray-700 mb-2">Conscious State *</label>
                    <select id="conscious_state" name="conscious_state" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('conscious_state') border-red-500 @enderror">
                        <option value="">Select conscious state</option>
                        <option value="Alert" {{ old('conscious_state') === 'Alert' ? 'selected' : '' }}>Fully Alert</option>
                        <option value="Drowsy" {{ old('conscious_state') === 'Drowsy' ? 'selected' : '' }}>Drowsy</option>
                        <option value="Unconscious" {{ old('conscious_state') === 'Unconscious' ? 'selected' : '' }}>Unconscious</option>
                    </select>
                    @error('conscious_state')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex gap-4 pt-6 border-t border-gray-200">
            <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-lg hover:bg-blue-700 font-medium">
                Create Patient Account
            </button>
            <a href="{{ route('clinician.patients.index') }}" class="bg-gray-300 text-gray-800 px-8 py-2 rounded-lg hover:bg-gray-400 font-medium">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
