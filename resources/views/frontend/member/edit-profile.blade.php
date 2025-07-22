@extends('frontend.layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-8">
                <h1 class="text-3xl font-bold text-white">Edit Your Profile</h1>
                <p class="text-blue-100 mt-2">Update your personal information and preferences</p>
            </div>

            <!-- Form -->
            <form action="{{ route('member.profile.update') }}" method="POST" class="p-6 space-y-8">
                @csrf
                @method('PUT')

                <!-- Personal Information -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Personal Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $member->first_name) }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('first_name') border-red-500 @enderror" 
                                   required>
                            @error('first_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $member->last_name) }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('last_name') border-red-500 @enderror" 
                                   required>
                            @error('last_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $member->phone) }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $member->date_of_birth?->format('Y-m-d')) }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('date_of_birth') border-red-500 @enderror">
                            @error('date_of_birth')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                            <select id="gender" name="gender" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('gender') border-red-500 @enderror">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $member->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $member->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $member->gender) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea id="address" name="address" rows="3" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror">{{ old('address', $member->address) }}</textarea>
                            @error('address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="bg-red-50 rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        Emergency Contact
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Name</label>
                            <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $member->emergency_contact_name) }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('emergency_contact_name') border-red-500 @enderror">
                            @error('emergency_contact_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Phone</label>
                            <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $member->emergency_contact_phone) }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('emergency_contact_phone') border-red-500 @enderror">
                            @error('emergency_contact_phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Health & Fitness Information -->
                <div class="bg-blue-50 rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        Health & Fitness Information
                    </h2>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Medical Conditions (if any)</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                @php
                                    $conditions = ['Diabetes', 'Heart Disease', 'High Blood Pressure', 'Asthma', 'Arthritis', 'Back Problems', 'Knee Problems', 'Other'];
                                    $memberConditions = old('medical_conditions', $member->medical_conditions ?? []);
                                @endphp
                                @foreach($conditions as $condition)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="medical_conditions[]" value="{{ $condition }}" 
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                               {{ in_array($condition, $memberConditions) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">{{ $condition }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fitness Goals</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                @php
                                    $goals = ['Weight Loss', 'Muscle Gain', 'Strength Training', 'Cardio Fitness', 'Flexibility', 'General Health', 'Sports Performance', 'Rehabilitation'];
                                    $memberGoals = old('fitness_goals', $member->fitness_goals ?? []);
                                @endphp
                                @foreach($goals as $goal)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="fitness_goals[]" value="{{ $goal }}" 
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                               {{ in_array($goal, $memberGoals) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">{{ $goal }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="preferred_workout_time" class="block text-sm font-medium text-gray-700 mb-2">Preferred Workout Time</label>
                                <select id="preferred_workout_time" name="preferred_workout_time" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select Time</option>
                                    <option value="Early Morning (5-8 AM)" {{ old('preferred_workout_time', $member->preferred_workout_time) == 'Early Morning (5-8 AM)' ? 'selected' : '' }}>Early Morning (5-8 AM)</option>
                                    <option value="Morning (8-11 AM)" {{ old('preferred_workout_time', $member->preferred_workout_time) == 'Morning (8-11 AM)' ? 'selected' : '' }}>Morning (8-11 AM)</option>
                                    <option value="Afternoon (11 AM-2 PM)" {{ old('preferred_workout_time', $member->preferred_workout_time) == 'Afternoon (11 AM-2 PM)' ? 'selected' : '' }}>Afternoon (11 AM-2 PM)</option>
                                    <option value="Evening (2-6 PM)" {{ old('preferred_workout_time', $member->preferred_workout_time) == 'Evening (2-6 PM)' ? 'selected' : '' }}>Evening (2-6 PM)</option>
                                    <option value="Night (6-10 PM)" {{ old('preferred_workout_time', $member->preferred_workout_time) == 'Night (6-10 PM)' ? 'selected' : '' }}>Night (6-10 PM)</option>
                                </select>
                            </div>

                            <div>
                                <label for="referral_source" class="block text-sm font-medium text-gray-700 mb-2">How did you hear about us?</label>
                                <select id="referral_source" name="referral_source" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select Source</option>
                                    <option value="Friend/Family" {{ old('referral_source', $member->referral_source) == 'Friend/Family' ? 'selected' : '' }}>Friend/Family</option>
                                    <option value="Social Media" {{ old('referral_source', $member->referral_source) == 'Social Media' ? 'selected' : '' }}>Social Media</option>
                                    <option value="Google Search" {{ old('referral_source', $member->referral_source) == 'Google Search' ? 'selected' : '' }}>Google Search</option>
                                    <option value="Advertisement" {{ old('referral_source', $member->referral_source) == 'Advertisement' ? 'selected' : '' }}>Advertisement</option>
                                    <option value="Walk-in" {{ old('referral_source', $member->referral_source) == 'Walk-in' ? 'selected' : '' }}>Walk-in</option>
                                    <option value="Other" {{ old('referral_source', $member->referral_source) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('member.profile') }}" 
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-105 font-semibold">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
