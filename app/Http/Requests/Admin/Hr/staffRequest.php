<?php

namespace App\Http\Requests\Admin\Hr;

use App\SmStaff;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class staffRequest extends FormRequest
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

        $maxFileSize = generalSetting()->file_size*1024;
        $staff = null;
        $id = $this->id;
        if (!$id){
            $id = $this->staff_id;
        }
        if ($id) {
            $staff = SmStaff::findOrFail($id);
        }


        $school_id=auth()->user()->school_id;

        return [
            'staff_no' => ['required', 'integer', Rule::unique('sm_staffs', 'staff_no')->where('school_id', $school_id)->ignore($id) ],
            'role_id' => "required|integer",
            'staff_photo' => "image|mimes:jpeg,png,jpg|max:".$maxFileSize,
            'department_id' => "required|integer",
            'designation_id' => "required|integer",
            'first_name' => "required|max:200",
            'last_name' => "required|max:200",
            'fathers_name'=>'nullable|max:200',
            'mothers_name'=>'nullable|max:200',
            'email' => ['required' ,Rule::unique('users', 'email')->ignore($staff ? $staff->user_id : null)],
            'gender_id' => "required|integer",
            'date_of_birth' => "nullable|date",
            'date_of_joining' => "required|date",
            'mobile' => "max:30",
            'marital_status' => "nullable",
            'emergency_mobile'=> "nullable",
            'driving_license' => "nullable",
            'staff_photo' => "image|mimes:jpeg,png,jpg|max:".$maxFileSize,
            'current_address' => "required|max:255",
            'permanent_address' => "nullable|max:255",
            'qualification'=>"nullable|max:255",
            'experience'=>"nullable|max:255",
            'epf_no' =>"nullable|max:255",
            'basic_salary' => "required|max:100",
            'basic_salary'=>"nullable",
            'contract_type'=>"nullable",
            'location'=>"nullable|max:255",
            'bank_account_name'=>"nullable|max:255",
            'bank_account_no'=>"nullable|max:255",
            'bank_brach'=>"nullable|max:255",
            'facebook_url'=>"nullable",
            'twiteer_url'=>"nullable",
            'linkedin_url'=>"nullable",
            'instragram_url'=>"nullable",
            'resume' => "sometimes|nullable|mimes:pdf,doc,docx|max:".$maxFileSize,
            'joining_letter' => "sometimes|nullable|mimes:pdf,doc,docx|max:".$maxFileSize,
            'other_document' => "sometimes|nullable|mimes:pdf,doc,docx,jpg,jpeg,png,txt|max:".$maxFileSize,
        ];
    }
}
