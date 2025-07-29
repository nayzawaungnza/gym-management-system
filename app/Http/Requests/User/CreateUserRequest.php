<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:15|unique:users', // Changed from 'mobile'
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'boolean', // 'in:0,1|default:1' is not needed here, boolean is sufficient for checkbox
            'profile_photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|nullable', // Changed from 'avatar'
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:15|regex:/^([0-9\s\-\+$$$$]*)$/|phone:MM',
            'role' => 'required|string|exists:roles,name', // Added for single role selection
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Full Name is required.',
            'name.max' => 'Full Name is too long.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email is too long.',
            'email.unique' => 'Email already exists.',
            'phone.unique' => 'Phone number already exists.',
            'phone.regex' => 'Phone number is not valid.',
            'phone.phone' => 'Phone number is not in a valid format (e.g., 09xxxxxxxx).',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'profile_photo.image' => 'Profile photo must be an image.',
            'profile_photo.mimes' => 'Profile photo must be a JPEG, PNG, JPG, GIF, or SVG.',
            'profile_photo.max' => 'Profile photo may not be greater than 2MB.',
            'date_of_birth.date' => 'Date of Birth must be a valid date.',
            'gender.in' => 'Invalid gender selected.',
            'emergency_contact.max' => 'Emergency contact name is too long.',
            'emergency_phone.regex' => 'Emergency phone number is not valid.',
            'emergency_phone.phone' => 'Emergency phone number is not in a valid format (e.g., 09xxxxxxxx).',
            'role.required' => 'A role must be assigned.',
            'role.exists' => 'The selected role is invalid.',
        ];
    }
}