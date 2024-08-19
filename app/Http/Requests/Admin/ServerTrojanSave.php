<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ServerTrojanSave extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'show' => '',
            'name' => 'required',
            'group_id' => 'required|array',
            'route_id' => 'nullable|array',
            'parent_id' => 'nullable|integer',
            'host' => 'required',
            'port' => 'required',
            'server_port' => 'required',
            'network' => 'required',
            'network_settings' => 'nullable',
            'allow_insecure' => 'nullable|in:0,1',
            'server_name' => 'nullable',
            'tags' => 'nullable|array',
            'rate' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The node name cannot be empty',
            'group_id.required' => 'The permission group cannot be empty',
            'group_id.array' => 'The permission group format is incorrect',
            'route_id.array' => 'The route group format is incorrect',
            'parent_id.integer' => 'The parent node format is incorrect',
            'host.required' => 'The node address cannot be empty',
            'port.required' => 'The connection port cannot be empty',
            'server_port.required' => 'The backend service port cannot be empty',
            'allow_insecure.in' => 'The "allow insecure" format is incorrect',
            'tags.array' => 'The tags format is incorrect',
            'rate.required' => 'The rate cannot be empty',
            'rate.numeric' => 'The rate format is incorrect'
        ];
    }
}
