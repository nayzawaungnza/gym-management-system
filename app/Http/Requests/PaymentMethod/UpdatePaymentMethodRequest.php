<?php

namespace App\Http\Requests\PaymentMethod;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentMethodRequest extends FormRequest
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
        $paymentMethodId = $this->route('paymentmethod')->id;

        return [
            'display_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('payment_methods', 'display_name')->ignore($paymentMethodId),
            ],
            'provider_name' => 'nullable|string|max:255',
            'method_name' => 'nullable|string|max:255',
            'expire_minutes' => 'nullable|integer|min:0',
            'payment_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'display_name.required' => 'Display Name is required.',
            'display_name.unique' => 'A payment method with this display name already exists.',
            'expire_minutes.integer' => 'Expire Minutes must be an integer.',
            'expire_minutes.min' => 'Expire Minutes cannot be negative.',
            'payment_logo.image' => 'Payment Logo must be an image.',
            'payment_logo.mimes' => 'Payment Logo must be a JPEG, PNG, JPG, GIF, or SVG.',
            'payment_logo.max' => 'Payment Logo may not be greater than 2MB.',
        ];
    }
}
