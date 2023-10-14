<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
            'first_name' => 'string|min:2|max:255|required',
            'last_name' => 'string|min:2|max:255|required',
            'email' => [
                'email', 'min:2', 'max:255', 'required', Rule::unique('users', 'email')->ignore($this->route('id'))
            ],
            'avatar_path' => 'nullable|string|max:255',
        ];
    }
}
