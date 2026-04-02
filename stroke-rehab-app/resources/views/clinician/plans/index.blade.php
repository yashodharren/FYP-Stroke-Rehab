@extends('layouts.clinician')

@section('page_title', 'Rehabilitation Plans')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Rehabilitation Plans</h1>
            <p class="text-gray-600 mt-2">Manage all rehabilitation plans for your patients</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Plan Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Difficulty</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Recovery Probability</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Start Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rehabPlans as $plan)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $plan->plan_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $plan->patient->user->name }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                    @if($plan->status === 'draft') bg-gray-100 text-gray-800
                                    @elseif($plan->status === 'active') bg-green-100 text-green-800
                                    @elseif($plan->status === 'completed') bg-blue-100 text-blue-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                {{ ucfirst($plan->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $plan->difficulty_level }}/5</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ ($plan->recovery_probability * 100) ?? 'N/A' }}%</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $plan->start_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="{{ route('clinician.plans.edit', $plan->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                            @if($plan->status === 'draft')
                            <form method="POST" action="{{ route('clinician.plans.publish', $plan->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-800 font-medium">Publish</button>
                            </form>
                            @endif
                            <button type="button" onclick="deletePlan({{ $plan->id }}, '{{ $plan->plan_name }}')" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-600">No rehabilitation plans created yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function deletePlan(planId, planName) {
        Swal.fire({
            title: 'Delete Rehabilitation Plan?',
            text: 'Are you sure you want to delete "' + planName + '"? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Delete Plan',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/clinician/plans/' + planId + '/delete';
                form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                    '<input type="hidden" name="_method" value="DELETE">';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection