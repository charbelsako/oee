@extends('layouts._layout')
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js" referrerpolicy="no-referrer">
    </script>
@endsection
@section('css')
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

    <h1>Temperature Chart</h1>
    <canvas id="temperatureChart" width="700" height="400">
    </canvas>
    <h1>Voltage Chart</h1>
    <div id="voltageChart">
    </div>

    <script>
        let intervalTimer;
        let lastDataPoint;
        async function loadData() {
            try {
                const deviceId = document.querySelector('#uuid').value;
                let returnData = await fetch(`/api/get-data?uuid=${deviceId}`)
                let jsonData = await returnData.json();

                data = []
                let labels = [];
                for (let key in jsonData.temperature) {
                    const temperature = jsonData.temperature[key].celsius
                    const date = moment(key);
                    labels.push(date.format('dddd MMM hh mm:ss'))
                    data.push(temperature)
                    lastDataPoint = key;
                }

                // @TODO: Extract this code to its own function maybe named draw chart
                var data = {
                    labels,
                    datasets: [{
                        label: 'Temperature',
                        data,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        fill: false
                    }]
                };

                const options = {
                    tooltip: {
                        enabled: true,
                        mode: 'index',
                        intersect: false
                    }
                }
                var ctx = document.getElementById('temperatureChart').getContext('2d');

                if (window.myChart) {
                    console.log('Destroying')
                    window.myChart.destroy();
                }

                myChart = new Chart(ctx, {
                    type: 'line',
                    data: data,
                    options: options
                });

                // setTimeout(() => {
                //     console.log('fetching again')
                //     loadData();
                // }, 5000);
                getNewData();
            } catch (err) {
                console.error(err);
            }
        }

        function getNewData() {
            const deviceId = document.querySelector('#uuid').value
            let returnData = await fetch(`/api/get-new-data?uuid=${deviceId}&startDate=${lastDataPoint}`)
            let jsonData = await returnData.json();
            // @TODO get data from the last end point's date
            // const lastDate =
        }
        loadData();
    </script>

    <style>
        #temperatureChart {
            width: 700px;
            height: 350px;
        }
    </style>
@endsection
