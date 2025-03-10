<?php

namespace App\Http\Controllers;

use App\Enums\Constants;
use App\Http\Requests\DeviceRequest;
use App\Models\ButtonStatus;
use App\Models\Country;
use App\Models\Device;
use App\Models\DeviceTemp;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\InfluxDBClientService;
use Illuminate\Support\Facades\Log;

class DeviceController extends Controller
{
    private $data = null;
    protected $influxDBClientService;
    public function __construct(InfluxDBClientService $influxDBClientService)
    {
        $this->influxDBClientService = $influxDBClientService;
    }

    public function getJson(Request $request)
    {
        $this->data['shift_work'] = $request->shift_work;
        $this->data['shift_year'] = $request->shift_year;
        $this->data['shift_month'] = $request->shift_month;
        $this->data['shift_day'] = $request->shift_day;
        $fun = $request->f;

        return $this->{$fun}();
    }

    public function oee_chart()
    {
        $data = $this->data;



        $this->data['series_data'] = [['name' => 'Data Packages', 'data' => $data]];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //        check_user_has_not_permission('device_index');
        $countries = Country::query()->where('parent_id', 0)->orderByDesc('name')->get();
        $temps = DeviceTemp::query()->where('status', Constants::DEVICETEMPSTATUS['pending'])
            ->whereNull('device_id')->get();

        if (\request()->ajax()) {
            $per_page = $request->get('per_page', 10);
            $items = Device::query()->when($request->filled('search'), function ($q) use ($request) {
                $search = '%' . $request->search . '%';
                $q->where('project', 'like', $search)->orWhere('machine', $search)->orWhere('process', $search);
            })->with(['country', 'city'])->orderByDesc('id')->paginate($per_page);

            $data['view_render'] = view('cms.devices.partials._table', compact('items'))->render();
            return response(['status' => true, 'data' => $data], 200);
        }
        return view("cms.devices.index", compact('countries', 'temps'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DeviceRequest $request)
    {
        //        check_user_has_not_permission('device_create');
        $device = false;
        $data = $request->only(['project', 'machine', 'process', 'version', 'country_id', 'city_id',
            'plus_millisecond', 'produced_parts_per_hour', 'second_per_pulse', 'pieces_per_pulse']);
        $temp = DeviceTemp::find($request->device_temp_id);
        if ($temp) {
            $device_uuid = explode('-', $temp->prefix);
            $first = @$device_uuid[0];
            $second = @$device_uuid[1];
            if ($first && $second) {
                $data['timezone'] = Country::find($request->country_id)->timezone;
                $data['uuid'] = generate_new_device_uuid_code($first, $second);
                $device = Device::query()->create($data);
                if ($device) {
                    $temp->update([
                        'device_id' => $device->id,
                        'status' => Constants::DEVICETEMPSTATUS['added'],
                        'uuid' => $device->uuid
                    ]);
                }
            }
        }
        $success = (bool) $device;
        $message = $device ? 'device create successfully' : 'device saved unsuccessfully';
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
        $response['data'] = Country::query()->where('parent_id', $country_id)->orderBy('name')->get();
        return response()->json($response);
    }

    /**
     * get all temp device available
     */
    public function getDeviceTempAvailable(Request $request)
    {
        $response['success'] = true;
        $response['message'] = 'get data successfully';
        $response['data'] = DeviceTemp::query()->where('status', Constants::DEVICETEMPSTATUS['pending'])
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
        $response['success'] = (bool) $item;
        $response['message'] = $item ? 'get data successfully' : 'unsuccessfully';
        $response['data'] = $item;
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {
            //        check_user_has_not_permission('device_show');
            $device = Device::find($id);

            $is_live = true;

            $plus_millisecond = $device->plus_millisecond; // add to device setting
            $produced_parts_per_hour = $device->produced_parts_per_hour; // add to device setting
            $second_per_pulse = $device->second_per_pulse; // add to device setting
            $pieces_per_pulse = $device->pieces_per_pulse; // add to device setting

            $morning_shift_start = Carbon::parse('6:00')->format('H:i');
            $morning_shift_end = Carbon::parse('14:00')->format('H:i');
            $afternoon_shift_start = Carbon::parse('14:00')->format('H:i');
            $afternoon_shift_end = Carbon::parse('22:00')->format('H:i');
            $night_shift_start = Carbon::parse('22:00')->format('H:i');
            $night_shift_end = Carbon::parse('6:00')->format('H:i');

            $is_night_shift = false;
            $now = Carbon::now()->tz('UTC')->addHours($device->timezone)->format('H:i');
            if ($morning_shift_start <= $now && $now <= $morning_shift_end) {
                Log::info("morning shift");
                $shift_start = $morning_shift_start;
                $shift_end = $morning_shift_end;
            } elseif ($afternoon_shift_start <= $now && $now < $afternoon_shift_end) {
                $shift_start = $afternoon_shift_start;
                $shift_end = $afternoon_shift_end;
            } else {
                $shift_start = $night_shift_start;
                $shift_end = $night_shift_end;
                $is_night_shift = true;
                // @NOTE: WHY IS THIS HERE, this should add night shift to the next day
                $plus_millisecond = $plus_millisecond + 86400;
            }

            $cycle_time = $second_per_pulse * $pieces_per_pulse; // add to config (seconds per pulse * pieces per pulse)
            // if ($request->filled('shift_work')) {
            //     $shift_work = $request->shift_work;
            //     $shift_year = $request->shift_year;
            //     $shift_month = $request->shift_month;
            //     $shift_day = $request->shift_day;
            //     if ($shift_work == 1) {
            //         $shift_date_start = $shift_year . '-' . $shift_month . '-' . $shift_day . ' 06:00';
            //         $shift_date_end = $shift_year . '-' . $shift_month . '-' . $shift_day . ' 14:00';
            //         $shift_start = Carbon::parse($shift_date_start)->format('Y-m-d H:i');
            //         $shift_end = Carbon::parse($shift_date_end)->format('Y-m-d H:i');
            //     } elseif ($shift_work == 2) {
            //         $shift_date_start = $shift_year . '-' . $shift_month . '-' . $shift_day . ' 14:00';
            //         $shift_date_end = $shift_year . '-' . $shift_month . '-' . $shift_day . ' 22:00';
            //         $shift_start = Carbon::parse($shift_date_start)->format('Y-m-d H:i');
            //         $shift_end = Carbon::parse($shift_date_end)->format('Y-m-d H:i');
            //     } else {
            //         $shift_date_start = $shift_year . '-' . $shift_month . '-' . $shift_day . ' 22:00';
            //         $shift_date_end = $shift_year . '-' . $shift_month . '-' . $shift_day . ' 06:00';
            //         $shift_start = Carbon::parse($shift_date_start)->format('Y-m-d H:i');
            //         $shift_end = Carbon::parse($shift_date_end)->format('Y-m-d H:i');
            //         $plus_millisecond = $plus_millisecond + 86400;
            //     }
            // }

            $shift_start = strtotime($shift_start);
            $shift_start_ms = intval($shift_start) * 1000;
            $now = strtotime($now);
            if ($is_night_shift) {
                $shift_end = strtotime($shift_end) + $plus_millisecond;
            } else {
                $shift_end = strtotime($shift_end);
            }
            $shift_end_ms = intval($shift_end) * 1000;

            $carbon_start = Carbon::createFromTimestampMs($shift_start_ms, 'UTC');
            $carbon_end = Carbon::createFromTimestampMs($shift_end_ms, 'UTC');
            Log::info('Start ' . $carbon_start);
            Log::info('End ' . $carbon_end);
            $shift_date_start = $carbon_start->format('Y-m-d\TH:i:s.u\Z');
            $shift_date_end = $carbon_end->format('Y-m-d\TH:i:s.u\Z');

            $time = (double) ($shift_end - $shift_start);
            Log::info('time is ' . $time);

            if (!$request->filled('shift_work')) {
                $shift_end = $now;
                $is_live = false;
            }

            Log::info($shift_date_end);
            Log::info($shift_date_start);
            $query = "from(bucket: \"oee_test\")
            |> range(start: $shift_date_start, stop: $shift_date_end)
            |> filter(fn: (r) => r._measurement == \"ok_products\" and r.box_number == \"$device->uuid\")";
            $ok_parts_data = $this->influxDBClientService->queryData($query);

            $ok_parts = 0;
            foreach ($ok_parts_data as $timestamp => $values) {
                if (isset($values['ok_products'])) {
                    $ok_parts += $values['ok_products'];
                }
            }

            $query = "from(bucket: \"oee_test\") |> range(start: $shift_date_start, stop: $shift_date_end) |> filter(fn: (r) => r._measurement == \"not_ok_products\" and r.box_number == \"$device->uuid\")";
            $nok_parts_data = $this->influxDBClientService->queryData($query);

            $nok_parts = 0;
            foreach ($nok_parts_data as $timestamp => $values) {
                if (isset($values['not_ok_products'])) {
                    $nok_parts += $values['not_ok_products'];
                }
            }

            $query = "from(bucket: \"oee_test\")
            |> range(start: $shift_date_start, stop: $shift_date_end)
            |> filter(fn: (r) => r._measurement == \"status_buttons\" and r.box_number == \"$device->uuid\")";
            $button_data = $this->influxDBClientService->queryData($query);


            $pause_time_count = 0;
            $inspection_time_count = 0;
            $breakdown_time_count = 0;
            foreach ($button_data as $timestamp => $values) {
                $inspection_time_count += $values['inspection'];
                $pause_time_count += $values['pause'];
                $breakdown_time_count += $values['breakdown'];
            }

            $pause_time = $pause_time_count * $second_per_pulse;
            $inspection_time = $inspection_time_count * $second_per_pulse;
            $breakdown_time = $breakdown_time_count * $second_per_pulse;

            $shift_duration = Carbon::parse($time)->format('H:i');
            $planned_break = $pause_time + $inspection_time; // done(produced time + slow production)
            $unplanned_break = $breakdown_time; // done(produced time + slow production)
            $target_production = (int) ($produced_parts_per_hour * ($time / 3600));
            $total_parts = $ok_parts + $nok_parts;
            $actual_production = $total_parts;
            $total_break = $planned_break + $unplanned_break;
            $quality = ($total_parts != 0 && $ok_parts != 0) ? $ok_parts / $total_parts : 0;
            $availability = $time != 0 ? ($time - $total_break) / $time : 0;
            // @TODO this is wrong I think
            $possible_production = (int) ($cycle_time != 0 ? ($time - $total_break) / $cycle_time : 0);
            Log::info('possible production ' . $possible_production);
            Log::info('Total Break ' . $total_break);
            Log::info('cycle time ' . $cycle_time);
            $performance = $possible_production != 0 ? $total_parts / $possible_production : 0;
            $oee = $availability * $performance * $quality;

            $data['item'] = $device;
            $data['ok_parts'] = $ok_parts;
            $data['nok_parts'] = $nok_parts;
            $data['actual_production'] = $actual_production;
            $data['possible_production'] = $possible_production;
            $data['target_production'] = $target_production;
            $data['unplanned_break'] = $unplanned_break;
            $data['planned_break'] = $planned_break;
            $data['shift_duration'] = $shift_duration;
            $data['quality'] = round($quality * 100, 2);
            $data['performance'] = round($performance * 100, 2);
            $data['availability'] = round($availability * 100, 2);
            $data['oee'] = round($oee * 100, 2);
            $data['no_data'] = false;
            $data['is_live'] = $is_live;

            return view("cms.devices.show", $data);
        } catch (\Exception $err) {
            Log::error($err);
            $data['item'] = $device;
            $data['ok_parts'] = 0;
            $data['nok_parts'] = 0;
            $data['actual_production'] = 0;
            $data['possible_production'] = 0;
            $data['target_production'] = 0;
            $data['unplanned_break'] = 0;
            $data['planned_break'] = 0;
            $data['shift_duration'] = 0;
            $data['quality'] = 0;
            $data['performance'] = 0;
            $data['availability'] = 0;
            $data['oee'] = 0;
            $data['is_live'] = true;
            $data['no_data'] = true;
            return view("cms.devices.show", $data);
        }
    }

    public function showgraph(Request $request, $id)
    {
        $device = Device::find($id);
        $data["item"] = $device;
        // @TODO: no data returned right now
        return view("cms.devices.showgraph", $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DeviceRequest $request)
    {
        //        check_user_has_not_permission('device_edit');
        $device = Device::find($request->device_id);
        $data = $request->only(['project', 'machine', 'process', 'version', 'country_id', 'city_id',
            'plus_millisecond', 'produced_parts_per_hour', 'second_per_pulse', 'pieces_per_pulse']);

        if ($request->filled('country_id')) {
            $data['timezone'] = Country::find($request->country_id)->timezone;
        }

        $device = $device->update($data);
        $success = (bool) $device;
        $message = $device ? 'device create successfully' : 'device saved unsuccessfully';
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
        $success = (bool) $item;
        $message = $item ? 'device deleted successfully' : 'device delete unsuccessfully';
        $response['success'] = $success;
        $response['message'] = $message;
        $response['data'] = [];
        return response()->json($response);
    }
}
