<?php

namespace App\Jobs;

use App\Models\AirFlow;
use App\Models\ButtonStatus;
use App\Models\DeviceNote;
use App\Models\Humidity;
use App\Models\Temperature;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class StoreDeviceData implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    var $notes;
    var $device_id;
    var $time;
    var $unix_at;

    public $tries = 5;
    public $timeout = 120000;
    public $failOnTimeout = true;
    public $backoff = [60,120,300,900,1800];

    public function __construct($notes,$device_id,$time,$unix_at)
    {
        $this->notes = $notes;
        $this->device_id = $device_id;
        $this->time = $time;
        $this->unix_at = $unix_at;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            DB::beginTransaction();
            $notes = $this->notes;
            $device_id = $this->device_id;
            $time = $this->time;
            $unix_at = $this->unix_at;
            $device_note = DeviceNote::query()->create([
                'notes'=>$notes,
                'registered_at'=>$time,
                'unix_at'=>$unix_at,
            ]);
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
            Log::info('Added Successfully!');
        } catch (\Exception $exception){
            DB::rollback();
            Log::info('Catch Start');
            Log::info($exception);
            Log::info('Catch End');
        }
    }

    public function failed($e)
    {
        Log::info('Failed Start');
        Log::info($e);
        Log::info('Failed End');
    }
}
