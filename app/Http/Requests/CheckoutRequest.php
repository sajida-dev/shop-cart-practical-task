<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->cart()->exists() === true;
    }

    public function rules(): array
    {
        return [
            'shipping_address' => ['required', 'string', 'max:1000'],
            'billing_address'  => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_address.required' => 'Shipping address is required.',
            'billing_address.required'  => 'Billing address is required.',
        ];
    }
}
