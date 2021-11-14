<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Device;
use App\Models\Subscription;
use DB;

class SubscriptionController extends Controller
{
    public function check(Request $request) {
        // Check subscription service

       $validator =  Validator::make($request->all(),[
            'client_token' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(["status" => false, "message" => $validator->messages()->first()], 400);
        }
        $device = Device::where("client_token", $request->client_token)->first();

        if (!$device) {
            return response()->json(["status" => false, "message" => "Client token not found"], 404);
        } else {
            $subscription = Subscription::where("device_id", $device->id)->where("app_id", $device->app_id)->get();
            if (!$subscription) {
                return response()->json(["status" => false, "message" => "Subscription not found"], 404);
            } else {
                return response()->json(["status" => true, "message" => $subscription], 404);
            }
        }
    }

    public function report() {
        $report = DB::select(DB::raw("SELECT Year(s.expired_at)  AS year,
                    Month(s.expired_at) AS month,
                    Day(s.expired_at) AS day,
                    d.os as operating_system,
                    s.status AS status,
                    count(*) as total
            FROM  `subscriptions` s, `devices` d
            WHERE s.device_id = d.id
            GROUP  BY Year(s.expired_at),
                    Month(s.expired_at),
                    Day(s.expired_at),
                    s.status,
                    d.os
            ORDER  BY year,
                    month,
                    day"));
        return response()->json(["status" => true, "message" => $report], 200);
    }
}
