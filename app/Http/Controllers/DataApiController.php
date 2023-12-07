<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;
use Illuminate\Support\Facades\Log;
use App\Services\InfluxDBClientService;

class DataApiController extends Controller
{
    protected $influxDBClientService;

    public function __construct(InfluxDBClientService $influxDBClientService)
    {
        $this->influxDBClientService = $influxDBClientService;
    }
    public function getData(Request $request)
    {
        try {
            $device = $request->input("uuid");

            $queryApi = $this->influxDBClientService->createQueryApi();
            $query = "from(bucket: \"oee_test\") |> range(start: -13d) |> filter(fn: (r) => r._measurement == \"temperature\" and r.box_number == \"$device\")";
            $temperature_data = $queryApi->query($query);

            $records = [];
            foreach ($temperature_data as $table) {
                foreach ($table->records as $record) {
                    $row = key_exists($record->getTime(), $records) ? $records[$record->getTime()] : [];
                    $records[$record->getTime()] = array_merge($row, [$record->getField() => $record->getValue()]);
                }
            }

            $query = "from(bucket: \"oee_test\") |> range(start: -13d) |> filter(fn: (r) => r._measurement == \"voltage\" and r.box_number == \"$device\")";
            $temperature_data = $queryApi->query($query);
            $volt_data = $queryApi->query($query);

            $volt_records = [];
            foreach ($volt_data as $table) {
                foreach ($table->records as $record) {
                    $row = key_exists($record->getTime(), $volt_records) ? $volt_records[$record->getTime()] : [];
                    $volt_records[$record->getTime()] = array_merge($row, [$record->getField() => $record->getValue()]);
                }
            }

            $data = ['temperature' => $records, 'voltage' => $volt_records];
            Log::info($records);
            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function getNewData(Request $request)
    {
        try {
            $device = $request->input("uuid");
            $startDate = $request->input("startDate");

            $query = "from(bucket: \"oee_test\") |> range(start: time(v: \"$startDate\"), stop: now()) |> filter(fn: (r) => r._measurement == \"temperature\" and r.box_number == \"$device\")";
            $temperature_data = $this->influxDBClientService->queryData($query);

            $query = "from(bucket: \"oee_test\") |> range(start: time(v: \"$startDate\"), stop: now()) |> filter(fn: (r) => r._measurement == \"voltage\" and r.box_number == \"$device\")";
            $volt_data = $this->influxDBClientService->queryData($query);

            $query = "from(bucket: \"oee_test\") |> range(start: time(v: \"$startDate\"), stop: now()) |> filter(fn: (r) => r._measurement == \"airflow\" and r.box_number == \"$device\")";
            $airflow_data = $this->influxDBClientService->queryData($query);

            $query = "from(bucket: \"oee_test\") |> range(start: time(v: \"$startDate\"), stop: now()) |> filter(fn: (r) => r._measurement == \"humidity\" and r.box_number == \"$device\")";
            $humidity_data = $this->influxDBClientService->queryData($query);

            $query = "from(bucket: \"oee_test\") |> range(start: time(v: \"$startDate\"), stop: now()) |> filter(fn: (r) => r._measurement == \"intensity\" and r.box_number == \"$device\")";
            $current_data = $this->influxDBClientService->queryData($query);

            $data = [
                'temperature' => $temperature_data,
                'voltage' => $volt_data,
                'humidity' => $humidity_data,
                'intensity' => $current_data,
                'airflow' => $airflow_data
            ];
            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
