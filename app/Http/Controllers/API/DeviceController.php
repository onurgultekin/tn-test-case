<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\App;
use Validator;
use Hash;

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
            return response()->json(["status" => false, "message" => $validator->messages()->first()]);
        }

        // Create a token for this device.

        $token = Hash::make($request->uuid);

        $request["client_token"] = $token;

        $device = Device::where("uuid", $request->uuid)->first();

        if (!$device) {
            $device = Device::create($request->all());
        }

        return response()->json(["success" => "true", "device" => $device]);

    }
}
