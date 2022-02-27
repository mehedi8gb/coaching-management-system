<?php

namespace App\Http\Requests\Admin\Library;

use Illuminate\Foundation\Http\FormRequest;

class SmBookRequest extends FormRequest
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

            'book_title' => "required|max:200",
            'book_category_id' => "required",
            'quantity' => "sometimes|nullable|integer|min:0",            
            'book_number' => "sometimes|nullable",
            'isbn_no' => "sometimes|nullable",
            'publisher_name' => "sometimes|nullable",
            'author_name' => "sometimes|nullable",
            'details' => "sometimes|nullable",
            'book_price' => "sometimes|nullable|integer|min:0",
            'rack_number' => "sometimes|nullable",
            'book_price' => "sometimes|nullable",
        ];
    }
}
