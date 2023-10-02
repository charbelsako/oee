<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request)
    {
//        check_user_has_not_permission('role_index');
        if (\request()->ajax()) {
            $per_page = $request->get('per_page', 10);
            $items = Role::query()->when($request->filled('search'),function ($q) use ($request){
                $search = '%'.$request->search.'%';
                $q->where('name','like',$search);
            })->select('id', 'name')->orderBy('id')->paginate($per_page);

            $data['view_render'] = view('cms.roles.partials._table', compact('items'))->render();
            return response(['status' => true, 'data' => $data], 200);
        }
        return view("cms.roles.index");
    }

    public function create()
    {
//        check_user_has_not_permission('role_create');
        return view('cms.roles.create');
    }

    public function store(Request $request)
    {
//        check_user_has_not_permission('role_create');
        $response = [];
        $errors = [];
        try {
            //Validate inputs
            $inputs = [
                'name' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $inputs);

            if (!$validator->passes()) {
                $errors = $validator->errors();
                throw new \Exception('Validation exception');
            }

            $role = Role::query()->create([
                'name' => $request->name,
                'guard_name' => "web",
            ]);

            $response['success'] = true;
            $response['message'] = 'added successfully!';
            $response['errors'] = [];
        } catch (\Exception $exception) {
            $response['success'] = false;
            $response['message'] = $exception->getMessage();
            $response['errors'] = $errors;
        }
        $response['data'] = [];
        return response()->json($response);
    }

    public function edit($id)
    {
//        check_user_has_not_permission('role_edit');
        $item = Role::query()->select(['id', 'name'])->find($id);
        $response['success'] = (bool)$item;
        $response['message'] = $item?'get data successfully':'unsuccessfully';
        $response['data'] = $item;
        return response()->json($response);
    }

    public function update(Request $request)
    {
//        check_user_has_not_permission('role_edit');

        $id = $request->role_id;
        $response = [];
        $errors = [];
        try {
            //Validate inputs
            $inputs = [
                'name' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $inputs);

            if (!$validator->passes()) {
                $errors = $validator->errors();
                throw new \Exception('Validation exception');
            }

            $item = Role::query()->findOrFail($id);

            $item->update([
                'name' => $request->name
            ]);

            $response['success'] = true;
            $response['message'] = 'update successfully!';
            $response['errors'] = [];
        } catch (\Exception $exception) {
            $response['success'] = false;
            $response['message'] = $exception->getMessage();
            $response['errors'] = $errors;
        }
        $response['data'] = [];
        return response()->json($response);
    }

    public function delete($id)
    {
//        check_user_has_not_permission('role_delete');
        try {
            $item = Role::query()->find($id);
            if (!$item) {
                return redirect()->route('roles.index')->with('Role not found!');
            }
            $item->syncPermissions([]);
            $item->delete();
            $response['success'] = true;
            $response['message'] = 'deleted successfully!';
        } catch (\Exception $exception) {
            $response['success'] = false;
            $response['message'] = $exception->getMessage();
        }
        $response['errors'] = [];
        $response['data'] = [];
        return response()->json($response);
    }

    public function rolePermissions($id)
    {
//        check_user_has_not_permission('permissions_to_role');
        $role = Role::query()->find($id);
        if (!$role) {
            return redirect()->route('roles.index')->with('Role not found!');
        }

        $permissions = Permission::query()->select('id', 'name', 'group', 'label')->get()->groupBy('group');
        $checked_permissions = $role->permissions()->pluck('id')->toArray();

        return view('cms.roles.permissions', compact('role', 'permissions', 'checked_permissions'));
    }

    public function updateRolePermissions(Request $request, $id)
    {
//        check_user_has_not_permission('permissions_to_role');
        $response = [];
        $errors = [];
        try {
            $role = Role::query()->findOrFail($id);
            //Validate inputs
            $inputs = [
                'checked_permissions' => 'nullable',
                'checked_permissions.*' => 'exists:numero_cp_permissions,id',
            ];

            $validator = Validator::make($request->all(), $inputs);

            if (!$validator->passes()) {
                $errors = $validator->errors();
                throw new \Exception('Validation exception');
            }

            if ($role->is_edit) {
                $role->syncPermissions($request->checked_permissions);
            }

            $response['success'] = true;
            $response['message'] = 'update successfully!';
            $response['errors'] = [];
        } catch (\Exception $exception) {
            $response['success'] = false;
            $response['message'] = $exception->getMessage();
            $response['errors'] = $errors;
        }
        $response['data'] = [];
        return response()->json($response);
    }
}
