<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentSave;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function getPaymentMethods()
    {
        $methods = [];
        foreach (glob(base_path('app//Payments') . '/*.php') as $file) {
            array_push($methods, pathinfo($file)['filename']);
        }
        return response([
            'data' => $methods
        ]);
    }

    public function fetch()
    {
        $payments = Payment::orderBy('sort', 'ASC')->get();
        foreach ($payments as $k => $v) {
            $notifyUrl = url("/api/v1/guest/payment/notify/{$v->payment}/{$v->uuid}");
            if ($v->notify_domain) {
                $parseUrl = parse_url($notifyUrl);
                $notifyUrl = $v->notify_domain . $parseUrl['path'];
            }
            $payments[$k]['notify_url'] = $notifyUrl;
        }
        return response([
            'data' => $payments
        ]);
    }

    public function getPaymentForm(Request $request)
    {
        $paymentService = new PaymentService($request->input('payment'), $request->input('id'));
        return response([
            'data' => $paymentService->form()
        ]);
    }

    public function show(Request $request)
    {
        $payment = Payment::find($request->input('id'));
        if (!$payment) abort(500, __('Payment method does not exist'));
        $payment->enable = !$payment->enable;
        if (!$payment->save()) abort(500, __('Save failed'));
        return response([
            'data' => true
        ]);
    }

    public function save(Request $request)
    {
        if (!config('aikopanel.app_url')) {
            abort(500, __('Please set the APP_URL in the .env file'));
        }
        $params = $request->validate([
            'name' => 'required',
            'icon' => 'nullable',
            'payment' => 'required',
            'config' => 'required',
            'notify_domain' => 'nullable|url',
            'handling_fee_fixed' => 'nullable|integer',
            'handling_fee_percent' => 'nullable|numeric|between:0.1,100'
        ], [
            'name.required' => 'Display name cannot be empty',
            'payment.required' => 'Payment method cannot be empty',
            'config.required' => 'Configuration cannot be empty',
            'notify_domain.url' => 'Notify domain format error',
            'handling_fee_fixed.integer' => 'Fixed handling fee must be an integer',
            'handling_fee_percent.between' => 'Handling fee percentage must be between 0.1 and 100'
        ]);
        if ($request->input('id')) {
            $payment = Payment::find($request->input('id'));
            if (!$payment) abort(500, __('Payment method does not exist'));
            try {
                $payment->update($params);
            } catch (\Exception $e) {
                abort(500, $e->getMessage());
            }
            return response([
                'data' => true
            ]);
        }
        $params['uuid'] = Helper::randomChar(8);
        if (!Payment::create($params)) {
            abort(500, __('Creation failed'));
        }
        return response([
            'data' => true
        ]);
    }

    public function drop(Request $request)
    {
        $payment = Payment::find($request->input('id'));
        if (!$payment) abort(500, __('Payment method does not exist'));
        return response([
            'data' => $payment->delete()
        ]);
    }


    public function sort(Request $request)
    {
        $request->validate([
            'ids' => 'required|array'
        ], [
            'ids.required' => 'Incorrect parameters',
            'ids.array' => 'Incorrect parameters'
        ]);
        DB::beginTransaction();
        foreach ($request->input('ids') as $k => $v) {
            if (!Payment::find($v)->update(['sort' => $k + 1])) {
                DB::rollBack();
                abort(500, __('Save failed'));
            }
        }
        DB::commit();
        return response([
            'data' => true
        ]);
    }
}
