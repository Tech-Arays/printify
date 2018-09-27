<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;

class RegisterFormRequest extends Request
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
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'max:255',
            'last_name' => 'max:255',
            'username' => 'max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6'
        ];
    }
}
