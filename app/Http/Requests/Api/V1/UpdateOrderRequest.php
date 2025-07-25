<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Stock;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'customer' => 'string|max:255',
            'warehouse_id' => 'exists:warehouses,id',
            'products' => 'array|min:1',
            'products.*.product_id' => 'exists:products,id',
            'products.*.count' => 'integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'products.min' => 'Заказ должен содержать хотя бы один товар',
            'products.*.product_id.exists' => 'Один из товаров не найден',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($this->products as $item) {
                $stock = Stock::where([
                    'warehouse_id' => $this->warehouse_id,
                    'product_id' => $item['product_id']
                ])->first();

                if (!$stock || $stock->stock < $item['count']) {
                    $validator->errors()->add(
                        'products',
                        "Недостаточно товара ID {$item['product_id']} на складе ID {$this->warehouse_id}"
                    );
                }
            }
        });
    }
}
