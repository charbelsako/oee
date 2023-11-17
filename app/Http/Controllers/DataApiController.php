<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;
use Illuminate\Support\Facades\Log;

class DataApiController extends Controller
{
    public function getData()
    {
        $records = [];

        // Get data to view in home
        $client = new Client([
            'url' => env('INFLUXDB_HOST'),
            'token' => env('INFLUXDB_TOKEN'),
            'bucket' => env('INFLUXDB_BUCKET'),
            'org' => env('INFLUXDB_ORG'),
            'precision' => WritePrecision::S,
            'verifySSL' => false
        ]);
        $queryApi = $client->createQueryApi();
        $query = "from(bucket: \"oee_test\") |> range(start: -6d) |> filter(fn: (r) => r._measurement == \"temperature\")";
        $temperature_data = $queryApi->query($query);

        $records = [];
        foreach ($temperature_data as $table) {
            foreach ($table->records as $record) {
                error_log('Temperature ' . $record->getTime() . PHP_EOL);
                $row = key_exists($record->getTime(), $records) ? $records[$record->getTime()] : [];
                $records[$record->getTime()] = array_merge($row, [$record->getField() => $record->getValue()]);
            }
        }

        return $records;
    }
}
