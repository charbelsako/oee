<?php

namespace App\Http\Controllers\Api;

use App\Enums\Constants;
use App\Http\Controllers\Controller;
use App\Jobs\StoreDeviceData;
use App\Models\AirFlow;
use App\Models\ButtonStatus;
use App\Models\Device;
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

    public function deviceStatus(Request $request)
    {
        $mac_address = $request->mac_address;
        if ($request->filled('mac_address')) {
            $temp = DeviceTemp::query()
                ->where('mac_address',$mac_address)->first();
            if (!$temp) {
                return response()->json([
                    'status'  => false,
                    'data'    => [],
                    'message' => 'device not registered!'
                ]);
            }
            if ($temp->uuid && $temp->status == Constants::getIdByName('added')) {
                return response()->json([
                    'status'  => true,
                    'data'    => $temp->uuid,
                    'message' => 'device already registered!'
                ]);
            }
            $device_uuid = explode('-',$temp->device_uuid);
            $first = @$device_uuid[0];
            $second = @$device_uuid[1];
            if ($first && $second) {
                $uuid = generate_new_device_uuid_code($first,$second);
                $temp->update(['uuid'=>$uuid,]);
                return response()->json([
                    'status' => (bool)$temp,
                    'data' => $temp->uuid??[],
                    'message' => 'Added Successfully!!'
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'data' => [],
                    'message' => 'prefix invalid'
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'mac_address is empty'
            ]);
        }
    }

    public function storeDevice(Request $request)
    {
        $device_uuid = $request->device_uuid;
        $mac_address = $request->mac_address;
        if ($request->filled('device_uuid') && $request->filled('mac_address')) {
            $temp = DeviceTemp::query()->where('prefix',$device_uuid)
                ->where('mac_address',$mac_address)->first();
            if ($temp) {
                return response()->json([
                    'status' => true,
                    'data' => $temp->uuid??'',
                    'message' => $temp->uuid?'device already registered and active':'device already registered but inactive'
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'device_uuid or mac_address is empty'
            ]);
        }
        $temp = DeviceTemp::query()->create(['mac_address'=>$mac_address,'prefix'=>$device_uuid]);
        return response()->json([
            'status' => (bool)$temp,
            'data' => [],
            'message' => $temp?'Added Successfully!!':'Added Unsuccessfully!!'
        ]);
    }

    public function store(Request $request)
    {
        $device_uuid = $request->header('X-Apikey');
        if (empty($device_uuid)){
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'device unsupported'
            ]);
        }
        $device = Device::query()->where('uuid',$device_uuid)->first();
        if (!$device) {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'device uuid unsupported'
            ]);
        }

        /*
         * {
         *     "notes":"*
         *              S255,
         *              T2000,0.00,0.00,
         *              H2000,0.00,0.00,
         *              V1000,224.10,223.30,224.10,226.10,222.70,
         *              I1000,222.50,221.50,221.20,223.20,220.30,
         *              A5000,0,
         *              O0,
         *              N0
         *              #",
         *     "time": "1690477773200"
         * }
         * S = Button Status(decimal) - need to convert to binary
         * T = Temperature (C) درجة الحرارة
         * H = Humidity (%RH) الرطوبة
         * V = Volt (V) الفولت
         * I = Current (A) التيار
         * A = Air flow (m/min) تدفق الهواء
         * O = Product ok المنتجات الناجحة
         * N = Product nok المنتجات الفاشلة
         * P(W) = V * I * PF التشغيل
         **/
        $device_id = $device->id;
        $unix_at = $request->time;
        $time = (int) $unix_at/1000;
        $time = Carbon::createFromTimestamp($time);
//        $time = Carbon::now();

        $notes = $request->notes;
        if ($request->has('notes') && $request->has('time') && Str::contains($notes,'T')
            && Str::contains($notes,'H') && Str::contains($notes,'V') && Str::contains($notes,'I')
            && Str::contains($notes,'A') && Str::contains($notes,'S') && Str::contains($notes,'O')
            && Str::contains($notes,'N')) {
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
