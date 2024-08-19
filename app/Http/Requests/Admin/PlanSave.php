<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PlanSave extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'content' => '',
            'group_id' => 'required',
            'transfer_enable' => 'required',
            'device_limit' => 'nullable|integer',
            'month_price' => 'nullable|integer',
            'quarter_price' => 'nullable|integer',
            'half_year_price' => 'nullable|integer',
            'year_price' => 'nullable|integer',
            'two_year_price' => 'nullable|integer',
            'three_year_price' => 'nullable|integer',
            'onetime_price' => 'nullable|integer',
            'reset_price' => 'nullable|integer',
            'reset_traffic_method' => 'nullable|integer|in:0,1,2,3,4',
            'capacity_limit' => 'nullable|integer',
            'speed_limit' => 'nullable|integer'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The package name cannot be empty',
            'type.required' => 'The package type cannot be empty',
            'type.in' => 'The package type format is incorrect',
            'group_id.required' => 'The permission group cannot be empty',
            'transfer_enable.required' => 'The data transfer limit cannot be empty',
            'device_limit.integer' => 'The device limit format is incorrect',
            'month_price.integer' => 'The monthly payment amount format is incorrect',
            'quarter_price.integer' => 'The quarterly payment amount format is incorrect',
            'half_year_price.integer' => 'The semi-annual payment amount format is incorrect',
            'year_price.integer' => 'The annual payment amount format is incorrect',
            'two_year_price.integer' => 'The biennial payment amount format is incorrect',
            'three_year_price.integer' => 'The triennial payment amount format is incorrect',
            'onetime_price.integer' => 'The one-time payment amount format is incorrect',
            'reset_price.integer' => 'The traffic reset package amount format is incorrect',
            'reset_traffic_method.integer' => 'The traffic reset method format is incorrect',
            'reset_traffic_method.in' => 'The traffic reset method format is incorrect',
            'capacity_limit.integer' => 'The user capacity limit format is incorrect',
            'speed_limit.integer' => 'The speed limit format is incorrect'

        ];
    }
}
