<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ServerShadowsocksSave extends FormRequest
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
            'parent_id' => 'nullable|integer',
            'route_id' => 'nullable|array',
            'host' => 'required',
            'port' => 'required',
            'server_port' => 'required',
            'cipher' => 'required|in:aes-128-gcm,aes-192-gcm,aes-256-gcm,chacha20-ietf-poly1305,2022-blake3-aes-128-gcm,2022-blake3-aes-256-gcm',
            'obfs' => 'nullable|in:http',
            'obfs_settings' => 'nullable|array',
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
            'cipher.required' => 'The encryption method cannot be empty',
            'tags.array' => 'The tags format is incorrect',
            'rate.required' => 'The rate cannot be empty',
            'rate.numeric' => 'The rate format is incorrect',
            'obfs.in' => 'The obfuscation format is incorrect',
            'obfs_settings.array' => 'The obfuscation settings format is incorrect'
        ];
    }
}
