<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Weight;


class WeightController extends Controller
{
    /** 体重を保存するAPI */
    public function store_today(Request $request)
    {
        $validated = $request->validate([
            'weight' => 'required|numeric',
        ]);

        $record = Weight::where('user_id', $request->user()->id)
                ->whereDate('date', Carbon::today())->first();

        if ($record) {
            $record->update([
                'weight' => $validated['weight'],
            ]);
        } else {
            Weight::create([
                'user_id' => $request->user()->id,
                'weight' => $validated['weight'],
                'date' => Carbon::today(),
            ]);
        }

        return response()->json([], 200);
    }

    /** ユーザーの体重情報（一週間分）を取得するAPI */
    public function get_daily(Request $request) {
        // dd($request->user()->weight_daily());
        return response()->json($request->user()->weight_daily(), 200);
    }

    /** ユーザーの体重情報（週ごとの平均体重情報）を取得するAPI */
    public function get_weekly(Request $request) {
        return response()->json($request->user()->weight_weekly(), 200);
    }
}
