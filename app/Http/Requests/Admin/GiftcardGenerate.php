<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GiftcardGenerate extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'generate_count' => 'nullable|integer|max:500',
            'name' => 'required',
            'type' => 'required|in:1,2,3,4',
            'value' => ['required_if:type,1,2,3', 'nullable', 'integer'],
            'started_at' => 'required|integer',
            'ended_at' => 'required|integer',
            'limit_use' => 'nullable|integer',
            'code' => ''
        ];
    }

    public function messages()
    {
        return [
            'generate_count.integer' => 'The generate count must be a number',
            'generate_count.max' => 'The maximum generate count is 500',
            'name.required' => 'The name cannot be empty',
            'type.required' => 'The type cannot be empty',
            'type.in' => 'The type format is incorrect',
            'value.required' => 'The value cannot be empty',
            'value.integer' => 'The value format is incorrect',
            'started_at.required' => 'The start time cannot be empty',
            'started_at.integer' => 'The start time format is incorrect',
            'ended_at.required' => 'The end time cannot be empty',
            'ended_at.integer' => 'The end time format is incorrect',
            'limit_use.integer' => 'The maximum usage count format is incorrect'
        ];
    }
}
