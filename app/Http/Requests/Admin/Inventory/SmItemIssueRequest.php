<?php

namespace App\Http\Requests\Admin\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class SmItemIssueRequest extends FormRequest
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
            'role_id' => "required",
            'issue_date' => "required|date",
            'due_date' => "required|date",
            'item_category_id' =>  "required",
            'item_id' => "required",
            'quantity' => "required",
            'description' => "sometimes|nullable"
        ];
    }
}
