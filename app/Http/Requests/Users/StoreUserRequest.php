<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users'],
            'first_name' => ['required', 'string', 'min:3', 'max:50'],
            'roles' => ['required', 'array'],
        ];
    }
}
