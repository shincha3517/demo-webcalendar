<?php

namespace Modules\Schedule\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Internationalisation\BaseFormRequest;
use Modules\Schedule\Entities\Teacher;

class UpdateTeacherRequest extends FormRequest
{

    public function rules()
    {
        $teacherId = $this->route()->parameter('id');
        $teacher = Teacher::find($teacherId);
        $userId = $teacher->user_id;


        return [
            'name'=>'required',
            'email'=>"required|email|unique:users,email,{$userId}",
            'subject' => 'required',
            'phone_number' => 'required',
            'password' => 'confirmed',
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
