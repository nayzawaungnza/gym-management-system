<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust authorization logic as needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $memberId = $this->route('member')->id; // Get the member ID from the route

        return [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => ['required', 'email', Rule::unique('members')->ignore($memberId)],
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string|max:255',
            'membership_type_id' => 'required|exists:membership_types,id',
            'membership_start_date' => 'required|date',
            'membership_end_date' => 'required|date|after:membership_start_date',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,suspended,expired',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'remove_profile_photo' => 'nullable|boolean', // For removing existing photo
            'medical_conditions' => 'nullable|string',
            'fitness_goals' => 'nullable|string',
            'preferred_workout_time' => 'nullable|string|max:50',
            'referral_source' => 'nullable|string|max:100',
            'member_id' => ['nullable', 'string', 'max:191', Rule::unique('members')->ignore($memberId)],
        ];
    }
}