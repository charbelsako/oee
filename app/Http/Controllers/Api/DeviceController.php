<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AirFlow;
use App\Models\ButtonStatus;
use App\Models\DeviceNote;
use App\Models\Humidity;
use App\Models\Temperature;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $notes = $request->notes;
        if ($request->has('notes') && $request->has('time') && Str::contains($notes,'T')
            && Str::contains($notes,'H') && Str::contains($notes,'V') && Str::contains($notes,'I')
            && Str::contains($notes,'A') && Str::contains($notes,'S') && Str::contains($notes,'P')) {
            $device_note = DeviceNote::query()->create([
                'notes'=>$notes,
                'registered_at'=>$time,
                'unix_at'=>$unix_at,
            ]);
            try{
                DB::beginTransaction();
                $notes = str_replace(['*','#'],'',$notes);
                $notes = explode('T',$notes);
                $notes = explode('H',$notes[1]);
                $temperature = $notes[0];
                $temperature = explode(',',$temperature);
                foreach ($temperature as $i_temp=>$temp){
                    if ($i_temp == 0) {
                        continue;
                    }elseif (empty($temp)) {
                        continue;
                    }else{
                        Temperature::query()->create([
                            'device_id'=>$device_id,
                            'time'=>$temperature[0],
                            'value'=>$temp,
                            'registered_at'=>$time,
                            'unix_at'=>$unix_at,
                        ]);
                    }
                }

                $notes = explode('V',$notes[1]);
                $humidity = $notes[0];
                $humidity = explode(',',$humidity);
                foreach ($humidity as $i_hum=>$hum){
                    if ($i_hum == 0) {
                        continue;
                    }elseif (empty($hum)) {
                        continue;
                    }else{
                        Humidity::query()->create([
                            'device_id'=>$device_id,
                            'time'=>$humidity[0],
                            'value'=>$hum,
                            'registered_at'=>$time,
                            'unix_at'=>$unix_at,
                        ]);
                    }
                }
                $notes = explode('I',$notes[1]);
                $volt = $notes[0];
                $volt = explode(',',$volt);
                foreach ($volt as $i_vol=>$hum){
                    if ($i_vol == 0) {
                        continue;
                    }elseif (empty($hum)) {
                        continue;
                    }else{
                        Humidity::query()->create([
                            'device_id'=>$device_id,
                            'time'=>$volt[0],
                            'value'=>$hum,
                            'registered_at'=>$time,
                            'unix_at'=>$unix_at,
                        ]);
                    }
                }
                $notes = explode('A',$notes[1]);
                $current = $notes[0];
                $current = explode(',',$current);
                foreach ($current as $i_cur=>$cur){
                    if ($i_cur == 0) {
                        continue;
                    }elseif (empty($cur)) {
                        continue;
                    }else{
                        Humidity::query()->create([
                            'device_id'=>$device_id,
                            'time'=>$current[0],
                            'value'=>$cur,
                            'registered_at'=>$time,
                            'unix_at'=>$unix_at,
                        ]);
                    }
                }
                $notes = explode('S',$notes[1]);
                $airflow = $notes[0];
                $airflow = explode(',',$airflow);
                foreach ($airflow as $i_air=>$air){
                    if ($i_air == 0) {
                        continue;
                    }elseif (empty($air)) {
                        continue;
                    }else{
                        AirFlow::query()->create([
                            'device_id'=>$device_id,
                            'time'=>$airflow[0],
                            'value'=>$air,
                            'registered_at'=>$time,
                            'unix_at'=>$unix_at,
                        ]);
                    }
                }
                $notes = explode('P',$notes[1]);
                $s_buttons = str_replace(',','',$notes[0]);
                $s_buttons = decbin($s_buttons);
                $btn1 = Str::substr($s_buttons, 7, 1);
                $btn2 = Str::substr($s_buttons, 6, 1);
                $btn3 = Str::substr($s_buttons, 5, 1);
                $btn4 = Str::substr($s_buttons, 4, 1);
                ButtonStatus::query()->create([
                    'device_id'=>$device_id,
                    'btn1'=>$btn1,
                    'btn2'=>$btn2,
                    'btn3'=>$btn3,
                    'btn4'=>$btn4,
                    'registered_at'=>$time,
                    'unix_at'=>$unix_at,
                ]);
                $power = $notes[1];
                $power = explode(',',$power);
                foreach ($power as $i_pow=>$pow){
                    if ($i_pow == 0) {
                        continue;
                    }elseif (empty($pow)) {
                        continue;
                    }else{
                        Humidity::query()->create([
                            'device_id'=>$device_id,
                            'time'=>$power[0],
                            'value'=>$pow,
                            'registered_at'=>$time,
                            'unix_at'=>$unix_at,
                        ]);
                    }
                }
                DB::commit();

                return response()->json([
                    'status' => true,
                    'data' => $device_note,
                    'message' => 'Added Successfully!!'
                ]);
            } catch (\Exception $exception){
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'data' => [],
                    'message' => 'Server Exception!!'
                ]);
            }

        } else {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'Added Unsuccessfully!!'
            ]);
        }
    }
}
