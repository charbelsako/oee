<?php

namespace App\Http\Controllers\Api;

use App\Enums\Constants;
use App\Http\Controllers\Controller;
use App\Jobs\StoreDeviceData;
// @NOTE: This will be changed to influx
// use App\Models\AirFlow;
// use App\Models\Temperature;
// use App\Models\Humidity;
use App\Models\ButtonStatus;
use App\Models\Device;
use App\Models\DeviceNote;
use App\Models\DeviceTemp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;
use InfluxDB2\Point;

class DeviceController extends Controller
{
    public function microtime(Request $request)
    {
        return "*" . (int) (microtime(true) * 1000) . "#";

        // @NOTE: this code is never reached
        $data['device_time'] = '*'.strtotime(Carbon::now()).'#';
        $data['device_time_milli'] = '*'.now()->getTimestampMs().'#';
        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'device config'
        ]);
    }

    // @TODO: this may not be needed
    public function deviceStatus(Request $request)
    {
        $mac_address = $request->mac_address;
        if ($request->filled('mac_address')) {
            $temp = DeviceTemp::query()->where('mac_address',$mac_address)->first();
            return response()->json(['data'=>$temp]);
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
                    'data' => $temp->uuid ?? '',
                    'message' => $temp->uuid ? 'device already registered and active' : 'device already registered but inactive'
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'device_uuid or mac_address is empty'
            ]);
        }
        $temp = DeviceTemp::query()->create(['mac_address' => $mac_address, 'prefix' => $device_uuid]);
        return response()->json([
            'status' => (bool)$temp,
            'data' => [],
            'message' => $temp ? 'Added Successfully!!' : 'An error occurred while adding device'
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
    //    $time = Carbon::now();

        $notes = $request->notes;
        if ($request->has('notes') && $request->has('time') && Str::contains($notes,'T')
            && Str::contains($notes,'H') && Str::contains($notes,'V') && Str::contains($notes,'I')
            && Str::contains($notes,'A') && Str::contains($notes,'S') && Str::contains($notes,'O')
            && Str::contains($notes,'N')) {
            error_log("Start Calling Job");
            try{
                // @TODO: start transaction
                $pf = 1; // static ثابت حاليا حسب يوم 16/9
                DeviceNote::query()->create([
                    'notes'=>$notes,
                    'registered_at'=>$time,
                    'unix_at'=>$unix_at,
                ]);

                $notes = str_replace(['*','#'],'',$notes);
                $notes = explode('S',$notes);
                $notes = explode('T',$notes[1]);
                $s_buttons = str_replace(',','',$notes[0]);
                $s_buttons = decbin($s_buttons);
                $start = Str::substr($s_buttons, 7, 1);
                $pause = Str::substr($s_buttons, 6, 1);
                $inspection = Str::substr($s_buttons, 5, 1);
                $breakdown = Str::substr($s_buttons, 4, 1);

                $notes = explode('H',$notes[1]);
                $temperature = $notes[0];
                $temperature = explode(',',$temperature);

                $notes = explode('V',$notes[1]);
                $humidity = $notes[0];
                $humidity = explode(',',$humidity);

                $notes = explode('I',$notes[1]);
                $volt = $notes[0];
                $volt = explode(',',$volt);

                $notes = explode('A',$notes[1]);
                $current = $notes[0];
                $current = explode(',',$current);

                $notes = explode('O',$notes[1]);
                $airflow = $notes[0];
                $airflow = explode(',',$airflow);

                $notes = explode('N',$notes[1]);
                $product_ok = $notes[0];
                $product_ok = explode(',',$product_ok);
                foreach ($product_ok as $ok){
                    if ($ok > 0 && $pause == 0){
                        $start = 0; // 0 => on
                        $pause = 1; // 1 => off
                        break;
                    }
                }

            $product_nok = $notes[1];
            $product_nok = explode(',',$product_nok);
            response()->json(['data'=>$product_nok]);
            foreach ($product_nok as $nok){
                if ($nok > 0 && $pause == 0){
                    $start = 0; // 0 => on
                    $pause = 1; // 1 => off
                    break;
                }
            }
            ButtonStatus::query()->create([
                'device_id'=>$device_id,
                'start'=>$start,
                'pause'=>$pause,
                'inspection'=>$inspection,
                'breakdown'=>$breakdown,
                'registered_at'=>$time,
                'unix_at'=>$unix_at,
            ]);
            foreach ($temperature as $temp){
                Temperature::query()->create([
                    'device_id'=>$device_id,
                    'time'=>$temperature[0],
                    'value'=>$temp,
                    'start'=>$start,
                    'pause'=>$pause,
                    'inspection'=>$inspection,
                    'breakdown'=>$breakdown,
                    'registered_at'=>$time,
                    'unix_at'=>$unix_at,
                ]);
            }
            foreach ($humidity as $hum){
                Humidity::query()->create([
                    'device_id'=>$device_id,
                    'time'=>$humidity[0],
                    'value'=>$hum,
                    'start'=>$start,
                    'pause'=>$pause,
                    'inspection'=>$inspection,
                    'breakdown'=>$breakdown,
                    'registered_at'=>$time,
                    'unix_at'=>$unix_at,
                ]);
            }
            foreach ($volt as $vol){
                Volt::query()->create([
                    'device_id'=>$device_id,
                    'time'=>$volt[0],
                    'value'=>$vol,
                    'start'=>$start,
                    'pause'=>$pause,
                    'inspection'=>$inspection,
                    'breakdown'=>$breakdown,
                    'registered_at'=>$time,
                    'unix_at'=>$unix_at,
                ]);
            }
            foreach ($current as $i_cur=>$cur){
                Current::query()->create([
                    'device_id'=>$device_id,
                    'time'=>$current[0],
                    'value'=>$cur,
                    'start'=>$start,
                    'pause'=>$pause,
                    'inspection'=>$inspection,
                    'breakdown'=>$breakdown,
                    'registered_at'=>$time,
                    'unix_at'=>$unix_at,
                ]);
                $vol_n = $volt[$i_cur];
                $power_value = $pf * $cur * $vol_n;
                Power::query()->create([
                    'device_id'=>$device_id,
                    'time'=>$current[0],
                    'value'=>$power_value,
                    'start'=>$start,
                    'pause'=>$pause,
                    'inspection'=>$inspection,
                    'breakdown'=>$breakdown,
                    'registered_at'=>$time,
                    'unix_at'=>$unix_at,
                ]);
            }
            foreach ($airflow as $air){
                AirFlow::query()->create([
                    'device_id'=>$device_id,
                    'time'=>$airflow[0],
                    'value'=>$air,
                    'start'=>$start,
                    'pause'=>$pause,
                    'inspection'=>$inspection,
                    'breakdown'=>$breakdown,
                    'registered_at'=>$time,
                    'unix_at'=>$unix_at,
                ]);
            }
            foreach ($product_ok as $ok){
                Product::query()->create([
                    'device_id'=>$device_id,
                    'is_ok'=>1,
                    'time'=>$second_per_pulse,
                    'value'=>$ok,
                    'start'=>$start,
                    'pause'=>$pause,
                    'inspection'=>$inspection,
                    'breakdown'=>$breakdown,
                    'registered_at'=>$time,
                    'unix_at'=>$unix_at,
                ]);
            }
            foreach ($product_nok as $nok){
                Product::query()->create([
                    'device_id'=>$device_id,
                    'is_ok'=>0,
                    'time'=>$second_per_pulse,
                    'value'=>$nok,
                    'start'=>$start,
                    'pause'=>$pause,
                    'inspection'=>$inspection,
                    'breakdown'=>$breakdown,
                    'registered_at'=>$time,
                    'unix_at'=>$unix_at,
                ]);
            }

            // @TODO: commit data
            error_log('Added Successfully!');
        } catch (\Exception $exception){
            // @TODO rollback??
            error_log('Catch Start');
            error_log($exception);
            error_log('Catch End');
        }

        // dispatch((new StoreDeviceData($notes,$device_id,$time,$unix_at))->onQueue('store_device_data')->delay(Carbon::now()-> addSecond()));
        error_log("End Calling Job");

            return response()->json([
                'status' => true,
                'data' => [],
                'message' => 'Added Successfully!!'
            ]);

        } else {
            return response()->json([
                    'status' => false,
                    'data' => [],
                    'message' => 'Something went wrong!!'
            ]);
        }
    }
}
