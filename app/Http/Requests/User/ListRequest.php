<?php

namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;

class ListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'startDate' => ['bail', 'nullable', 'string', 'date_format:Y-m-d H:i:s', 'before_or_equal:endDate'],
            'endDate' => ['bail', 'nullable', 'string', 'date_format:Y-m-d H:i:s'],
            'roleCode' => ['bail', 'nullable', 'string', 'exists:user_roles,code']
        ];
    }
}
