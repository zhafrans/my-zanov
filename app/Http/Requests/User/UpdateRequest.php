<?php

namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'isActive' => ['bail', 'required', 'string', Rule::in(['0', '1'])],
            'name' => ['bail', 'required', 'uppercase', 'string', 'max:100'],
            'password' => ['bail', 'nullable', 'string']
        ];
    }
}
