<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\App;
use App\Models\Subscription;
use Validator;
use Hash;
use Carbon\Carbon;
use App\Jobs\SubscriptionStatusJob;

class DeviceController extends Controller
{
    public function register(Request $request) {
        /* Register device endpoint */
        $validator =  Validator::make($request->all(),[
            'uuid' => 'required|max:255',
            'app_id' => 'required|exists:apps,id',
            'language' => 'required',
            'os' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["status" => false, "message" => $validator->messages()->first()], 400);
        }

        // Create a token for this device.

        $token = Hash::make($request->uuid);

        $request["client_token"] = $token;

        // Check if device exists
        $device = Device::where("uuid", $request->uuid)->first();

        //Create device if not exists
        if (!$device) {
            $device = Device::create($request->all());
        }

        return response()->json(["success" => "true", "device" => $device], 200);
    }

    public function purchase(Request $request) {
        // Purchase endpoint

        $validator =  Validator::make($request->all(),[
            'client_token' => 'required|max:255',
            'receipt' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(["status" => false, "message" => $validator->messages()->first()], 400);
        }
        
        $device = Device::where("client_token", $request->client_token)->first();

        if(!$device) {
            return response()->json(["status" => false, "message" => "Client token not found"], 404);
        } else {
            $os = $device->os;
            if ($os === 'android') {
                // Call Google Play Store mock API
                $mockResponse = $this->mockGoogle($request);
            } else if ($os === 'ios') {
                // Call App Store mock API
                $mockResponse = $this->mockApple($request);
            } else {
                return response()->json(["status" => false, "message" => "Specified OS does not exist."], 404);
            }
            if (json_decode($mockResponse->content())->message->status) {
                $subscription = Subscription::where("device_id", $device->id)->where("app_id", $device->app_id)->first();
                if (!$subscription) {
                    $subscription = Subscription::create([
                        'status' => 'Started',
                        'device_id' => $device->id,
                        'app_id' => $device->app_id,
                        'receipt' => $request->receipt,
                        'expired_at' => json_decode($mockResponse->content())->message->expireDate
                    ]);
                } else {
                    if (json_decode($mockResponse->content())->message->expireDate > $subscription->expired_at) {
                        $subscription->status = "Renewed";
                    } else {
                        $subscription->status = "Cancelled";
                    }
                    $subscription->expired_at = json_decode($mockResponse->content())->message->expireDate;
                    $subscription->save();
                }
                $this->dispatch( (new SubscriptionStatusJob($subscription)));
                return response()->json(["success" => "true", "subscription" => $subscription], 200);
            } else {
                return $mockResponse;
            }
        }
    }
    public function mockGoogle(Request $request) {
       // Mock Google Verify Service

       $validator =  Validator::make($request->all(),[
            'receipt' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(["status" => false, "message" => $validator->messages()->first()], 400);
        }

        return $this->verifyReceipt($request->receipt);
    }

    public function mockApple(Request $request) {
       // Mock Apple Verify Service

       $validator =  Validator::make($request->all(),[
            'receipt' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(["status" => false, "message" => $validator->messages()->first()], 400);
        }

        return $this->verifyReceipt($request->receipt);
    }

    protected function verifyReceipt($receipt) {
        if (substr($receipt, -1) % 2 !== 0) {
            $randomStatus = rand(0,1);
            if($randomStatus) {
                $expireDate = Carbon::now()->addMonths(rand(1, 120))->format("Y-m-d h:i:s");
            } else {
                $expireDate = null;
            }
            $response = [
                "status" => $randomStatus,
                "expireDate" => $expireDate
            ];
            return response()->json(["status" => true, "message" => $response], 200);
        } else {
            $response = [
                "status" => false,
                "message" => "Receipt is not available."
            ];
            return response()->json(["status" => false, "message" => $response], 400);
        }
    }

    public function checkSubscription(Request $request) {
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
            $subscription = Subscription::where("device_id", $device->id)->where("app_id", $device->app_id)->first();
            if (!$subscription) {
                return response()->json(["status" => false, "message" => "Subscription not found"], 404);
            } else {
                return response()->json(["status" => true, "message" => $subscription], 404);
            }
        }
    }
}
