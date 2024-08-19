<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GiftcardGenerate;
use App\Models\Giftcard;
use App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiftcardController extends Controller
{
    public function fetch(Request $request)
    {
        $current = $request->input('current', 1);
        $pageSize = max($request->input('pageSize', 10), 10);
        $sortType = in_array($request->input('sort_type'), ['ASC', 'DESC']) ? $request->input('sort_type') : 'DESC';
        $sort = $request->input('sort', 'id');
        
        $builder = Giftcard::orderBy($sort, $sortType);
        $total = $builder->count();
        $giftcards = $builder->forPage($current, $pageSize)->get();

        return response([
            'data' => $giftcards,
            'total' => $total
        ]);
    }

    public function generate(GiftcardGenerate $request)
    {
        if ($request->input('generate_count')) {
            $this->multiGenerate($request);
            return;
        }

        $params = $request->validated();
        if (!$request->input('id')) {
            if (!isset($params['code'])) {
                $params['code'] = Helper::randomChar(16);
            }
            if (!Giftcard::create($params)) {
                abort(500, __('Gift card creation failed'));
            }
        } else {
            $giftcard = Giftcard::find($request->input('id'));
            if (!$giftcard) {
                abort(404, __('Gift card does not exist'));
            }
            try {
                $giftcard->update($params);
            } catch (\Exception $e) {
                abort(500, __('Gift card update failed'));
            }
        }

        return response([
            'data' => true
        ]);
    }

    private function multiGenerate(GiftcardGenerate $request)
    {
        $giftcards = [];
        $giftcard = $request->validated();
        $giftcard['created_at'] = $giftcard['updated_at'] = time();
        unset($giftcard['generate_count']);
        
        for ($i = 0; $i < $request->input('generate_count'); $i++) {
            do {
                $giftcard['code'] = Helper::randomChar(16);
            } while (Giftcard::where('code', $giftcard['code'])->exists());
            array_push($giftcards, $giftcard);
        }
        DB::beginTransaction();
        try {
            if (!Giftcard::insert($giftcards)) {
                throw new \Exception('Gift card batch generation failed');
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500, $e->getMessage());
        }

        $data = "Name,Type,Value,Start Time,End Time,Available Uses,Gift Card Code,Creation Time\r\n";
        foreach ($giftcards as $giftcard) {
            $type = ['', 'Amount', 'Duration', 'Data', 'Reset'][$giftcard['type']];
            $value = ['', round($giftcard['value']/100, 2), $giftcard['value'] . ' days', $giftcard['value'] . 'GB', '-'][$giftcard['type']];
            $startTime = date('Y-m-d H:i:s', $giftcard['started_at']);
            $endTime = date('Y-m-d H:i:s', $giftcard['ended_at']);
            $limitUse = $giftcard['limit_use'] ?? 'No Limit';
            $createTime = date('Y-m-d H:i:s', $giftcard['created_at']);
            $data .= "{$giftcard['name']},{$type},{$value},{$startTime},{$endTime},{$limitUse},{$giftcard['code']},{$createTime}\r\n";
        }        

        // Return the CSV data as a response
       echo($data);
    }

    public function drop(Request $request)
    {
        $giftcardId = $request->input('id');
        if (empty($giftcardId)) {
            abort(400, __('Parameter error'));
        }

        $giftcard = Giftcard::find($giftcardId);
        if (!$giftcard) {
            abort(404, __('Gift card does not exist'));
        }

        if (!$giftcard->delete()) {
            abort(500, __('Gift card deletion failed'));
        }

        return response([
            'data' => true
        ]);
    }
}
