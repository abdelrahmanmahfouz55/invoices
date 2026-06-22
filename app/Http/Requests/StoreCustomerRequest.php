<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'email'      => ['nullable', 'email', 'max:255'],
            'address'    => ['nullable', 'string', 'max:500'],
            'tax_number' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم العميل مطلوب.',
            'name.max'      => 'اسم العميل يجب ألا يتجاوز 255 حرفاً.',
            'email.email'   => 'البريد الإلكتروني غير صالح.',
            'phone.max'     => 'رقم الهاتف يجب ألا يتجاوز 20 رقماً.',
        ];
    }
}
