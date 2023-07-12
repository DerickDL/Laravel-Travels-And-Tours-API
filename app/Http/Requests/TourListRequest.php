<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TourListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // dd(request()->all());
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'priceTo' => 'numeric',
            'priceFrom' => 'numeric',
            'dateFrom' => 'date',
            'dateTo' => 'date',
            'sortBy' => Rule::in(['price']),
            'sortOrder' => Rule::in(['asc', 'desc'])
        ];
    }

    /**
     * Custom error messages for validation
     */
    public function messages(): array
    {
        return [
            'priceFrom' => 'The priceFrom parameter must be numeric',
            'priceTo' => 'The priceTo parameter must be numeric',
            'dateFrom' => 'The dateFrom parameter must be in date format',
            'dateTo' => 'The dateTo parameter must be in date format',
            'sortBy' => 'The sortBy parameter must be price',
            'sortOrder' => 'The sortOrder parameter must be asc or desc only'
        ];
    }
}
