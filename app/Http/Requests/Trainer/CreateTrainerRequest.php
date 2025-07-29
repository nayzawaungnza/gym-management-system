<?php

namespace App\Http\Requests\Trainer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTrainerRequest extends FormRequest
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
            // User fields
            //'name' => 'required|string|max:255', // This will be combined from first_name/last_name in service
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:15|unique:users,phone', // User's phone
            'is_active' => 'boolean', // For User and Trainer

            // Trainer specific fields
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'specialization' => 'nullable|string|max:100',
            'certifications' => 'nullable|array',
            'certifications.*.name' => 'required_with:certifications|string|max:255',
            'certifications.*.year' => 'required_with:certifications|integer|min:1900|max:' . date('Y'),
            'hourly_rate' => 'nullable|numeric|min:0',
            'bio' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'hire_date' => 'required|date',
            'role' => ['required', 'string', Rule::in(['Trainer'])], // Hidden field, must be 'Trainer'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Full Name is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'Email already exists.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'phone.unique' => 'Phone number already exists.',
            'phone.phone' => 'Phone number is not in a valid format (e.g., 09xxxxxxxx).',
            'first_name.required' => 'First Name is required.',
            'last_name.required' => 'Last Name is required.',
            'certifications.*.name.required_with' => 'Certification name is required.',
            'certifications.*.year.required_with' => 'Certification year is required.',
            'certifications.*.year.integer' => 'Certification year must be a number.',
            'certifications.*.year.min' => 'Certification year must be at least 1900.',
            'certifications.*.year.max' => 'Certification year cannot be in the future.',
            'hourly_rate.numeric' => 'Hourly rate must be a number.',
            'hourly_rate.min' => 'Hourly rate cannot be negative.',
            'profile_photo.image' => 'Profile photo must be an image.',
            'profile_photo.mimes' => 'Profile photo must be a JPEG, PNG, JPG, GIF, or SVG.',
            'profile_photo.max' => 'Profile photo may not be greater than 2MB.',
            'hire_date.required' => 'Hire Date is required.',
            'hire_date.date' => 'Hire Date must be a valid date.',
            'role.required' => 'Role is required.',
            'role.in' => 'Invalid role selected.',
        ];
    }
}