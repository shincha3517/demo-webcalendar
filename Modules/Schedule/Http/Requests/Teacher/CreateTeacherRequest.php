<?php

namespace Modules\Schedule\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class CreateTeacherRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
            'phone_number'=> 'required',
            'subject'=> 'required',
            'email' => 'required|unique:users|email',
            'password' => 'required|min:3|confirmed',
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [];
    }
}
