<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Stock;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'customer' => 'required|string|max:255',
            'warehouse_id' => 'required|exists:warehouses,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.count' => 'required|integer|min:1',
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
            if ($this->products !== null) {
                foreach ($this->products as $item) {
                    $stock = Stock::where([
                        'warehouse_id' => $this->warehouse_id,
                        'product_id' => $item['product_id']
                    ])->first();

                    if (!$stock || $stock->stock < $item['count']) {
                        $validator->errors()->add(
                            'items',
                            "Недостаточно товара ID {$item['product_id']} на складе"
                        );
                    }
                }
            }
        });
    }

    protected function failedValidation($validator)
    {
        throw new ValidationException($validator, response()->json([
            'code' => 422,
            'message' => $validator->getMessageBag(),
            'errors' => $validator->errors()
        ], 422));
    }
}
