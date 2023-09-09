<?php

namespace App\Http\Controllers\Api;

use App\Enums\Constants;
use App\Http\Controllers\Controller;
use App\Jobs\StoreDeviceData;
use App\Models\AirFlow;
use App\Models\ButtonStatus;
use App\Models\DeviceNote;
use App\Models\DeviceTemp;
use App\Models\Humidity;
use App\Models\Temperature;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    public function microtime(Request $request)
    {
        return "*" . (int) (microtime(true) * 1000) . "#";


        $data['device_time'] = '*'.strtotime(Carbon::now()).'#';
        $data['device_time_milli'] = '*'.now()->getTimestampMs().'#';
        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'device config'
        ]);
    }

    public function storeDevice(Request $request)
    {
        $status = false;
        $uuid = null;
        if ($request->filled('device_uuid')) {
            $device_uuid = explode('-',$request->device_uuid);
            $first = @$device_uuid[0];
            $second = @$device_uuid[1];
            if ($first && $second) {
                $uuid = generate_new_device_uuid_code($first,$second);
                $status = true;
            }
        }
        if ($status && $uuid) {
            $temp = DeviceTemp::query()->create(['uuid'=>$uuid,'prefix'=>$request->device_uuid]);
            return response()->json([
                'status' => (bool)$temp,
                'data' => @$temp->uuid,
                'message' => 'Added Successfully!!'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'Added Unsuccessfully!!'
            ]);
        }
    }

    public function store(Request $request)
    {

        /*
         * {
         *     "notes":"*
         *              T2000,0.00,0.00,
         *              H2000,0.00,0.00,
         *              V1000,444.50,228.50,228.30,228.10,228.60,
         *              I1000,434.70,226.10,225.90,226.70,227.50,
         *              A5000,0,
         *              S255,
         *              P0,0,
         *              #",
         *     "time": "1690477773200"
         * }
         * T = Temperature (C)
         * H = Humidity (%RH)
         * V = Volt (V)
         * I = Current (A)
         * A = Air flow (m/min)
         * P = Power(W)
         **/
        $device_id = 1;
        $unix_at = $request->time;
        $time = (int) $unix_at/1000;
        $time = Carbon::createFromTimestamp($time);
//        $time = Carbon::now();

        $notes = $request->notes;
        if ($request->has('notes') && $request->has('time') && Str::contains($notes,'T')
            && Str::contains($notes,'H') && Str::contains($notes,'V') && Str::contains($notes,'I')
            && Str::contains($notes,'A') && Str::contains($notes,'S') && Str::contains($notes,'P')) {
            Log::info("Start Calling Job");
            dispatch((new StoreDeviceData($notes,$device_id,$time,$unix_at))->onQueue('store_device_data')->delay(Carbon::now()-> addSecond()));
            Log::info("End Calling Job");

            return response()->json([
                'status' => true,
                'data' => [],
                'message' => 'Added Successfully!!'
            ]);

        } else {
            return response()->json([
                    'status' => false,
                    'data' => [],
                    'message' => 'Added Unsuccessfully!!'
            ]);
        }
    }
}
