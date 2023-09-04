<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // truncate tables
//         \DB::statement("SET FOREIGN_KEY_CHECKS = 0;");

//        \DB::table('model_has_permissions')->truncate();
//        \DB::table('model_has_roles')->truncate();
//        \DB::table('role_has_permissions')->truncate();
//        Role::query()->truncate();
//        Permission::query()->truncate();

//         \DB::statement("SET FOREIGN_KEY_CHECKS = 1;");

        $permissions_array = [
            // Role
            [
                'name' => 'role_index',
                'label' => 'View all roles',
                'group' => 'Role',
                'roles' => ['super', 'admin']
            ],
            [
                'name' => 'role_create',
                'label' => 'Create new role',
                'group' => 'Role',
                'roles' => ['super', 'admin']
            ],
            [
                'name' => 'role_edit',
                'label' => 'edit role',
                'group' => 'Role',
                'roles' => ['super', 'admin']
            ],
            [
                'name' => 'role_delete',
                'label' => 'Delete role',
                'group' => 'Role',
                'roles' => ['super', 'admin']
            ],
            [
                'name' => 'add_permissions_to_role',
                'label' => 'Add permissions to role',
                'group' => 'Role',
                'roles' => ['super', 'admin', 'editor', 'viewer']
            ],
            // User
            [
                'name' => 'user_index',
                'label' => 'View all users',
                'group' => 'User',
                'roles' => ['super', 'admin']
            ],
            [
                'name' => 'user_create',
                'label' => 'Create new user',
                'group' => 'User',
                'roles' => ['super', 'admin']
            ],
            [
                'name' => 'user_edit',
                'label' => 'edit user',
                'group' => 'User',
                'roles' => ['super', 'admin']
            ],
            [
                'name' => 'user_delete',
                'label' => 'Delete user',
                'group' => 'User',
                'roles' => ['super', 'admin']
            ],
            [
                'name' => 'user_status',
                'label' => 'Change status',
                'group' => 'User',
                'roles' => ['super', 'admin', 'editor', 'viewer']
            ],
            // Device
            [
                'name' => 'device_index',
                'label' => 'View all devices',
                'group' => 'Device',
                'roles' => ['super', 'admin']
            ],
            [
                'name' => 'device_create',
                'label' => 'Create new device',
                'group' => 'Device',
                'roles' => ['super', 'admin']
            ],
            [
                'name' => 'device_edit',
                'label' => 'Edit device',
                'group' => 'Device',
                'roles' => ['super', 'admin']
            ],
            [
                'name' => 'device_delete',
                'label' => 'Delete device',
                'group' => 'Device',
                'roles' => ['super', 'admin']
            ],
            [
                'name' => 'device_status',
                'label' => 'Change status',
                'group' => 'Device',
                'roles' => ['super', 'admin', 'editor', 'viewer']
            ],

        ];

        $admin_permissions = collect($permissions_array)->filter(function ($p) {
            return isset($p['roles']) && in_array('admin', $p['roles']);
        });
        $editor_permissions = collect($permissions_array)->filter(function ($p) {
            return isset($p['roles']) && in_array('editor', $p['roles']);
        });
        $viewer_permissions = collect($permissions_array)->filter(function ($p) {
            return isset($p['roles']) && in_array('viewer', $p['roles']);
        });

        // create roles
        $super_role = Role::query()->firstOrCreate(['name' => 'Super Admin'], [
            'guard_name' => 'web', 'is_edit' => 0]);
        $admin_role = Role::query()->firstOrCreate(['name' => 'Admin'], [
            'guard_name' => 'web']);
        $editor_role = Role::query()->firstOrCreate(['name' => 'Editor'], [
            'guard_name' => 'web']);
        $viewer_role = Role::query()->firstOrCreate(['name' => 'Viewer'], [
            'guard_name' => 'web']);

        // create permissions
        $permissions_names_array = [];
        foreach ($permissions_array as $item) {
            Permission::query()->firstOrCreate([
                'name' => $item['name']
            ], [
                'label' => $item['label'],
                'group' => $item['group'],
                'guard_name' => 'web'
            ]);
            $permissions_names_array[] = $item['name'];
        }

        // add permissions to roles
        // super admin
        $super_role->syncPermissions($permissions_names_array);

        // admin
        $admin_permissions_names_array = [];
        foreach ($admin_permissions as $item) {
            $admin_permissions_names_array[] = $item['name'];
        }
        $admin_role->syncPermissions($admin_permissions_names_array);

        // editor
        $editor_permissions_names_array = [];
        foreach ($editor_permissions as $item) {
            $editor_permissions_names_array[] = $item['name'];
        }
        $editor_role->syncPermissions($editor_permissions_names_array);

        // viewer
        $viewer_permissions_names_array = [];
        foreach ($viewer_permissions as $item) {
            $viewer_permissions_names_array[] = $item['name'];
        }
        $viewer_role->syncPermissions($viewer_permissions_names_array);

        // assign role to user
        $users_array = [
            // Users
            [
                'name' => 'Super Admin',
                'email' => 'super@oee.com',
                'password' => Hash::make('super@000'),
                'status' => 101,
                'is_editable' => 152,
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@oee.com',
                'password' => Hash::make('admin@1234'),
                'is_active' => 101,
            ],
            [
                'name' => 'Editor',
                'email' => 'editor@oee.com',
                'password' => Hash::make('editor@99'),
                'is_active' => 101,
            ],
            [
                'name' => 'Viewer',
                'email' => 'viewer@oee.com',
                'password' => Hash::make('viewer@88'),
                'is_active' => 101,
            ],
        ];

        foreach ($users_array as $u) {
            User::query()->updateOrCreate(['email'=>$u['email']],$u);
        }
        $users = User::query()->get();
        $super_ids = [1];
        $admin_ids = [2];
        $editor_ids = [3];
        foreach ($users as $user) {
            if (in_array($user->id, $super_ids)) {
                $user->assignRole('Super Admin');
            } elseif (in_array($user->id, $admin_ids)) {
                $user->assignRole('Admin');
            } elseif (in_array($user->id, $editor_ids)) {
                $user->assignRole('Editor');
            } else {
                $user->assignRole('Viewer');
            }
        }

        // cache clear
        Artisan::call('cache:clear');
    }
}
