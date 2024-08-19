<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class KnowledgeSave extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'category' => 'required',
            'language' => 'required',
            'title' => 'required',
            'body' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'The title cannot be empty',
            'category.required' => 'The category cannot be empty',
            'body.required' => 'The content cannot be empty',
            'language.required' => 'The language cannot be empty'

        ];
    }
}
