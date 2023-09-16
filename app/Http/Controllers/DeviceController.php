<?php

namespace App\Http\Controllers;

use App\Enums\Constants;
use App\Http\Requests\DeviceRequest;
use App\Models\AirFlow;
use App\Models\ButtonStatus;
use App\Models\Country;
use App\Models\Current;
use App\Models\Device;
use App\Models\DeviceTemp;
use App\Models\Humidity;
use App\Models\Power;
use App\Models\Temperature;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
//        check_user_has_not_permission('device_index');
        $countries = Country::query()->where('parent_id',0)->orderByDesc('name')->get();
        $temps = DeviceTemp::query()->where('status',Constants::DEVICETEMPSTATUS['pending'])
            ->whereNull('device_id')->get();

        if (\request()->ajax()) {
            $per_page = $request->get('per_page', 10);
            $items = Device::query()->with(['country','city'])->orderByDesc('id')->paginate($per_page);

            $data['view_render'] = view('cms.devices.partials._table', compact('items'))->render();
            return response(['status' => true, 'data' => $data], 200);
        }
        return view("cms.devices.index",compact('countries','temps'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DeviceRequest $request)
    {
//        check_user_has_not_permission('device_create');
        $device = false;
        $data = $request->only(['project','machine','process','version','country_id','city_id']);
        $temp = DeviceTemp::find($request->device_temp_id);
        if ($temp) {
            $device_uuid = explode('-',$temp->prefix);
            $first = @$device_uuid[0];
            $second = @$device_uuid[1];
            if ($first && $second) {
                $data['timezone'] = Country::find($request->country_id)->timezone;
                $data['uuid'] = generate_new_device_uuid_code($first,$second);
                $device = Device::query()->create($data);
                if ($device) {
                    $temp->update([
                        'device_id'=>$device->id,
                        'status'=>Constants::DEVICETEMPSTATUS['added'],
                        'uuid'=>$device->uuid
                    ]);
                }
            }
        }
        $success = (bool)$device;
        $message = $device?'device create successfully':'device saved unsuccessfully';
        $response['success'] = $success;
        $response['message'] = $message;
        $response['data'] = [];
        return response()->json($response);
    }

    /**
     * get all cities by country id
     */
    public function getCityByCountryId(Request $request)
    {
        $country_id = $request->country_id;
        $response['success'] = true;
        $response['message'] = 'get data successfully';
        $response['data'] = Country::query()->where('parent_id',$country_id)->orderBy('name')->get();
        return response()->json($response);
    }

    /**
     * get all temp device available
     */
    public function getDeviceTempAvailable(Request $request)
    {
        $response['success'] = true;
        $response['message'] = 'get data successfully';
        $response['data'] = DeviceTemp::query()->where('status',Constants::DEVICETEMPSTATUS['pending'])
            ->whereNull('device_id')->get();
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     */
    public function edit($id)
    {
//        check_user_has_not_permission('device_edit');
        $item = Device::find($id);
        $response['success'] = (bool)$item;
        $response['message'] = $item?'get data successfully':'unsuccessfully';
        $response['data'] = $item;
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,$id)
    {
//        check_user_has_not_permission('device_show');
        $item = Device::find($id);

        $plus_milisecond = 0;

        $morning_shift_start = Carbon::parse('6:00')->format('H:i');
        $morning_shift_end = Carbon::parse('14:00')->format('H:i');
        $afternoon_shift_start = Carbon::parse('14:00')->format('H:i');
        $afternoon_shift_end = Carbon::parse('22:00')->format('H:i');
        $night_shift_start = Carbon::parse('22:00')->format('H:i');
        $night_shift_end = Carbon::parse('6:00')->format('H:i');
        $now = Carbon::now()->addHours(11)->format('H:i');
        if ($morning_shift_start <= $now && $now < $morning_shift_end) {
            $shift_start = $morning_shift_start;
            $shift_end = $morning_shift_end;
        } elseif ($afternoon_shift_start <= $now && $now < $afternoon_shift_end) {
            $shift_start = $afternoon_shift_start;
            $shift_end = $afternoon_shift_end;
        } else {
            $shift_start = $night_shift_start;
            $shift_end = $night_shift_end;
            $plus_milisecond = $plus_milisecond + 86400;
        }

        $shift_start = strtotime($shift_start);
        $now = strtotime($now);
        $shift_end = strtotime($shift_end) + $plus_milisecond;

        $airflow = AirFlow::query()->where('device_id',$id)
            ->where('unix_at','>=',$shift_start)
            ->where('unix_at','<=',$now)->get();

        $button_status = ButtonStatus::query()->where('device_id',$id)
            ->where('unix_at','>=',$shift_start)
            ->where('unix_at','<=',$now)->get();

        $current = Current::query()->where('device_id',$id)
            ->where('unix_at','>=',$shift_start)
            ->where('unix_at','<=',$now)->get();

        $humidity = Humidity::query()->where('device_id',$id)
            ->where('unix_at','>=',$shift_start)
            ->where('unix_at','<=',$now)->get();

        $power = Power::query()->where('device_id',$id)
            ->where('unix_at','>=',$shift_start)
            ->where('unix_at','<=',$now)->get();

        $temperature = Temperature::query()->where('device_id',$id)
            ->where('unix_at','>=',$shift_start)
            ->where('unix_at','<=',$now)->get();

        $volt = Temperature::query()->where('device_id',$id)
            ->where('unix_at','>=',$shift_start)
            ->where('unix_at','<=',$now)->get();

        $cycle_time = 8; // add to config (seconds per pules * pieces per pules)
        $time = (double)($now - $shift_start);
        if ($request->filled('shift_work')) {
            $time = 8;
        }
        $shift_duration = Carbon::parse($time)->format('H:i');
        $pause_time = 0;
        $inspection_time = 0;
        $planned_break = $pause_time + $inspection_time;
        $breakdown_time = 0;
        $unplanned_break = $breakdown_time;
        $produced_parts_per_hour = 5; // add to device setting
        $target_production = $produced_parts_per_hour * $time;
        $actual_production = $produced_parts_per_hour * $cycle_time;
        $ok_parts = 5;
        $nok_parts = 5;
        $total_parts = $ok_parts + $nok_parts;
        $total_break = $planned_break + $unplanned_break;
        $quality = $ok_parts / $total_parts;
        $availability = ($time - $total_break) / $time;
        $possible_production = ($time - $total_break) / $cycle_time;
        $performance = $total_parts / $possible_production;
        $oee = $availability + $performance + $quality;

        $data['item'] = $item;
        $data['ok_parts'] = $ok_parts;
        $data['nok_parts'] = $nok_parts;
        $data['actual_production'] = $actual_production;
        $data['possible_production'] = $possible_production;
        $data['target_production'] = $target_production;
        $data['unplanned_break'] = $unplanned_break;
        $data['planned_break'] = $planned_break;
        $data['shift_duration'] = $shift_duration;
        $data['quality'] = round($quality,2);
        $data['performance'] = round($performance,2);
        $data['availability'] = round($availability,2);
        $data['oee'] = round($oee,2);

        return view("cms.devices.show",$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DeviceRequest $request)
    {
//        check_user_has_not_permission('device_edit');
        $device = Device::find($request->device_id);
        $data = $request->only(['project','machine','process','version','country_id','city_id']);

        if ($request->filled('country_id')) {
            $data['timezone'] = Country::find($request->country_id)->timezone;
        }

        $device = $device->update($data);
        $success = (bool)$device;
        $message = $device?'device create successfully':'device saved unsuccessfully';
        $response['success'] = $success;
        $response['message'] = $message;
        $response['data'] = [];
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
//        check_user_has_not_permission('device_delete');
        $item = Device::find($id);
        $item = $item?->delete();
        $success = (bool)$item;
        $message = $item?'device deleted successfully':'device delete unsuccessfully';
        $response['success'] = $success;
        $response['message'] = $message;
        $response['data'] = [];
        return response()->json($response);
    }
}
