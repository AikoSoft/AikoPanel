<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdate extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email:strict',
            'password' => 'nullable',
            'transfer_enable' => 'numeric',
            'device_limit' => 'nullable|integer',
            'expired_at' => 'nullable|integer',
            'banned' => 'required|in:0,1',
            'plan_id' => 'nullable|integer',
            'commission_rate' => 'nullable|integer|min:0|max:100',
            'discount' => 'nullable|integer|min:0|max:100',
            'u' => 'integer',
            'd' => 'integer',
            'balance' => 'integer',
            'commission_balance' => 'integer'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'The email cannot be empty',
            'email.email' => 'The email format is incorrect',
            'transfer_enable.numeric' => 'The data transfer format is incorrect',
            'device_limit.integer' => 'The device limit format is incorrect',
            'expired_at.integer' => 'The expiration time format is incorrect',
            'banned.required' => 'The ban status cannot be empty',
            'banned.in' => 'The ban status format is incorrect',
            'plan_id.integer' => 'The subscription plan format is incorrect',
            'commission_rate.integer' => 'The referral commission rate format is incorrect',
            'commission_rate.nullable' => 'The referral commission rate format is incorrect',
            'commission_rate.min' => 'The referral commission rate must be at least 0',
            'commission_rate.max' => 'The referral commission rate must be at most 100',
            'discount.integer' => 'The exclusive discount rate format is incorrect',
            'discount.nullable' => 'The exclusive discount rate format is incorrect',
            'discount.min' => 'The exclusive discount rate must be at least 0',
            'discount.max' => 'The exclusive discount rate must be at most 100',
            'u.integer' => 'The upload traffic format is incorrect',
            'd.integer' => 'The download traffic format is incorrect',
            'balance.integer' => 'The balance format is incorrect',
            'commission_balance.integer' => 'The commission balance format is incorrect'
        ];
    }
}
