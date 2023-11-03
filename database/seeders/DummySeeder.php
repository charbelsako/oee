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
        $country1 = Country::query()->firstOrCreate(['name' => 'Country1'],
            ['parent_id' => 0, 'flag' => '', 'timezone' => '+3', 'status' => 101]);
        $country2 = Country::query()->firstOrCreate(['name' => 'Country2'],
            ['parent_id' => 0, 'flag' => '', 'timezone' => '+3', 'status' => 101]);

        // create city
        $city = Country::query()->firstOrCreate(['name' => 'City1'],
            ['parent_id' => $country1->id, 'flag' => '', 'timezone' => '+3', 'status' => 101]);
        Country::query()->firstOrCreate(['name' => 'City2'],
            ['parent_id' => $country1->id, 'flag' => '', 'timezone' => '+3', 'status' => 101]);
        Country::query()->firstOrCreate(['name' => 'City3'],
            ['parent_id' => $country2->id, 'flag' => '', 'timezone' => '+3', 'status' => 101]);
        Country::query()->firstOrCreate(['name' => 'City4'],
            ['parent_id' => $country2->id, 'flag' => '', 'timezone' => '+3', 'status' => 101]);
        Country::query()->firstOrCreate(['name' => 'City5'],
            ['parent_id' => $country2->id, 'flag' => '', 'timezone' => '+3', 'status' => 101]);
        Country::query()->firstOrCreate(['name' => 'City6'],
            ['parent_id' => $country2->id, 'flag' => '', 'timezone' => '+3', 'status' => 101]);

        // create device
        $device = Device::query()->firstOrCreate(
            ['project' => 'OEE','machine' => 'OEE','process' => 'OEE'],
            ['uuid' => 'TE-ST1234', 'country_id' => $country1->id, 'city_id' => $city->id, 'timezone' => $city->timezone, 'version' => '1.0', 'status' => 202, 'plus_millisecond' => 10, 'produced_parts_per_hour' => 100, 'second_per_pulse' => 10, 'pieces_per_pulse' => 24]);
        // cache clear
        Artisan::call('cache:clear');
    }
}
