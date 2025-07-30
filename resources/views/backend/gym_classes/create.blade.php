@extends('layouts.master', ['activePage' => 'gymclasses', 'titlePage' => 'Add Gym Class'])

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Gym Classes /</span> Add New Gym Class
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gym Class Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('gymclasses.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="class_name" class="form-label">Class Name *</label>
                                <input type="text" class="form-control @error('class_name') is-invalid @enderror" 
                                       id="class_name" name="class_name" value="{{ old('class_name') }}" required>
                                @error('class_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="trainer_id" class="form-label">Trainer</label>
                                <select class="form-select @error('trainer_id') is-invalid @enderror" 
                                        id="trainer_id" name="trainer_id">
                                    <option value="">Select Trainer</option>
                                    @foreach($trainers as $trainer)
                                        <option value="{{ $trainer->id }}" {{ old('trainer_id') == $trainer->id ? 'selected' : '' }}>
                                            {{ $trainer->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('trainer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="class_type" class="form-label">Class Type</label>
                                <input type="text" class="form-control @error('class_type') is-invalid @enderror" 
                                       id="class_type" name="class_type" value="{{ old('class_type') }}"
                                       placeholder="e.g., Yoga, HIIT, Spinning">
                                @error('class_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="schedule_day" class="form-label">Schedule Day *</label>
                                <select class="form-select @error('schedule_day') is-invalid @enderror" 
                                        id="schedule_day" name="schedule_day" required>
                                    <option value="">Select Day</option>
                                    @foreach($scheduleDays as $day)
                                        <option value="{{ $day }}" {{ old('schedule_day') == $day ? 'selected' : '' }}>
                                            {{ ucfirst($day) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('schedule_day')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="start_time" class="form-label">Start Time *</label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                       id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="end_time" class="form-label">End Time *</label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                       id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                                @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="duration_minutes" class="form-label">Duration (Minutes) *</label>
                                <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" 
                                       id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes') }}" min="1" required>
                                @error('duration_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="max_capacity" class="form-label">Max Capacity *</label>
                                <input type="number" class="form-control @error('max_capacity') is-invalid @enderror" 
                                       id="max_capacity" name="max_capacity" value="{{ old('max_capacity') }}" min="1" required>
                                @error('max_capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="price" class="form-label">Price ($) *</label>
                                <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" value="{{ old('price') }}" min="0" required>
                                @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="room" class="form-label">Room</label>
                                <input type="text" class="form-control @error('room') is-invalid @enderror" 
                                       id="room" name="room" value="{{ old('room') }}">
                                @error('room')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="difficulty_level" class="form-label">Difficulty Level</label>
                                <select class="form-select @error('difficulty_level') is-invalid @enderror" 
                                        id="difficulty_level" name="difficulty_level">
                                    <option value="">Select Difficulty</option>
                                    @foreach($difficultyLevels as $level)
                                        <option value="{{ $level }}" {{ old('difficulty_level') == $level ? 'selected' : '' }}>
                                            {{ ucfirst($level) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('difficulty_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-12">
                                <label for="equipment_needed" class="form-label">Equipment Needed</label>
                                <textarea class="form-control @error('equipment_needed') is-invalid @enderror" 
                                          id="equipment_needed" name="equipment_needed" rows="3">{{ old('equipment_needed') }}</textarea>
                                @error('equipment_needed')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="is_active" class="form-label">Status</label>
                                <select class="form-select @error('is_active') is-invalid @enderror" 
                                        id="is_active" name="is_active">
                                    <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Create Gym Class</button>
                            <a href="{{ route('gymclasses.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
