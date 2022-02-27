<?php

namespace App\Http\Requests\Admin\FrontSettings;

use Illuminate\Foundation\Http\FormRequest;

class SmCourseListRequest extends FormRequest
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
        $maxFileSize =generalSetting()->file_size*1024;
        return [
            'title' => 'required',
            'image' => 'required|dimensions:min_width=1420,min_height=450|mimes:jpg,jpeg,png|max:'.$maxFileSize,
            'category_id'=> 'required',
            'overview' =>'nullable',
            'outline' =>'nullable',
            'prerequisites' =>'nullable',
            'resources' =>'nullable',
            'stats' =>'nullable',
        ];
    }
}
