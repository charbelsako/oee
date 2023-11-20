<?php

namespace App\Http\Controllers\Api;

use App\Enums\Constants;
use App\Http\Controllers\Controller;
use App\Jobs\StoreDeviceData;
use App\Models\ButtonStatus;
use App\Models\Device;
use App\Models\DeviceNote;
use App\Models\DeviceTemp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;
use InfluxDB2\Point;

class DeviceController extends Controller
{
    public function microtime(Request $request)
    {
        return "*" . (int) (microtime(true) * 1000) . "#";
    }

    public function storeDevice(Request $request)
    {
        $device_uuid = $request->device_uuid;
        $mac_address = $request->mac_address;
        if ($request->filled('device_uuid') && $request->filled('mac_address')) {
            $temp = DeviceTemp::query()->where('prefix', $device_uuid)
                ->where('mac_address', $mac_address)->first();
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
            'status' => (bool) $temp,
            'data' => [],
            'message' => $temp ? 'Added Successfully!!' : 'An error occurred while adding device'
        ]);
    }

    public function store(Request $request)
    {
        $device_uuid = $request->header('X-Apikey');
        if (empty($device_uuid)) {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'device unsupported'
            ]);
        }
        $device = Device::query()->where('uuid', $device_uuid)->first();
        if (!$device) {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'device uuid unsupported'
            ]);
        }

        $device_id = $device->id;
        $unix_at = $request->time;
        $time = (int) $unix_at / 1000;
        $time = Carbon::createFromTimestamp($time);

        $notes = $request->notes;
        if (
            $request->has('notes')
            && $request->has('time')
            && Str::contains($notes, 'T')
            && Str::contains($notes, 'H')
            && Str::contains($notes, 'V')
            && Str::contains($notes, 'I')
            && Str::contains($notes, 'A')
            && Str::contains($notes, 'S')
            && Str::contains($notes, 'O')
            && Str::contains($notes, 'N')
        ) {
            try {
                DB::beginTransaction();
                $client = new Client([
                    'url' => env('INFLUXDB_HOST'),
                    'token' => env('INFLUXDB_TOKEN'),
                    'bucket' => env('INFLUXDB_BUCKET'),
                    'org' => env('INFLUXDB_ORG'),
                    'precision' => WritePrecision::S,
                    'verifySSL' => false
                ]);
                $writeApi = $client->createWriteApi();

                $pf = 1; // static ثابت حاليا حسب يوم 16/9
                DeviceNote::query()->create([
                    'notes' => $notes,
                    'registered_at' => $time,
                    'unix_at' => $unix_at,
                ]);

                $notes = str_replace(['*', '#'], '', $notes);

                $status_matches = find_pattern($notes, 'S');

                $s_buttons = $status_matches[0];

                $s_buttons = decbin($s_buttons);

                $start = $s_buttons & 1;
                $pause = ($s_buttons >> 1) & 1;
                $inspection = ($s_buttons >> 2) & 1;
                $breakdown = ($s_buttons >> 3) & 1;

                $temperature_matches = find_pattern($notes, 'T');
                $volt_matches = find_pattern($notes, 'V');
                $humidity_matches = find_pattern($notes, 'H');
                $intensity_matches = find_pattern($notes, 'I');
                $airflow_matches = find_pattern($notes, 'A');
                $ok_matches = find_pattern($notes, 'O');
                $not_ok_matches = find_pattern($notes, 'N');

                $point = Point::measurement('status_buttons');
                $point->addField('start', $start);
                $point->addField('pause', $pause);
                $point->addField('inspection', $inspection);
                $point->addField('breakdown', $breakdown);
                $point->addTag('box_number', $device_uuid);

                $writeApi->write($point);

                $point = Point::measurement('temperature');
                $point->addField('period', $temperature_matches[0]);
                array_shift($temperature_matches);
                $point->addField('celsius', join(',', $temperature_matches));
                $point->addTag('box_number', $device_uuid);
                $point->time(time());

                $writeApi->write($point);

                $point = Point::measurement('humidity');
                $point->addField('period', $humidity_matches[0]);
                array_shift($humidity_matches);
                $point->addField('humidity', join(',', $humidity_matches));
                $point->addTag('box_number', $device_uuid);
                $point->time(time());

                $writeApi->write($point);

                $point = Point::measurement('volts');
                $point->addField('period', $volt_matches[0]);
                array_shift($volt_matches);
                $point->addField('volt', join(',', $volt_matches));
                $point->addTag('box_number', $device_uuid);
                $point->time(time());

                $writeApi->write($point);

                $point = Point::measurement('intensity');
                $point->addField('period', $intensity_matches[0]);
                array_shift($intensity_matches);
                $point->addField('intensity', join(',', $intensity_matches));
                $point->addTag('box_number', $device_uuid);
                $point->time(time());

                $writeApi->write($point);

                $point = Point::measurement('airflow');
                $point->addField('period', $airflow_matches[0]);
                array_shift($airflow_matches);
                $point->addField('airflow', join(',', $airflow_matches));
                $point->addTag('box_number', $device_uuid);
                $point->time(time());

                $writeApi->write($point);

                $point = Point::measurement('ok_products');
                $point->addField('period', '5000');
                $point->addField('ok_products', $ok_matches[0]);
                $point->addTag('box_number', $device_uuid);
                $point->time(time());

                $writeApi->write($point);

                $point = Point::measurement('not_ok_products');
                $point->addField('period', '5000');
                $point->addField('not_ok_products', $ok_matches[0]);
                $point->addTag('box_number', $device_uuid);
                $point->time(time());

                $writeApi->write($point);

                error_log("End Calling Job");



                DB::commit();
                $client->close();

                return response()->json([
                    'status' => true,
                    'data' => [],
                    'message' => 'Added Successfully!!'
                ]);
            } catch (\Exception $exception) {
                DB::rollback();
                error_log('Catch Start');
                error_log($exception);
                error_log('Catch End');
            }
        } else {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'Something went wrong!!'
            ]);
        }
    }
}
