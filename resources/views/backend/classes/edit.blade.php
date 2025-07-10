@extends('layouts.master', ['activePage' => 'classes', 'titlePage' => 'Edit Class'])

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Gym Management /</span> Edit Class
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Edit Class Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('classes.update', $class->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="class_name" class="form-label">Class Name *</label>
                                <input type="text" class="form-control @error('class_name') is-invalid @enderror" 
                                       id="class_name" name="class_name" value="{{ old('class_name', $class->class_name) }}" required>
                                @error('class_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3 col-md-6">
                                <label for="trainer_id" class="form-label">Trainer *</label>
                                <select class="form-select @error('trainer_id') is-invalid @enderror" 
                                        id="trainer_id" name="trainer_id" required>
                                    <option value="">Select Trainer</option>
                                    @foreach($trainers as $trainer)
                                        <option value="{{ $trainer->id }}" {{ (old('trainer_id', $class->trainer_id) == $trainer->id) ? 'selected' : '' }}>
                                            {{ $trainer->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('trainer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3 col-md-6">
                                <label for="schedule_day" class="form-label">Schedule Date *</label>
                                <input type="datetime-local" class="form-control @error('schedule_day') is-invalid @enderror" 
                                       id="schedule_day" name="schedule_day" 
                                       value="{{ old('schedule_day', $class->schedule_day->format('Y-m-d\TH:i')) }}" required>
                                @error('schedule_day')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3 col-md-6">
                                <label for="duration_minutes" class="form-label">Duration (minutes) *</label>
                                <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" 
                                       id="duration_minutes" name="duration_minutes" 
                                       value="{{ old('duration_minutes', $class->duration_minutes) }}" 
                                       min="15" max="180" required>
                                @error('duration_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3 col-md-6">
                                <label for="max_capacity" class="form-label">Max Capacity *</label>
                                <input type="number" class="form-control @error('max_capacity') is-invalid @enderror" 
                                       id="max_capacity" name="max_capacity" 
                                       value="{{ old('max_capacity', $class->max_capacity) }}" 
                                       min="1" max="100" required>
                                @error('max_capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3 col-md-6">
                                <label for="class_type" class="form-label">Class Type</label>
                                <input type="text" class="form-control @error('class_type') is-invalid @enderror" 
                                       id="class_type" name="class_type" 
                                       value="{{ old('class_type', $class->class_type) }}"
                                       placeholder="e.g., Yoga, CrossFit, HIIT">
                                @error('class_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3 col-md-6">
                                <label for="price" class="form-label">Price ($)</label>
                                <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" value="{{ old('price', $class->price) }}">
                                @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3 col-md-6">
                                <label for="difficulty_level" class="form-label">Difficulty Level</label>
                                <select class="form-select @error('difficulty_level') is-invalid @enderror" 
                                        id="difficulty_level" name="difficulty_level">
                                    <option value="">Select Level</option>
                                    <option value="beginner" {{ old('difficulty_level', $class->difficulty_level) == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                    <option value="intermediate" {{ old('difficulty_level', $class->difficulty_level) == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                    <option value="advanced" {{ old('difficulty_level', $class->difficulty_level) == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                </select>
                                @error('difficulty_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3 col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3">{{ old('description', $class->description) }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3 col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', $class->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Status
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Update Class</button>
                            <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection