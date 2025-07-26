<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Stock;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class ReturnOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($this->order->products as $item) {
                $stock = Stock::where([
                    'warehouse_id' => $this->order->warehouse_id,
                    'product_id' => $item->id
                ])->first();

                if (!$stock || $stock->stock < $item->pivot->count) {
                    $validator->errors()->add(
                        'items',
                        "Недостаточно товара ID {$item->id} на складе"
                    );
                }
            }

            if ($this->order->status == 'completed') {
                $validator->errors()->add('order', 'Only canceled orders can be canceled.');
            }

            if ($this->order->status == 'active') {
                $validator->errors()->add('order', 'This order has already been actived.');
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
