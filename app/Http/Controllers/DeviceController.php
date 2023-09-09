<?php

namespace App\Http\Controllers;

use App\Enums\Constants;
use App\Http\Requests\DeviceRequest;
use App\Models\Country;
use App\Models\Device;
use App\Models\DeviceTemp;
use App\Models\User;
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
        $data = $request->only(['project','machine','process','version','country_id','city_id']);

        $data['timezone'] = Country::find($request->country_id)->timezone;
        $data['uuid'] = generate_new_device_uuid_code('PF','TM');

        $device = Device::query()->create($data);
        if ($device) {
            $temp = DeviceTemp::find($request->device_temp_id);
            $temp->update(['device_id'=>$device->id,'status'=>Constants::DEVICETEMPSTATUS['added']]);
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
    public function show($id)
    {
//        check_user_has_not_permission('device_show');
        $item = Device::find($id);
        return view("cms.devices.show",compact('item'));
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
