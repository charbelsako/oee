@extends('layouts._layout')

@push('css')
    <style>
        .chart-container {
            width: 100%;
            height: 300px;
        }

        .highcharts-data-table table {
            width: 600px;
            margin: 0 auto;
        }

        .highcharts-data-table table {
            font-family: Verdana, sans-serif;
            border-collapse: collapse;
            border: 1px solid #ebebeb;
            margin: 10px auto;
            text-align: center;
            width: 100%;
            max-width: 500px;
        }

        .highcharts-data-table caption {
            padding: 1em 0;
            font-size: 1.2em;
            color: #555;
        }

        .highcharts-data-table th {
            font-weight: 600;
            padding: 0.5em;
        }

        .highcharts-data-table td,
        .highcharts-data-table th,
        .highcharts-data-table caption {
            padding: 0.5em;
        }

        .highcharts-data-table thead tr,
        .highcharts-data-table tr:nth-child(even) {
            background: #f8f8f8;
        }

        .highcharts-data-table tr:hover {
            background: #f1f7ff;
        }

        @media (max-width: 600px) {
            .highcharts-data-table table {
                width: 100%;
            }

            .chart-container {
                width: 300px;
                float: none;
                margin: 0 auto;
            }
        }
    </style>
@endpush

@section('header')
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Overview</h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('home') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('devices.index') }}" class="text-muted text-hover-primary">Device Management</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Show Device</li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div id="kt_app_content_container" class="app-container container-xxl">
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="row col-12 border border-gray-300 border-bottom-1">
                    <div class="card-title row">
                        <div class="col-6">
                            <span class="font-bold h4">Location: </span>
                            <span class="h6">{{ $item->country->name . ' - ' . $item->city->name }}</span>
                        </div>
                        <div class="col-6">
                            <span class="font-bold h4">Version: </span>
                            <span class="h6">{{ $item->version }}</span>
                        </div>
                        <div class="col-6">
                            <span class="font-bold h4">IP address: </span>
                            <span class="h6">127.0.0.1</span>
                        </div>
                        <div class="col-6">
                            <span class="font-bold h4">Status: </span>
                            <span class="h6">{{ \App\Enums\Constants::getNameById($item->status) }}</span>
                        </div>
                        <div class="col-12">
                            <span class="font-bold h4">Machine Local: </span>
                            <span class="h6 clock">
                                <span id="date"></span>
                                <span id="time"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row col-12">
                    <div class="card-title row">
                        <div class="col-12">
                            <span class="font-bold h1">Project: </span>
                            <span class="h3">{{ $item->project }}</span>
                        </div>
                        <div class="col-12">
                            <span class="font-bold h1">Machine: </span>
                            <span class="h3">{{ $item->machine }}</span>
                        </div>
                        <div class="col-12">
                            <span class="font-bold h1">Process: </span>
                            <span class="h3">{{ $item->process }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card my-3">
            <div class="card-header border-0 py-6">
                <div class="row col-12">
                    <div class="card-title row">
                        <div class="col-2">
                            <span class="h2">From</span>
                        </div>
                        <div class="col-2">
                            <label for="year_id" class="form-control">Year:</label>
                            <select id="year_id" class="form-control">
                                @for ($y = 2023; $y < 2026; $y++)
                                    <option {{ date('Y') == $y ? 'selected' : '' }} value="{{ $y }}">
                                        {{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="month_id" class="form-control">Month:</label>
                            <select id="month_id" class="form-control">
                                <!-- Month options will be added dynamically here based on the year selection -->
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="day_id" class="form-control">Day:</label>
                            <select id="day_id" class="form-control">
                                <!-- Day options will be added dynamically here based on the month selection -->
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="shift_id" class="form-control">Shift:</label>
                            <select id="shift_id" class="form-control">
                                <option {{ date('H:i') >= '6:00' && date('H:i') <= '14:00' ? 'selected' : '' }}
                                    value="1">
                                    6:00 - 14:00</option>
                                <option {{ date('H:i') >= '14:00' && date('H:i') <= '22:00' ? 'selected' : '' }}
                                    value="1">14:00 - 22:00</option>
                                <option {{ date('H:i') >= '22:00' && date('H:i') <= '6:00' ? 'selected' : '' }}
                                    value="1">
                                    22:00 - 6:00</option>
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="live_id" class="form-control">Is live:</label>
                            <select id="live_id" class="form-control">
                                <option {{ $is_live ? 'selected' : '' }} value="1">Yes</option>
                                <option {{ !$is_live ? 'selected' : '' }} value="2">No</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <p class="fw-semibold h1 text-gray-400 text-center">
                    {{ $no_data ? 'No data' : '' }}
                </p>
            </div>
        </div>

        <div class="card">
            <div class="col-12 card-body">
                {{-- <p class="fw-semibold h1 text-gray-400 text-center">

                    </p> --}}
                <a href="/devices/showgraph/{{ $item->id }}">Go to analytics report</a>
            </div>
        </div>
        <div class="row gy-5 g-xl-10 mt-3">
            <div class="col-sm-6 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-header">
                        <div class="card-title">
                            <span class="fw-semibold h1 text-gray-400">OEE</span>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex justify-content-end" data-kt-device-table-toolbar="base">
                                <span class="fw-semibold badge badge-light-success fs-1">{{ $oee }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body row">
                        <div class="m-0 col-12">
                            <div id="container-oee" class="chart-container"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-header">
                        <div class="card-title">
                            <span class="fw-semibold h1 text-gray-400">Availability</span>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex justify-content-end" data-kt-device-table-toolbar="base">
                                <span class="fw-semibold badge badge-light-success fs-1">{{ $availability }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body row">
                        <div class="m-0 col-12">
                            <div id="container-availability" class="chart-container"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-header">
                        <div class="card-title">
                            <span class="fw-semibold h1 text-gray-400">Performance</span>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex justify-content-end" data-kt-device-table-toolbar="base">
                                <span class="fw-semibold badge badge-light-success fs-1">{{ $performance }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body row">
                        <div class="m-0 col-12">
                            <div id="container-performance" class="chart-container"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-header">
                        <div class="card-title">
                            <span class="fw-semibold h1 text-gray-400">Quality</span>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex justify-content-end" data-kt-device-table-toolbar="base">
                                <span class="fw-semibold badge badge-light-success fs-1">{{ $quality }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body row">
                        <div class="m-0 col-12">
                            <div id="container-quality" class="chart-container"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gy-5 g-xl-10 mt-3">
            <div class="col-sm-4 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-body row">
                        <div class="m-0 col-7">
                            <span class="fw-semibold h1 text-gray-400">Shift duration</span>
                        </div>
                        <div class="m-0 col-5">
                            <span class="fw-semibold badge badge-light-success fs-1">{{ $shift_duration }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-body row">
                        <div class="m-0 col-7">
                            <span class="fw-semibold h1 text-gray-400">Planned Break</span>
                        </div>
                        <div class="m-0 col-5">
                            <span class="fw-semibold badge badge-light-success fs-1">{{ $planned_break }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-body row">
                        <div class="m-0 col-7">
                            <span class="fw-semibold h1 text-gray-400">Unplanned Breakdown</span>
                        </div>
                        <div class="m-0 col-5">
                            <span class="fw-semibold badge badge-light-success fs-1">{{ $unplanned_break }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gy-5 g-xl-10 mt-3">
            <div class="col-sm-3 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-body row">
                        <div class="m-0 col-7">
                            <span class="fw-semibold h1 text-gray-400">Target Production</span>
                        </div>
                        <div class="m-0 col-5">
                            <span class="fw-semibold badge badge-light-success fs-1">{{ $target_production }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-body row">
                        <div class="m-0 col-7">
                            <span class="fw-semibold h1 text-gray-400">Possible Production</span>
                        </div>
                        <div class="m-0 col-5">
                            <span class="fw-semibold badge badge-light-success fs-1">{{ $possible_production }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-body row">
                        <div class="m-0 col-7">
                            <span class="fw-semibold h1 text-gray-400">Actual Production</span>
                        </div>
                        <div class="m-0 col-5">
                            <span class="fw-semibold badge badge-light-success fs-1">{{ $actual_production }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-body row">
                        <div class="m-0 col-7">
                            <span class="fw-semibold h1 text-gray-400">Ok/Nok</span>
                        </div>
                        <div class="m-0 col-5">
                            <span
                                class="fw-semibold badge badge-light-success fs-1">{{ $ok_parts . '/' . $nok_parts }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/solid-gauge.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script>
        $(document).ready(function() {
            let year_id = $("#year_id");
            let month_id = $("#month_id");
            let day_id = $("#day_id");


            const monthsByYear = {
                "2023": ["January", "February", "March", "April", "May", "June", "July", "August", "September",
                    "October", "November", "December"
                ],
                "2024": ["January", "February", "March", "April", "May", "June", "July", "August", "September",
                    "October", "November", "December"
                ],
                "2025": ["January", "February", "March", "April", "May", "June", "July", "August", "September",
                    "October", "November", "December"
                ]
            };

            const dayOptions = Array.from({
                length: 31
            }, (_, i) => i + 1);

            // Initialize the month and day selects
            updateMonthSelect();
            updateDaySelect();

            // Event listener for the year select
            year_id.on("change", function() {
                updateMonthSelect();
                updateDaySelect();
            });
            // Event listener for the month select
            month_id.on("change", function() {
                updateDaySelect();
            });

            function updateMonthSelect() {
                const selectedYear = year_id.val();

                // Clear previous options
                month_id.empty();

                // Add new month options based on the selected year
                $.each(monthsByYear[selectedYear], function(index, month) {
                    let selected = false;
                    let selected_month_id = "{{ date('F') }}";
                    if (month == selected_month_id) {
                        selected = true;
                    }
                    month_id.append($("<option>", {
                        value: month,
                        text: month,
                        selected: selected
                    }));
                });
            }

            function updateDaySelect() {
                const selectedMonth = month_id.val();

                // Clear previous options
                day_id.empty();

                // Get the number of days in the selected month
                const daysInMonth = new Date(year_id.val(), monthsByYear[year_id.val()].indexOf(selectedMonth) + 1,
                    0).getDate();

                // Add new day options based on the selected month
                for (let day = 1; day <= daysInMonth; day++) {
                    let selected = false;
                    let selected_day_id = "{{ date('d') }}";
                    if (day == selected_day_id) {
                        selected = true;
                    }
                    day_id.append($("<option>", {
                        value: day,
                        text: day,
                        selected: selected
                    }));
                }
            }
        });
    </script>

    <script>
        var WEEK = ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"];

        function zeroPadding(num, digit) {
            return String(num).padStart(digit, '0');
        }
    </script>
    <script>
        var gaugeOptions = {
            chart: {
                type: 'solidgauge'
            },
            title: null,
            pane: {
                center: ['50%', '85%'],
                size: '140%',
                startAngle: -90,
                endAngle: 90,
                background: {
                    backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || '#EEE',
                    innerRadius: '60%',
                    outerRadius: '100%',
                    shape: 'arc'
                }
            },
            exporting: {
                enabled: false
            },
            tooltip: {
                enabled: false
            },
            // the value axis
            yAxis: {
                stops: [
                    [0.1, '#55BF3B'], // green
                    [0.5, '#DDDF0D'], // yellow
                    [0.9, '#DF5353'] // red
                ],
                lineWidth: 0,
                tickWidth: 0,
                minorTickInterval: null,
                tickAmount: 2,
                title: {
                    y: -70
                },
                labels: {
                    y: 16
                }
            },
            plotOptions: {
                solidgauge: {
                    dataLabels: {
                        y: 5,
                        borderWidth: 0,
                        useHTML: true
                    }
                }
            }
        };

        // The speed gauge
        var chartOEE = Highcharts.chart('container-oee', Highcharts.merge(gaugeOptions, {
            yAxis: {
                min: 0,
                max: 100,
                title: {
                    text: 'OEE'
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'OEE',
                data: [{{ $oee }}],
                dataLabels: {
                    format: '<div style="text-align:center">' +
                        '<span style="font-size:25px">{y}</span><br/>' +
                        '<span style="font-size:12px;opacity:0.4">%</span>' +
                        '</div>'
                },
                tooltip: {
                    valueSuffix: ' %'
                }
            }]
        }));

        // The speed gauge
        var chartAvailability = Highcharts.chart('container-availability', Highcharts.merge(gaugeOptions, {
            yAxis: {
                min: 0,
                max: 100,
                title: {
                    text: 'Availability'
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Availability',
                data: [{{ $availability }}],
                dataLabels: {
                    format: '<div style="text-align:center">' +
                        '<span style="font-size:25px">{y}</span><br/>' +
                        '<span style="font-size:12px;opacity:0.4">%</span>' +
                        '</div>'
                },
                tooltip: {
                    valueSuffix: ' %'
                }
            }]
        }));

        // The speed gauge
        var chartPerformance = Highcharts.chart('container-performance', Highcharts.merge(gaugeOptions, {
            yAxis: {
                min: 0,
                max: 100,
                title: {
                    text: 'Performance'
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Performance',
                data: [{{ $performance }}],
                dataLabels: {
                    format: '<div style="text-align:center">' +
                        '<span style="font-size:25px">{y}</span><br/>' +
                        '<span style="font-size:12px;opacity:0.4">%</span>' +
                        '</div>'
                },
                tooltip: {
                    valueSuffix: ' %'
                }
            }]
        }));

        // The speed gauge
        var chartQuality = Highcharts.chart('container-quality', Highcharts.merge(gaugeOptions, {
            yAxis: {
                min: 0,
                max: 100,
                title: {
                    text: 'Quality'
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Quality',
                data: [{{ $quality }}],
                dataLabels: {
                    format: '<div style="text-align:center">' +
                        '<span style="font-size:25px">{y}</span><br/>' +
                        '<span style="font-size:12px;opacity:0.4">%</span>' +
                        '</div>'
                },
                tooltip: {
                    valueSuffix: ' %'
                }
            }]
        }));
    </script>
@endsection
