<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUrlPost extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'href' => 'required|max:255|url',
            'code' => 'nullable|string|min:6|max:32'
        ];
    }


    public function messages()
    {
        return [
            'href.required' => 'A URL-link is required, not empty.',
        ];
    }

}
