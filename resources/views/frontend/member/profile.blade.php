@extends('frontend.layouts.app')

@section('title', 'My Profile - FitZone')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Profile Header -->
        <div class="bg-white shadow-xl rounded-lg overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center">
                            @if($member->profile_photo)
                                <img src="{{ asset('storage/' . $member->profile_photo) }}" alt="Profile" class="w-18 h-18 rounded-full object-cover">
                            @else
                                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white">{{ $member->full_name }}</h1>
                            <p class="text-blue-100">Member ID: {{ $member->member_id }}</p>
                            <div class="flex items-center mt-2">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    @if($member->status === 'active') bg-green-100 text-green-800
                                    @elseif($member->status === 'inactive') bg-gray-100 text-gray-800
                                    @elseif($member->status === 'suspended') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($member->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <a href="{{ route('member.profile.edit') }}" 
                           class="inline-flex items-center px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Personal Information -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Basic Info -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Personal Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Phone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->phone ?: 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Date of Birth</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->date_of_birth ? $member->date_of_birth->format('M d, Y') : 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Gender</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->gender ? ucfirst($member->gender) : 'Not provided' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500">Address</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->address ?: 'Not provided' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        Emergency Contact
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Contact Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->emergency_contact_name ?: 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Contact Phone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->emergency_contact_phone ?: 'Not provided' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Health & Fitness -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        Health & Fitness Information
                    </h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Medical Conditions</label>
                            @if($member->medical_conditions && count($member->medical_conditions) > 0)
                                <div class="mt-1 flex flex-wrap gap-2">
                                    @foreach($member->medical_conditions as $condition)
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">{{ $condition }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="mt-1 text-sm text-gray-900">None reported</p>
                            @endif
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Fitness Goals</label>
                            @if($member->fitness_goals && count($member->fitness_goals) > 0)
                                <div class="mt-1 flex flex-wrap gap-2">
                                    @foreach($member->fitness_goals as $goal)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">{{ $goal }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="mt-1 text-sm text-gray-900">Not specified</p>
                            @endif
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Preferred Workout Time</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $member->preferred_workout_time ?: 'Not specified' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">How You Found Us</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $member->referral_source ?: 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Membership Info Sidebar -->
            <div class="space-y-6">
                <!-- Membership Status -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Membership Status</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Plan</label>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $member->membershipType->type_name ?? 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Join Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->join_date ? $member->join_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Start Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->membership_start_date ? $member->membership_start_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Expiry Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $member->membership_end_date ? $member->membership_end_date->format('M d, Y') : 'N/A' }}</p>
                            @if($member->membership_end_date && $member->membership_end_date->isPast())
                                <span class="inline-block mt-1 px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Expired</span>
                            @elseif($member->membership_end_date && $member->membership_end_date->diffInDays(now()) <= 30)
                                <span class="inline-block mt-1 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Expires Soon</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('member.dashboard') }}" 
                           class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            </svg>
                            Dashboard
                        </a>
                        
                        <a href="{{ route('member.classes') }}" 
                           class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13M3 6.253A4.971 4.971 0 008.457 11H15.543A4.971 4.971 0 0020 6.253V4a2 2 0 00-2-2H6a2 2 0 00-2 2v2.253z"></path>
                            </svg>
                            Browse Classes
                        </a>
                        
                        <a href="{{ route('member.my-classes') }}" 
                           class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            My Classes
                        </a>
                        
                        <a href="{{ route('member.attendance') }}" 
                           class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Attendance History
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
