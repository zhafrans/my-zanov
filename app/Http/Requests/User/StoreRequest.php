<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'uppercase' , 'max:100'],
            'email' => ['bail', 'required', 'string', 'email', 'max:100', 'unique:users,email'],
            'password' => ['bail', 'required', 'string'],
            'roleCode' => ['bail', 'required', 'string', 'exists:user_roles,code'],
            'tenantCode' => ['bail', 'nullable', 'string', 'exists:tenants,code']
        ];
    }
}
