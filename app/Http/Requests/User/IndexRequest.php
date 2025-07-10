<?php

namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge(
            $this->pageRules(),
            $this->sortRules(['name', 'created_at']),
            $this->searchRules(['name', 'email', 'phone']),
            [
                'roleCode' => ['bail', 'nullable', 'string', 'exists:user_roles,code'],
            ]
        );
    }
}
