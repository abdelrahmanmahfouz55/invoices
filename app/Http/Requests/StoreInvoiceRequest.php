<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'                         => ['required', 'in:invoice,quote'],
            'customer_id'                  => ['required', 'exists:customers,id'],
            'issue_date'                   => ['required', 'date'],
            'due_date'                     => ['nullable', 'date', 'after_or_equal:issue_date'],
            'tax_rate'                     => ['required', 'numeric', 'min:0', 'max:100'],
            'notes'                        => ['nullable', 'string', 'max:1000'],
            'items'                        => ['required', 'array', 'min:1'],
            'items.*.description'          => ['required', 'string', 'max:500'],
            'items.*.quantity'             => ['required', 'numeric', 'min:0.01', 'max:99999'],
            'items.*.unit_price'           => ['required', 'numeric', 'min:0', 'max:9999999'],
            'items.*.discount_percent'     => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required'                    => 'نوع المستند مطلوب.',
            'type.in'                          => 'نوع المستند غير صالح.',
            'customer_id.required'             => 'يرجى اختيار العميل.',
            'customer_id.exists'               => 'العميل المختار غير موجود.',
            'issue_date.required'              => 'تاريخ الإصدار مطلوب.',
            'issue_date.date'                  => 'تاريخ الإصدار غير صالح.',
            'due_date.date'                    => 'تاريخ الاستحقاق غير صالح.',
            'due_date.after_or_equal'          => 'تاريخ الاستحقاق يجب أن يكون بعد أو مساوياً لتاريخ الإصدار.',
            'tax_rate.required'                => 'نسبة الضريبة مطلوبة.',
            'tax_rate.numeric'                 => 'نسبة الضريبة يجب أن تكون رقماً.',
            'tax_rate.min'                     => 'نسبة الضريبة لا يمكن أن تكون سالبة.',
            'tax_rate.max'                     => 'نسبة الضريبة لا يمكن أن تتجاوز 100%.',
            'items.required'                   => 'يجب إضافة بند واحد على الأقل.',
            'items.min'                        => 'يجب إضافة بند واحد على الأقل.',
            'items.*.description.required'     => 'وصف البند مطلوب.',
            'items.*.quantity.required'        => 'الكمية مطلوبة.',
            'items.*.quantity.min'             => 'الكمية يجب أن تكون أكبر من صفر.',
            'items.*.unit_price.required'      => 'سعر الوحدة مطلوب.',
            'items.*.unit_price.min'           => 'سعر الوحدة لا يمكن أن يكون سالباً.',
            'items.*.discount_percent.min'     => 'نسبة الخصم لا يمكن أن تكون سالبة.',
            'items.*.discount_percent.max'     => 'نسبة الخصم لا يمكن أن تتجاوز 100%.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tax_rate' => $this->input('tax_rate', 15),
        ]);

        if ($this->has('items')) {
            $items = collect($this->items)->map(fn ($item) => [
                ...$item,
                'discount_percent' => $item['discount_percent'] ?? 0,
            ])->toArray();

            $this->merge(['items' => $items]);
        }
    }
}
