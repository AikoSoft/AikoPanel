<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CouponGenerate extends FormRequest
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
            'type' => 'required|in:1,2',
            'value' => 'required|integer',
            'started_at' => 'required|integer',
            'ended_at' => 'required|integer',
            'limit_use' => 'nullable|integer',
            'limit_use_with_user' => 'nullable|integer',
            'limit_plan_ids' => 'nullable|array',
            'limit_period' => 'nullable|array',
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
'value.required' => 'The amount or percentage cannot be empty',
'value.integer' => 'The amount or percentage format is incorrect',
'started_at.required' => 'The start time cannot be empty',
'started_at.integer' => 'The start time format is incorrect',
'ended_at.required' => 'The end time cannot be empty',
'ended_at.integer' => 'The end time format is incorrect',
'limit_use.integer' => 'The maximum usage count format is incorrect',
'limit_use_with_user.integer' => 'The user usage limit format is incorrect',
'limit_plan_ids.array' => 'The specified subscription format is incorrect',
'limit_period.array' => 'The specified period format is incorrect'

        ];
    }
}
