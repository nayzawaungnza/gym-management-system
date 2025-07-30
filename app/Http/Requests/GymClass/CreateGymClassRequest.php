<?php

namespace App\Http\Requests\GymClass;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateGymClassRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'trainer_id' => 'nullable|uuid|exists:trainers,id',
            'class_name' => 'required|string|max:50',
            'description' => 'nullable|string',
            'class_type' => 'nullable|string|max:50',
            'schedule_day' => ['required', 'string', Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])],
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration_minutes' => 'required|integer|min:1',
            'max_capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'room' => 'nullable|string|max:50',
            'equipment_needed' => 'nullable|string',
            'difficulty_level' => ['nullable', 'string', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'is_active' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'trainer_id.exists' => 'The selected trainer does not exist.',
            'class_name.required' => 'Class Name is required.',
            'class_name.max' => 'Class Name cannot exceed 50 characters.',
            'schedule_day.required' => 'Schedule Day is required.',
            'schedule_day.in' => 'Invalid Schedule Day selected.',
            'start_time.required' => 'Start Time is required.',
            'start_time.date_format' => 'Start Time must be in HH:MM format.',
            'end_time.required' => 'End Time is required.',
            'end_time.date_format' => 'End Time must be in HH:MM format.',
            'end_time.after' => 'End Time must be after Start Time.',
            'duration_minutes.required' => 'Duration is required.',
            'duration_minutes.integer' => 'Duration must be an integer.',
            'duration_minutes.min' => 'Duration must be at least 1 minute.',
            'max_capacity.required' => 'Max Capacity is required.',
            'max_capacity.integer' => 'Max Capacity must be an integer.',
            'max_capacity.min' => 'Max Capacity must be at least 1.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price cannot be negative.',
            'room.max' => 'Room cannot exceed 50 characters.',
            'difficulty_level.in' => 'Invalid Difficulty Level selected.',
        ];
    }
}
