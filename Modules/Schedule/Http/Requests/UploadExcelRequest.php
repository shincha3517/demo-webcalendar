<?php

namespace Modules\Schedule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Internationalisation\BaseFormRequest;

class UploadExcelRequest extends FormRequest
{

    public function rules()
    {
        return [
            'interval'=>'required',
            'startTime'=>'required',
            'importedFile' => 'required',
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
