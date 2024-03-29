<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;
    
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
            'email' => 'email|min:2|max:255|required|unique:users,email',
            'first_name' => 'string|min:2|max:255|required',
            'last_name' => 'string|min:2|max:255|required',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'password_confirmation' => 'required',
        ];
    }
}
