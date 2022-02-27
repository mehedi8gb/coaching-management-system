<?php

namespace App\Http\Requests\Admin\StudentInfo;

use App\SmStudent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SmStudentAdmissionRequest extends FormRequest
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
        $student = null;
        if ($this->id) {
            $student = SmStudent::with('parents')->findOrFail($this->id);
        }
        $school_id = auth()->user()->school_id;
        $academic_id = getAcademicId();
        
        $rules= [
            'session' => 'required',
            'class' => 'required',
            'section' => 'required',
            'admission_number' => ['required', 'nullable', 'integer', Rule::unique('sm_students', 'admission_no')->ignore(optional($student)->id)->where('school_id', $school_id)],
            'first_name' => 'required|max:100',
            'last_name' =>'nullable|max:100',
            'email_address' => ['sometimes', 'nullable', 'email', Rule::unique('users', 'email')->ignore(optional($student)->user_id)],
            'gender' => 'required',
            'date_of_birth' => 'required',
            'blood_group'=>'nullable|integer',
            'religion'=>'nullable|integer',
            'caste'=>'nullable',
            'phone_number'=>'nullable',
            'admission_date'=>'required|date',
            'student_category_id'=>'nullable|integer',
            'student_group_id' => 'nullable|integer',
            'height'=>'nullable',
            'weight'=>'nullable',
            'photo' => "sometimes|nullable|file|mimes:jpg,jpeg,png|max:".$maxFileSize,
            'fathers_name'=>'nullable|max:100',
            'fathers_occupation'=>'nullable|max:100',
            'fathers_phone'=>'nullable|max:100',
            'fathers_photo'=>'sometimes|nullable|file|mimes:jpg,jpeg,png|max:'.$maxFileSize,
            'mothers_name'=>'nullable|max:100',
            'mothers_occupation'=>'nullable|max:100',
            'mothers_phone'=>'nullable|max:100',
            'mothers_photo'=>'sometimes|nullable|mimes:jpg,jpeg,png',
            'guardians_name' =>'nullable|max:100',
            'relation'=>'required',
            'guardians_email' => "required_without:parent_id|different:email_address|unique:users,email,".optional(optional($student)->parents)->user_id,
            'guardians_photo' => 'sometimes|nullable|file|mimes:jpg,jpeg,png|max:'.$maxFileSize,
            'guardians_phone'=>'nullable|max:100',
            'guardians_occupation'=>'nullable|max:100',
            'guardians_address' => 'nullable|max:200',
            'current_address' => 'nullable|max:200',
            'permanent_address' => 'nullable|max:200',
            'route'=>'nullable|integer',
            'vehicle' =>'nullable|integer',
            'dormitory_name'=>'nullable|integer',
            'room_number' =>'nullable|integer',
            'national_id_number'=>'nullable',
            'local_id_number'=>'nullable',
            'bank_account_number'=>'nullable',
            'bank_name'=>'nullable',
            'previous_school_details'=>'nullable',
            'additional_notes'=>'nullable',
            'ifsc_code'=>'nullable',
            'document_file_1' => "sometimes|nullable|mimes:pdf,doc,docx,jpg,jpeg,png,txt|max:".$maxFileSize,
            'document_file_2' => "sometimes|nullable|mimes:pdf,doc,docx,jpg,jpeg,png,txt|max:".$maxFileSize,
            'document_file_3' => "sometimes|nullable|mimes:pdf,doc,docx,jpg,jpeg,png,txt|max:".$maxFileSize,
            'document_file_4' => "sometimes|nullable|mimes:pdf,doc,docx,jpg,jpeg,png,txt|max:".$maxFileSize,
        ];

        //added by abu nayem lead id number check replace of roll number

        if (moduleStatusCheck('Lead')==true) {
            $rules['roll_number'] = ['sometimes','nullable', Rule::unique('sm_students', 'roll_no')->ignore(optional($student)->id)->where('school_id', $school_id)->where('academic_id', $academic_id)->where('class_id', $this->class_id)];

            $rules['phone_number'] = ['sometimes','nullable', Rule::unique('sm_students', 'mobile')->ignore(optional($student)->id)];
       
        }

        return $rules;
    }
    public function attributes()
    {

        $attributes =  [
            'session' => 'Academic',
        ];
        if (moduleStatusCheck('Lead')==true) {
            $attributes['roll_number'] = 'ID Number';
        }
        return $attributes;
    }
}
