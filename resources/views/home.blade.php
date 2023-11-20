@extends('layouts._layout')
@section('js')
    <script src="{{ asset('assets/js/chartist.min.js') }}"></script>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/chartist.min.css') }}" />
@endsection

@section('content')
    <div class="row align-items-center m-5">
        <div class="col-3 text-center">
            <label for="uuid">Choose Device: </label>
        </div>
        <div class="col-6">
            <select name="uuid" id="uuid" class="form-select m-5">
                @foreach ($devices as $device)
                    <option value="{{ $device->uuid }}">{{ $device->uuid }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-3">
            <button onClick="loadData()" class="btn btn-info">Load</button>
        </div>
    </div>

    <div id="temperatureChart">
        <h1>Temperature Chart</h1>
    </div>
    <div id="voltageChart">
        <h1>Voltage Chart</h1>
    </div>
    <!-- Add this script after including Chartist.js -->
    <script>
        async function loadData() {
            try {
                const deviceId = document.querySelector('#uuid').value;
                let returnData = await fetch(`/api/get-data?uuid=${deviceId}`)
                let jsonData = await returnData.json();

                data = []
                let labels = [];
                for (let key in jsonData) {
                    const points = jsonData[key].celsius.split(',')
                    data.push(...points);
                    const period = jsonData[key].period;
                    const date = new Date(key);
                    labels.push(date.toDateString())
                    for (let i = 1; i < points.length; i += 1) {
                        const newDate = new Date();
                        newDate.setSeconds(date.getSeconds() - period / 1000)
                        labels.push(newDate);
                    }
                }

                var data = {
                    labels,
                    series: [data]
                };

                var options = {
                    width: '700px',
                    height: '300px',
                };

                new Chartist.Line('#temperatureChart', data, options);
            } catch (err) {
                console.error(err);
            }
        }
        loadData();
    </script>

    <style>
        #temperatureChart {
            width: 700px;
            height: 350px;
        }
    </style>
    {{-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div> --}}
@endsection
