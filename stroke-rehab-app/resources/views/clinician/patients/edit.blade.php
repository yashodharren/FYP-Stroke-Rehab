@extends('layouts.clinician')

@section('page_title', 'Edit Patient Information')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('clinician.patients.index') }}" class="text-blue-600 hover:text-blue-800 font-medium mb-4 inline-block">← Back to Patients</a>
        <h1 class="text-3xl font-bold text-gray-900">Edit Patient Information</h1>
        <p class="text-gray-600 mt-2">Update clinical information for {{ $patient->user->name }}</p>
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

    <form method="POST" action="{{ route('clinician.patients.update', $patient->id) }}" class="bg-white rounded-lg shadow p-8">
        @csrf
        @method('PUT')

        <!-- Demographics & Vitals Section -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6 pb-4 border-b border-gray-200">Demographics & Vitals</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="age" class="block text-sm font-medium text-gray-700 mb-2">Age (years) *</label>
                    <input type="number" id="age" name="age" value="{{ old('age', $patient->age) }}" required min="0" max="150"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('age') border-red-500 @enderror">
                    @error('age')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                    <select id="gender" name="gender" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('gender') border-red-500 @enderror">
                        <option value="">Select gender</option>
                        <option value="0" {{ old('gender', $patient->gender) === 0 || old('gender', $patient->gender) === '0' ? 'selected' : '' }}>Female</option>
                        <option value="1" {{ old('gender', $patient->gender) === 1 || old('gender', $patient->gender) === '1' ? 'selected' : '' }}>Male</option>
                    </select>
                    @error('gender')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="rsbp" class="block text-sm font-medium text-gray-700 mb-2">Systolic Blood Pressure (mmHg)</label>
                    <input type="number" id="rsbp" name="rsbp" value="{{ old('rsbp', $patient->rsbp) }}" min="0" max="300"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('rsbp') border-red-500 @enderror">
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
                        <option value="TACS" {{ old('stroke_subtype', $patient->stroke_subtype) === 'TACS' ? 'selected' : '' }}>TACS - Total Anterior Circulation Stroke (High Severity)</option>
                        <option value="PACS" {{ old('stroke_subtype', $patient->stroke_subtype) === 'PACS' ? 'selected' : '' }}>PACS - Partial Anterior Circulation Stroke</option>
                        <option value="LACS" {{ old('stroke_subtype', $patient->stroke_subtype) === 'LACS' ? 'selected' : '' }}>LACS - Lacunar Stroke (Small vessel, better recovery)</option>
                        <option value="POCS" {{ old('stroke_subtype', $patient->stroke_subtype) === 'POCS' ? 'selected' : '' }}>POCS - Posterior Circulation Stroke</option>
                        <option value="OTH" {{ old('stroke_subtype', $patient->stroke_subtype) === 'OTH' ? 'selected' : '' }}>OTH - Other/Unclassified</option>
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
                        <option value="Alert" {{ old('conscious_state', $patient->conscious_state) === 'Alert' ? 'selected' : '' }}>Fully Alert</option>
                        <option value="Drowsy" {{ old('conscious_state', $patient->conscious_state) === 'Drowsy' ? 'selected' : '' }}>Drowsy</option>
                        <option value="Unconscious" {{ old('conscious_state', $patient->conscious_state) === 'Unconscious' ? 'selected' : '' }}>Unconscious</option>
                    </select>
                    @error('conscious_state')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Functional Deficits Section -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6 pb-4 border-b border-gray-200">Functional Deficits</h2>
            <p class="text-gray-600 text-sm mb-6">Select all deficits present. These map to exercise recommendations.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-center">
                    <input type="checkbox" id="rdef1" name="rdef1" value="1" {{ old('rdef1', $patient->rdef1) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                    <label for="rdef1" class="ml-3 text-sm font-medium text-gray-700">
                        Face Deficit
                        <span class="text-xs text-gray-500 block">Facial weakness or asymmetry</span>
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="rdef2" name="rdef2" value="1" {{ old('rdef2', $patient->rdef2) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                    <label for="rdef2" class="ml-3 text-sm font-medium text-gray-700">
                        Arm/Hand Deficit
                        <span class="text-xs text-gray-500 block">Upper limb weakness or loss of function</span>
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="rdef3" name="rdef3" value="1" {{ old('rdef3', $patient->rdef3) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                    <label for="rdef3" class="ml-3 text-sm font-medium text-gray-700">
                        Leg/Foot Deficit
                        <span class="text-xs text-gray-500 block">Lower limb weakness or loss of function</span>
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="rdef4" name="rdef4" value="1" {{ old('rdef4', $patient->rdef4) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                    <label for="rdef4" class="ml-3 text-sm font-medium text-gray-700">
                        Dysphasia (Speech)
                        <span class="text-xs text-gray-500 block">Speech or language impairment</span>
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="rdef5" name="rdef5" value="1" {{ old('rdef5', $patient->rdef5) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                    <label for="rdef5" class="ml-3 text-sm font-medium text-gray-700">
                        Hemianopia (Vision)
                        <span class="text-xs text-gray-500 block">Loss of visual field</span>
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="rdef6" name="rdef6" value="1" {{ old('rdef6', $patient->rdef6) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                    <label for="rdef6" class="ml-3 text-sm font-medium text-gray-700">
                        Visuospatial Disorder
                        <span class="text-xs text-gray-500 block">Spatial awareness or coordination issues</span>
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="rdef7" name="rdef7" value="1" {{ old('rdef7', $patient->rdef7) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                    <label for="rdef7" class="ml-3 text-sm font-medium text-gray-700">
                        Brainstem/Cerebellar Signs
                        <span class="text-xs text-gray-500 block">Balance, coordination, or brainstem symptoms</span>
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="rdef8" name="rdef8" value="1" {{ old('rdef8', $patient->rdef8) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                    <label for="rdef8" class="ml-3 text-sm font-medium text-gray-700">
                        Other Deficits
                        <span class="text-xs text-gray-500 block">Any other neurological deficits</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Recovery Status Section -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6 pb-4 border-b border-gray-200">Recovery Status</h2>
            
            <div>
                <label for="recovery_status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                <select id="recovery_status" name="recovery_status" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('recovery_status') border-red-500 @enderror">
                    <option value="">Select status</option>
                    <option value="new" {{ old('recovery_status', $patient->recovery_status) === 'new' ? 'selected' : '' }}>New</option>
                    <option value="in_progress" {{ old('recovery_status', $patient->recovery_status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ old('recovery_status', $patient->recovery_status) === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="paused" {{ old('recovery_status', $patient->recovery_status) === 'paused' ? 'selected' : '' }}>Paused</option>
                </select>
                @error('recovery_status')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex gap-4 pt-6 border-t border-gray-200">
            <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-lg hover:bg-blue-700 font-medium">
                Save Changes
            </button>
            <a href="{{ route('clinician.patients.index') }}" class="bg-gray-300 text-gray-800 px-8 py-2 rounded-lg hover:bg-gray-400 font-medium">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
