<?php

namespace App\Http\Requests\Admin\Examination;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SmMarkGradeRequest extends FormRequest
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
        $school_id=auth()->user()->school_id;
        return [
            'grade_name' => ['required', 'max:50' , Rule::unique('sm_marks_grades')->where('school_id', $school_id)->ignore($this->id) ],
            'gpa' => ['required', 'max:4', Rule::unique('sm_marks_grades')->where('school_id', $school_id)->ignore($this->id) ],
            'percent_from' => "required|integer|min:0",
            'percent_upto' => "required|integer|min:",
            'grade_from' => "required|max:4|min:0",
            'grade_upto' => "required|max:4|min:",
            'description'=>'sometimes|nullable'
        ];
    }
}
