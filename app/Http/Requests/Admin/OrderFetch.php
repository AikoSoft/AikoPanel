<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OrderFetch extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'filter.*.key' => 'required|in:email,trade_no,status,commission_status,user_id,invite_user_id,callback_no,commission_balance',
            'filter.*.condition' => 'required|in:>,<,=,>=,<=,模糊,!=',
            'filter.*.value' => ''
        ];
    }

    public function messages()
    {
        return [
            'filter.*.key.required' => 'The filter key cannot be empty',
            'filter.*.key.in' => 'The filter key parameter is incorrect',
            'filter.*.condition.required' => 'The filter condition cannot be empty',
            'filter.*.condition.in' => 'The filter condition parameter is incorrect'

        ];
    }
}
