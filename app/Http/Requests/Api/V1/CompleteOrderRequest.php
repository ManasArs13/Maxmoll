<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class CompleteOrderRequest extends FormRequest
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
            if ($this->order->status == 'canceled') {
                $validator->errors()->add('order', 'Only active orders can be completed.');
            }

            if ($this->order->status == 'completed') {
                $validator->errors()->add('order', 'This order has already been completed.');
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
