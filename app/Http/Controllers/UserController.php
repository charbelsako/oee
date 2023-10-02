<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use function Clue\StreamFilter\fun;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
//        check_user_has_not_permission('role_index');
        if (\request()->ajax()) {
            $per_page = $request->get('per_page', 10);
            $items = User::query()
                ->when($request->filled('search'),function ($q) use ($request){
                    $search = '%'.$request->search.'%';
                    $q->where('name','like',$search)->orWhere('email',$search);
                })->with('roles')->orderBy('id')->paginate($per_page);

            $data['view_render'] = view('cms.users.partials._table', compact('items'))->render();
            return response(['status' => true, 'data' => $data], 200);
        }
        return view("cms.users.index");
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
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //        check_user_has_not_permission('device_edit');
        $item = User::find($id);
        $response['success'] = (bool)$item;
        $response['message'] = $item?'get data successfully':'unsuccessfully';
        $response['data'] = $item;
        $response['role'] = $item?->roles()->first();
        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //        check_user_has_not_permission('user_edit');
        $user = User::find($request->user_id);
        $data = $request->only(['name','email']);

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }
        if ($request->filled('role_id')) {
            if ($request->role_id == 2) $role = 'Viewer';
            elseif ($request->role_id == 1) $role = 'Editor';
            else $role = 'Admin';
            $user?->syncRoles($role);
        }


        $user = $user->update($data);
        $success = (bool)$user;
        $message = $user?'user update successfully':'user update unsuccessfully';
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
        $item = User::find($id);
        $item = $item?->delete();
        $success = (bool)$item;
        $message = $item?'user deleted successfully':'user delete unsuccessfully';
        $response['success'] = $success;
        $response['message'] = $message;
        $response['data'] = [];
        return response()->json($response);
    }

    public function profile()
    {
        return view('cms.users.profile');
    }

    public function profileUpdate(Request $request)
    {
        $user = auth()->user();
        $has_changes = false;

        if ($request->filled('name')) {
            $data['name'] = $request->name;
            $has_changes = true;
        }

        if ($request->filled('email')) {
            $data['email'] = $request->email;
            $has_changes = true;
        }

        if ($request->filled('password')) {
            $data['password'] = $request->password;
            $has_changes = true;
        }

        if (!$has_changes) {
            $response['success'] = false;
            $response['message'] = 'No changes!!';
            $response['data'] = [];
            return response()->json($response);
        }

        $is_update = $user->update($data);
        $success = (bool)$is_update;
        $message = $is_update?'user update successfully':'user update unsuccessfully';
        $response['success'] = $success;
        $response['message'] = $message;
        $response['data'] = [];
        return response()->json($response);
    }
}
