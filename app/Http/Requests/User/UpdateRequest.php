<?php

namespace App\Http\Requests\User;

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
            'isActive' => ['bail', 'sometimes', 'string', Rule::in(['0', '1'])],
            'name' => ['bail', 'sometimes', 'string', 'max:100'],
            'password' => ['bail', 'sometimes', 'string']
        ];
    }
}
