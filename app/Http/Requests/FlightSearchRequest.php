<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlightSearchRequest extends FormRequest
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
            'origin' => ['required', 'string', 'max:100'],
            'destination' => ['required', 'string', 'max:100'],
            'date' => ['required', 'date', 'date_format:Y-m-d'],
            'sort' => ['nullable', 'in:price_asc,price_desc'],
            'max_price' => ['nullable', 'numeric'],
        ];
    }
    public function messages(): array
    {
        return [
            'date.date_format' => 'Date must be in YYYY-MM-DD format.',
        ];
    }
}
