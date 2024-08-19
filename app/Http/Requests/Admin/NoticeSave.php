<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class NoticeSave extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'content' => 'required',
            'img_url' => 'nullable|url',
            'tags' => 'nullable|array'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'The title cannot be empty',
            'content.required' => 'The content cannot be empty',
            'img_url.url' => 'The image URL format is incorrect',
            'tags.array' => 'The tags format is incorrect'

        ];
    }
}
