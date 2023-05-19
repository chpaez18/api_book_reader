<?php

namespace App\Http\Requests\Users;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
    public function rules()
    {
        return [
            'email' => ['string', 'email', 'max:100', Rule::unique('users')->ignore($this->user)],
            'first_name' => ['string', 'min:3', 'max:50'],
            'password' => ['string', 'min:3', 'max:50'],
            'status' => ['integer'],
            'roles' => ['array'],
        ];
    }

    
}
