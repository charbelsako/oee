@extends('layouts._layout')
@section('js')
    <script src="{{ asset('assets/js/chartist.min.js') }}"></script>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/chartist.min.css') }}" />
@endsection

@section('content')
    <script></script>
    <div id="myChart"></div>
    <!-- Add this script after including Chartist.js -->
    <script>
        async function loadData() {

            try {
                let returnData = await fetch('/api/get-data')
                let jsonData = await returnData.json();
                // @TODO: Separate all data points by their respective periods
                data = []
                let labels = [];
                for (let key in jsonData) {
                    const points = jsonData[key].celsius.split(',')
                    data.push(...points);
                    const period = jsonData[key].period;
                    const date = new Date(key);
                    labels.push(date)
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

                // Options for the chart
                var options = {
                    // Add your chart options here
                };

                // Create a line chart
                new Chartist.Line('#myChart', data, options);
            } catch (err) {
                console.error(err);
            }
        }
        loadData();
    </script>

    <style>
        #myChart {
            width: 100%;
            height: 300px;
            /* Set a fixed height or adjust as needed */
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
