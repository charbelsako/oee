<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Device;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DummySeeder extends Seeder
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

//        Device::query()->truncate();

//         \DB::statement("SET FOREIGN_KEY_CHECKS = 1;");

        // create country
        $country = Country::query()->firstOrCreate(['name' => 'Country'],
            ['parent_id' => 0, 'flag' => '', 'timezone' => '+3', 'status' => 1]);

        // create city
        $city = Country::query()->firstOrCreate(['name' => 'City'],
            ['parent_id' => $country->id, 'flag' => '', 'timezone' => '+3', 'status' => 1]);

        // create device
        $device = Device::query()->firstOrCreate(
            ['project' => 'OEE','machine' => 'OEE','process' => 'OEE'],
            ['country_id' => $country->id, 'city_id' => $city->id, 'timezone' => $city->timezone, 'version' => '1.0', 'status' => 1]);
        // cache clear
        Artisan::call('cache:clear');
    }
}
