<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EquipmentRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $equipmentId = $this->route('equipment')?->id;

        return [
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('equipment')->ignore($equipmentId)
            ],
            'category' => 'required|string|max:255',
            'status' => 'required|in:operational,maintenance,out_of_order,retired',
            'purchase_date' => 'nullable|date|before_or_equal:today',
            'purchase_price' => 'nullable|numeric|min:0|max:999999.99',
            'warranty_expiry_date' => 'nullable|date|after_or_equal:purchase_date',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'specifications' => 'nullable|json',
            'maintenance_interval_days' => 'nullable|integer|min:1|max:365',
            'last_maintenance_date' => 'nullable|date|before_or_equal:today',
            'maintenance_notes' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Equipment name is required.',
            'category.required' => 'Equipment category is required.',
            'status.required' => 'Equipment status is required.',
            'status.in' => 'Invalid equipment status selected.',
            'serial_number.unique' => 'This serial number is already in use.',
            'purchase_price.numeric' => 'Purchase price must be a valid number.',
            'purchase_price.max' => 'Purchase price cannot exceed 999,999.99.',
            'warranty_expiry_date.after_or_equal' => 'Warranty expiry date must be after or equal to purchase date.',
            'maintenance_interval_days.min' => 'Maintenance interval must be at least 1 day.',
            'maintenance_interval_days.max' => 'Maintenance interval cannot exceed 365 days.',
            'last_maintenance_date.before_or_equal' => 'Last maintenance date cannot be in the future.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'Image must be a JPEG, PNG, JPG, or GIF file.',
            'image.max' => 'Image size cannot exceed 2MB.',
            'specifications.json' => 'Specifications must be valid JSON format.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert empty strings to null for nullable fields
        $nullableFields = [
            'brand', 'model', 'serial_number', 'purchase_date', 'purchase_price',
            'warranty_expiry_date', 'location', 'description', 'specifications',
            'maintenance_interval_days', 'last_maintenance_date', 'maintenance_notes'
        ];

        foreach ($nullableFields as $field) {
            if ($this->has($field) && $this->input($field) === '') {
                $this->merge([$field => null]);
            }
        }
    }
}