<?php

namespace App\Http\Requests\MembershipType;

use Illuminate\Foundation\Http\FormRequest;

class CreateMembershipTypeRequest extends FormRequest
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
            'type_name' => 'required|string|max:255|unique:membership_types,type_name',
            'duration_months' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'type_name.required' => 'Membership type name is required.',
            'type_name.unique' => 'A membership type with this name already exists.',
            'duration_months.required' => 'Duration in months is required.',
            'duration_months.integer' => 'Duration must be an integer.',
            'duration_months.min' => 'Duration must be at least 1 month.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price cannot be negative.',
        ];
    }
}
