<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'currentPassword' => ['bail', 'required', 'string', 'currentPassword:sanctum'],
            'newPassword' => [
                'bail',
                'required',
                'string',
                'different:currentPassword',
                Password::min(8)->mixedCase()->numbers()->symbols(),
                'same:newPasswordConfirmation'
            ],
            'newPasswordConfirmation' => ['bail', 'required', 'string']
        ];
    }
}
