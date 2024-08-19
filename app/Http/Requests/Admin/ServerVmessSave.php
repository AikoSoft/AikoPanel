<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ServerVmessSave extends FormRequest
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
            'tls' => 'required',
            'tags' => 'nullable|array',
            'rate' => 'required|numeric',
            'network' => 'required|in:tcp,kcp,ws,http,domainsocket,quic,grpc,httpupgrade,splithttp',
            'networkSettings' => 'nullable|array',
            'ruleSettings' => 'nullable|array',
            'tlsSettings' => 'nullable|array',
            'dnsSettings' => 'nullable|array'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The node name cannot be empty',
            'group_id.required' => 'The permission group cannot be empty',
            'group_id.array' => 'The permission group format is incorrect',
            'route_id.array' => 'The route group format is incorrect',
            'parent_id.integer' => 'The parent ID format is incorrect',
            'host.required' => 'The node address cannot be empty',
            'port.required' => 'The connection port cannot be empty',
            'server_port.required' => 'The backend service port cannot be empty',
            'tls.required' => 'TLS cannot be empty',
            'tags.array' => 'The tags format is incorrect',
            'rate.required' => 'The rate cannot be empty',
            'rate.numeric' => 'The rate format is incorrect',
            'network.required' => 'The transport protocol cannot be empty',
            'network.in' => 'The transport protocol format is incorrect',
            'networkSettings.array' => 'The transport protocol configuration is incorrect',
            'ruleSettings.array' => 'The rule configuration is incorrect',
            'tlsSettings.array' => 'The TLS configuration is incorrect',
            'dnsSettings.array' => 'The DNS configuration is incorrect'
        ];
    }
}
