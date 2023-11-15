@extends('layouts._layout')
@section('js')
    <script src="{{ asset('assets/js/chartist.min.js') }}"></script>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/chartist.min.css') }}" />
@endsection

@section('content')
    <div>
        @foreach ($records as $record)
            <li>
                @foreach ($record as $value)
                    {{ $value . '->' }}
                @endforeach
            </li>
        @endforeach
    </div>
    <div id="myChart"></div>
    <!-- Add this script after including Chartist.js -->
    <script>
        // Sample data
        var data = {
            labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            series: [
                [5, 2, 4, 8, 3]
            ]
        };

        // Options for the chart
        var options = {
            // Add your chart options here
        };

        // Create a line chart
        new Chartist.Line('#myChart', data, options);
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
