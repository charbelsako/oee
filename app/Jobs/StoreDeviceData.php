<?php

namespace App\Jobs;

use App\Models\AirFlow;
use App\Models\ButtonStatus;
use App\Models\Current;
use App\Models\DeviceNote;
use App\Models\Humidity;
use App\Models\Power;
use App\Models\Product;
use App\Models\Temperature;
use App\Models\Volt;
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
        $second_per_pulse = 5000;
        try{
            DB::beginTransaction();
            $pf = 1; // static ثابت حاليا حسب يوم 16/9
            $notes = $this->notes;
            $device_id = $this->device_id;
            $time = $this->time;
            $unix_at = $this->unix_at;
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
