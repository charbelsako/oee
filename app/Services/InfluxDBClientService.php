<?php

namespace App\Services;

use InfluxDB2\Client;
use InfluxDB2\QueryApi;
use InfluxDB2\WriteApi;
use InfluxDB2\Model\WritePrecision;

class InfluxDBClientService
{
  protected $client;

  public function __construct()
  {
    $this->client = new Client([
      'url' => env('INFLUXDB_HOST'),
      'token' => env('INFLUXDB_TOKEN'),
      'bucket' => env('INFLUXDB_BUCKET'),
      'org' => env('INFLUXDB_ORG'),
      'precision' => WritePrecision::MS,
      'verifySSL' => false
    ]);
  }

  public function getClient(): Client
  {
    return $this->client;
  }

  public function createWriteApi(): WriteApi
  {
    return $this->client->createWriteApi();
  }

  public function createQueryApi(): QueryApi
  {
    return $this->client->createQueryApi();
  }
}
