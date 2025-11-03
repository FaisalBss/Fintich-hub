<?php

namespace App\Http\Requests\Trade;

use Illuminate\Foundation\Http\FormRequest;

class InitiateBuyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'symbol' => 'required|string|max:10',
            'quantity' => 'required|numeric|min:0.00001',
            'type' => 'required|string|in:stock,crypto'
        ];
    }
}
