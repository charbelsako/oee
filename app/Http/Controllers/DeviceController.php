<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Country;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
//        check_user_has_not_permission('role_index');
        $countries = Country::query()->where('parent_id',0)->orderByDesc('name')->get();

        if (\request()->ajax()) {
            $per_page = $request->get('per_page', 10);
            $items = Device::query()->with(['country','city'])->orderBy('id')->paginate($per_page);

            $data['view_render'] = view('cms.devices.partials._table', compact('items'))->render();
            return response(['status' => true, 'data' => $data], 200);
        }
        return view("cms.devices.index",compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $data = $request->only(['name','email','password']);
        if ($request->role == 2) $role = 'Viewer';
        elseif ($request->role == 1) $role = 'Editor';
        else $role = 'Admin';

        $user = User::query()->create($data);
        $success = (bool)$user;
        $message = $user?'user create successfully':'user saved unsuccessfully';
        $user?->assignRole($role);
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
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
