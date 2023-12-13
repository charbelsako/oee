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
                        <div class="col-4">
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
                        {{-- <div class="col-2">
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
                        </div> --}}
                        {{-- <div class="col-2">
                            <label for="live_id" class="form-control">Is live:</label>
                            <select id="live_id" class="form-control">
                                <option {{ $is_live ? 'selected' : '' }} value="1">Yes</option>
                                <option {{ !$is_live ? 'selected' : '' }} value="2">No</option>
                            </select>
                        </div> --}}
                        <div class="col-4">
                            <span class="h2">To:</span>
                        </div>
                        <div class="col-2">
                            <label for="year_id2" class="form-control">Year:</label>
                            <select id="year_id2" class="form-control">
                                @for ($y = 2023; $y < 2026; $y++)
                                    <option {{ date('Y') == $y ? 'selected' : '' }} value="{{ $y }}">
                                        {{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="month_id2" class="form-control">Month:</label>
                            <select id="month_id2" class="form-control">
                                <!-- Month options will be added dynamically here based on the year selection -->
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="day_id2" class="form-control">Day:</label>
                            <select id="day_id2" class="form-control">
                                <!-- Day options will be added dynamically here based on the month selection -->
                            </select>
                        </div>
                        {{-- <div class="col-2">
                            <label for="shift_id2" class="form-control">Shift:</label>
                            <select id="shift_id2" class="form-control">
                                <option {{ date('H:i') >= '6:00' && date('H:i') <= '14:00' ? 'selected' : '' }}
                                    value="1">
                                    6:00 - 14:00</option>
                                <option {{ date('H:i') >= '14:00' && date('H:i') <= '22:00' ? 'selected' : '' }}
                                    value="1">14:00 - 22:00</option>
                                <option {{ date('H:i') >= '22:00' && date('H:i') <= '6:00' ? 'selected' : '' }}
                                    value="1">
                                    22:00 - 6:00</option>
                            </select>
                        </div> --}}
                        {{-- <div class="col-2">
                            <label for="live_id2" class="form-control">Is live:</label>
                            <select id="live_id2" class="form-control">
                                <option {{ $is_live ? 'selected' : '' }} value="1">Yes</option>
                                <option {{ !$is_live ? 'selected' : '' }} value="2">No</option>
                            </select>
                        </div> --}}
                    </div>
                    <div class="card-body row">
                        <div class="col-2"><button class="btn btn-info" onClick="fetchData()">Load</button></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gy-5 g-xl-10 mt-3">
            <div class="col-sm-12 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-header">
                        <div class="card-title">
                            <span class="fw-semibold h1 text-gray-400">OEE</span>
                        </div>

                    </div>
                    <div class="card-body row">
                        <div class="m-0 col-12">
                            <canvas id="oeeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-header">
                        <div class="card-title">
                            <span class="fw-semibold h1 text-gray-400">Availability</span>
                        </div>

                    </div>
                    <div class="card-body row">
                        <div class="m-0 col-12">
                            <canvas id="availabilityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-header">
                        <div class="card-title">
                            <span class="fw-semibold h1 text-gray-400">Performance</span>
                        </div>

                    </div>
                    <div class="card-body row">
                        <div class="m-0 col-12">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-header">
                        <div class="card-title">
                            <span class="fw-semibold h1 text-gray-400">Quality</span>
                        </div>

                    </div>
                    <div class="card-body row">
                        <div class="m-0 col-12">
                            <canvas id="qualityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-header">
                        <div class="card-title">
                            <span class="fw-semibold h1 text-gray-400">Cycle Time</span>
                        </div>
                    </div>
                    <div class="card-body row">
                        <div class="m-0 col-12">
                            <canvas id="cycleChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js" referrerpolicy="no-referrer">
        < script src = "https://cdnjs.cloudflare.com/ajax/libs/chartjs-adapter-moment/1.0.1/chartjs-adapter-moment.min.js"
        integrity = "sha512-hVy4KxCKgnXi2ok7rlnlPma4JHXI1VPQeempoaclV1GwRHrDeaiuS1pI6DVldaj5oh6Opy2XJ2CTljQLPkaMrQ=="
        crossorigin = "anonymous"
        referrerpolicy = "no-referrer" >
    </script>
    </script>
    <script>
        $(document).ready(function() {
            let year_id = $("#year_id");
            let to_year_id = $("#year_id2")
            let month_id = $("#month_id");
            let to_month_id = $("#month_id2")
            let day_id = $("#day_id");
            let to_day_id = $("#day_id2")


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
            updateToMonthSelect();
            updateToDaySelect();
            updateDaySelect();

            // Event listener for the year select
            year_id.on("change", function() {
                updateMonthSelect();
                updateDaySelect();
            });
            to_year_id.on("change", function() {
                updateMonthSelect();
                updateDaySelect();
            });
            // Event listener for the month select
            month_id.on("change", function() {
                updateDaySelect();
            });
            to_month_id.on('change', function() {
                updateDaySelect()
            })

            function updateToMonthSelect() {
                const selectedYear = to_year_id.val();

                // Clear previous options
                to_month_id.empty();

                // Add new month options based on the selected year
                $.each(monthsByYear[selectedYear], function(index, month) {
                    let selected = false;
                    let selected_month_id = "{{ date('F') }}";
                    if (month == selected_month_id) {
                        selected = true;
                    }
                    to_month_id.append($("<option>", {
                        value: month,
                        text: month,
                        selected: selected
                    }));
                });
            }

            function updateToDaySelect() {
                const selectedMonth = to_month_id.val();

                // Clear previous options
                to_day_id.empty();

                // Get the number of days in the selected month
                const daysInMonth = new Date(to_year_id.val(), monthsByYear[to_year_id.val()].indexOf(
                        selectedMonth) + 1,
                    0).getDate();

                // Add new day options based on the selected month
                for (let day = 1; day <= daysInMonth; day++) {
                    let selected = false;
                    let selected_day_id = "{{ date('d') }}";
                    if (day == selected_day_id) {
                        selected = true;
                    }
                    to_day_id.append($("<option>", {
                        value: day,
                        text: day,
                        selected: selected
                    }));
                }
            }

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
        // @TODO: remove static data
        var data = [{
                "Date": "01/09/23",
                "Total Production Hours": 24,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 0,
                "Changeover + Setup Time (mins)": 15,
                "Quality Inspection Time (mins)": 0,
                "Planned Performance Rate": "98.0%",
                "Target Quality Rate": "99.0%",
                "Machine Planned Cycle Time (s)": 42.00,
                "Planned Availability (mins)": 1290,
                "Planned Availability (%)": "89.58%",
                "Planned OEE": "86.91%",
                "Planned Production (parts)": 1843,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 15,
                "Unplanned Maintenance Time (mins)": 20,
                "Changeover+ Setup Time (mins)": 0,
                "Other Factors (mins)": 50,
                "Quality Inspection Time (mins)": 0,
                "Total Parts Produced": 1710,
                "Total Good Parts": 1699,
                "Actual Availability (mins)": 1220,
                "Actual Availability (%)": "84.72%",
                "Actual Cycle Time (s)": 42.807,
                "Actual Performance Rate": "98.11%",
                "Actual QUALITY Rate": "99.36%",
                "Actual OEE": "82.59%",
                "Remarks": "Bosch/ENP trials - 50 min\n3rd station assembly cylinder nut loose - 20 min"
            },
            {
                "Date": "02/09/23",
                "Total Production Hours": 24,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 60,
                "Changeover + Setup Time (mins)": 15,
                "Quality Inspection Time (mins)": 0,
                "Planned Performance Rate": "98.0%",
                "Target Quality Rate": "98.0%",
                "Machine Planned Cycle Time (s)": 42.00,
                "Planned Availability (mins)": 1230,
                "Planned Availability (%)": "85.42%",
                "Planned OEE": "82.03%",
                "Planned Production (parts)": 1757,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 60,
                "Unplanned Maintenance Time (mins)": 60,
                "Changeover+ Setup Time (mins)": 15,
                "Other Factors (mins)": 0,
                "Quality Inspection Time (mins)": 0,
                "Total Parts Produced": 1662,
                "Total Good Parts": 1659,
                "Actual Availability (mins)": 1170,
                "Actual Availability (%)": "81.25%",
                "Actual Cycle Time (s)": 42.238,
                "Actual Performance Rate": "99.44%",
                "Actual QUALITY Rate": "99.82%",
                "Actual OEE": "80.65%",
                "Remarks": "PU pad change & all stations alignment check - 60 min (planned activity)"
            },
            {
                "Date": "03/09/23",
                "Planned Availability (%)": "98.0%",
                "Target Quality Rate": "98.0%",
                "Machine Planned Cycle Time (s)": 35.3,
                // (other data for the third row)
            },
            {
                "Date": "04/09/23",
                "Total Production Hours": 24,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 0,
                "Changeover + Setup Time (mins)": 15,
                "Quality Inspection Time (mins)": 0,
                "Planned Performance Rate": "98.0%",
                "Target Quality Rate": "98.0%",
                "Machine Planned Cycle Time (s)": 42.00,
                "Planned Availability (mins)": 1290,
                "Planned Availability (%)": "89.58%",
                "Planned OEE": "86.04",
                "Planned Production (parts)": 1843,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 60,
                "Unplanned Maintenance Time (mins)": 60,
                "Changeover+ Setup Time (mins)": 15,
                "Other Factors (mins)": 0,
                "Quality Inspection Time (mins)": 0,
                "Total Parts Produced": 1785,
                "Total Good Parts": 1780,
                "Actual Availability (mins)": 1170,
                "Actual Availability (%)": "81.25%",
                "Actual Cycle Time (s)": 39.328,
                "Actual Performance Rate": "106.79%",
                "Actual QUALITY Rate": "99.72%",
                "Actual OEE": "86.53",
                "Remarks": "Spring seat half assembly - 60 min"
            },
            {
                "Date": "05/09/23",
                "Total Production Hours": 24,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 0,
                "Changeover + Setup Time (mins)": 15,
                "Quality Inspection Time (mins)": 0,
                "Planned Performance Rate": "98.0%",
                "Target Quality Rate": "98.0%",
                "Machine Planned Cycle Time (s)": 42.00,
                "Planned Availability (mins)": 1290,
                "Planned Availability (%)": "89.58%",
                "Planned OEE": "86.04",
                "Planned Production (parts)": 1843,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 0,
                "Unplanned Maintenance Time (mins)": 0,
                "Changeover+ Setup Time (mins)": 15,
                "Other Factors (mins)": 0,
                "Quality Inspection Time (mins)": 0,
                "Total Parts Produced": 1830,
                "Total Good Parts": 1827,
                "Actual Availability (mins)": 1290,
                "Actual Availability (%)": "89.58%",
                "Actual Cycle Time (s)": 42.295,
                "Actual Performance Rate": "99.30%",
                "Actual QUALITY Rate": "99.84%",
                "Actual OEE": "88.81%",
                "Remarks": "No major issues"
            },
            {
                "Date": "06/09/23",
                "Total Production Hours": 24,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 0,
                "Changeover + Setup Time (mins)": 15,
                "Quality Inspection Time (mins)": 0,
                "Planned Performance Rate": "98.0%",
                "Target Quality Rate": "98.0%",
                "Machine Planned Cycle Time (s)": 42.00,
                "Planned Availability (mins)": 1290,
                "Planned Availability (%)": "89.58%",
                "Planned OEE": "86.04",
                "Planned Production (parts)": 1843,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 0,
                "Unplanned Maintenance Time (mins)": 0,
                "Changeover+ Setup Time (mins)": 15,
                "Other Factors (mins)": 0,
                "Quality Inspection Time (mins)": 0,
                "Total Parts Produced": 1850,
                "Total Good Parts": 1844,
                "Actual Availability (mins)": 1290,
                "Actual Availability (%)": "89.58%",
                "Actual Cycle Time (s)": 41.838,
                "Actual Performance Rate": "100.39%",
                "Actual QUALITY Rate": "99.68%",
                "Actual OEE": "89.64%",
                "Remarks": "No major issues"
            },
            {
                "Date": "07/09/23",
                "Total Production Hours": 24,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 0,
                "Changeover + Setup Time (mins)": 15,
                "Quality Inspection Time (mins)": 0,
                "Planned Performance Rate": "98.0%",
                "Target Quality Rate": "98.0%",
                "Machine Planned Cycle Time (s)": 42.00,
                "Planned Availability (mins)": 1290,
                "Planned Availability (%)": "89.58%",
                "Planned OEE": "86.04",
                "Planned Production (parts)": 1843,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 360,
                "Unplanned Maintenance Time (mins)": 360,
                "Changeover+ Setup Time (mins)": 15,
                "Other Factors (mins)": 0,
                "Quality Inspection Time (mins)": 0,
                "Total Parts Produced": 1456,
                "Total Good Parts": 1445,
                "Actual Availability (mins)": 570,
                "Actual Availability (%)": "39.58%",
                "Actual Cycle Time (s)": 23.489,
                "Actual Performance Rate": "178.81%",
                "Actual QUALITY Rate": "99.24%",
                "Actual OEE": "70.24%",
                "Remarks": "1st station - Poka yoke sensor for filler piece malfunction"
            }, {
                "Date": "08/09/23",
                "Total Production Hours": 24,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 0,
                "Changeover + Setup Time (mins)": 15,
                "Quality Inspection Time (mins)": 0,
                "Planned Performance Rate": "98.0%",
                "Target Quality Rate": "98.0%",
                "Machine Planned Cycle Time (s)": 42.00,
                "Planned Availability (mins)": 1290,
                "Planned Availability (%)": "89.58%",
                "Planned OEE": "86.04",
                "Planned Production (parts)": 1843,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 0,
                "Unplanned Maintenance Time (mins)": 0,
                "Changeover+ Setup Time (mins)": 15,
                "Other Factors (mins)": 0,
                "Quality Inspection Time (mins)": 0,
                "Total Parts Produced": 1850,
                "Total Good Parts": 1842,
                "Actual Availability (mins)": 1290,
                "Actual Availability (%)": "89.58",
                "Actual Cycle Time (s)": 41.838,
                "Actual Performance Rate": "100.39",
                "Actual QUALITY Rate": "99.57",
                "Actual OEE": "89.54",
                "Remarks": "No major issues"
            }, {
                "Date": "09/09/23",
                "Total Production Hours": 24,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 60,
                "Changeover + Setup Time (mins)": 15,
                "Quality Inspection Time (mins)": 0,
                "Planned Performance Rate": "98.0%",
                "Target Quality Rate": "98.0%",
                "Machine Planned Cycle Time (s)": 42.00,
                "Planned Availability (mins)": 1230,
                "Planned Availability (%)": "85.42%",
                "Planned OEE": "82.03",
                "Planned Production (parts)": 1757,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 60,
                "Unplanned Maintenance Time (mins)": 60,
                "Changeover+ Setup Time (mins)": 15,
                "Other Factors (mins)": 0,
                "Quality Inspection Time (mins)": 0,
                "Total Parts Produced": 1735,
                "Total Good Parts": 1720,
                "Actual Availability (mins)": 1170,
                "Actual Availability (%)": "81.25%",
                "Actual Cycle Time (s)": 40.461,
                "Actual Performance Rate": "103.80%",
                "Actual QUALITY Rate": "99.14",
                "Actual OEE": "83.61",
                "Remarks": "PU pad change & all stations alignment check - 60 min (planned activity)"
            },
            {
                "Date": "10/09/23",
                "Total Production Hours": 24,
                "Planned Availability (%)": "98.0",
                "Target Quality Rate": "98.0",
                "Machine Planned Cycle Time (s)": 42,
                "Actual QUALITY Rate": "99.66",
                "Planned OEE": "86.04",
                "Actual OEE": "83.61",
                "Actual Performance Rate": "94.42",
                "Planned Performance Rate": "98.0",
                "Actual Cycle Time (s)": 44.483,
                // Add other fields for the fifth row based on your actual data
            },
            {
                "Date": "11/09/23",
                "Total Production Hours": 24,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 0,
                "Changeover + Setup Time (mins)": 15,
                "Quality Inspection Time (mins)": 0,
                "Planned Performance Rate": "98.0",
                "Target Quality Rate": "98.0",
                "Machine Planned Cycle Time (s)": 42.00,
                "Planned Availability (mins)": 1290,
                "Planned Availability (%)": "89.58",
                "Planned OEE": "86.04",
                "Planned Production (parts)": 1843,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 0,
                "Unplanned Maintenance Time (mins)": 0,
                "Changeover+ Setup Time (mins)": 15,
                "Other Factors (mins)": 0,
                "Quality Inspection Time (mins)": 0,
                "Total Parts Produced": 1740,
                "Total Good Parts": 1734,
                "Actual Availability (mins)": 1290,
                "Actual Availability (%)": "89.58",
                "Actual Cycle Time (s)": 44.483,
                "Actual Performance Rate": "94.42",
                "Actual QUALITY Rate": "99.66",
                "Actual OEE": "84.29",
                "Remarks": "3rd station - Leakage NOK. PU pad change & recheck."
            },
            {
                "Date": "12/09/23",
                "Total Production Hours": 24,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 0,
                "Changeover + Setup Time (mins)": 15,
                "Quality Inspection Time (mins)": 0,
                "Planned Performance Rate": "98.0",
                "Target Quality Rate": "98.0",
                "Machine Planned Cycle Time (s)": 42.00,
                "Planned Availability (mins)": 1290,
                "Planned Availability (%)": "89.58",
                "Planned OEE": "86.04",
                "Planned Production (parts)": 1843,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 0,
                "Unplanned Maintenance Time (mins)": 0,
                "Changeover+ Setup Time (mins)": 15,
                "Other Factors (mins)": 0,
                "Quality Inspection Time (mins)": 0,
                "Total Parts Produced": 1850,
                "Total Good Parts": 1845,
                "Actual Availability (mins)": 1290,
                "Actual Availability (%)": "89.58",
                "Actual Cycle Time (s)": 41.838,
                "Actual Performance Rate": "100.39",
                "Actual QUALITY Rate": "99.73",
                "Actual OEE": "89.69",
                "Remarks": "No major issues"
            },
            {
                "Date": "13/09/23",
                "Total Production Hours": 24,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 0,
                "Changeover + Setup Time (mins)": 15,
                "Quality Inspection Time (mins)": 0,
                "Planned Performance Rate": "98.0",
                "Target Quality Rate": "98.0",
                "Machine Planned Cycle Time (s)": 42.00,
                "Planned Availability (mins)": 1290,
                "Planned Availability (%)": "89.58",
                "Planned OEE": "86.04",
                "Planned Production (parts)": 1843,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 0,
                "Unplanned Maintenance Time (mins)": 0,
                "Changeover+ Setup Time (mins)": 15,
                "Other Factors (mins)": 0,
                "Quality Inspection Time (mins)": 0,
                "Total Parts Produced": 1850,
                "Total Good Parts": 1841,
                "Actual Availability (mins)": 1290,
                "Actual Availability (%)": "89.58",
                "Actual Cycle Time (s)": 41.838,
                "Actual Performance Rate": "100.39",
                "Actual QUALITY Rate": "99.51",
                "Actual OEE": "89.49",
                "Remarks": "No major issues"
            },
            {
                "Date": "14/09/23",
                "Total Production Hours": 24,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 0,
                "Changeover + Setup Time (mins)": 15,
                "Quality Inspection Time (mins)": 0,
                "Planned Performance Rate": "98.0",
                "Target Quality Rate": "98.0",
                "Machine Planned Cycle Time (s)": 42.00,
                "Planned Availability (mins)": 1290,
                "Planned Availability (%)": "89.58",
                "Planned OEE": "86.04",
                "Planned Production (parts)": 1843,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 0,
                "Unplanned Maintenance Time (mins)": 0,
                "Changeover+ Setup Time (mins)": 15,
                "Other Factors (mins)": 0,
                "Quality Inspection Time (mins)": 0,
                "Total Parts Produced": 1820,
                "Total Good Parts": 1808,
                "Actual Availability (mins)": 1290,
                "Actual Availability (%)": "89.58",
                "Actual Cycle Time (s)": 42.527,
                "Actual Performance Rate": "98.76",
                "Actual QUALITY Rate": "99.34",
                "Actual OEE": "87.89",
                "Remarks": "Trial parts assembly (Localization)"
            },
            {
                "Date": "15/09/23",
                "Total Production Hours": 24,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 0,
                "Changeover + Setup Time (mins)": 15,
                "Quality Inspection Time (mins)": 0,
                "Planned Performance Rate": "98.0",
                "Target Quality Rate": "98.0",
                "Machine Planned Cycle Time (s)": 42.00,
                "Planned Availability (mins)": 1290,
                "Planned Availability (%)": "89.58",
                "Planned OEE": "86.04",
                "Planned Production (parts)": 1843,
                "Total Break Time (mins)": 135,
                "Planned Maintenance Time (mins)": 0,
                "Unplanned Maintenance Time (mins)": 0,
                "Changeover+ Setup Time (mins)": 15,
                "Other Factors (mins)": 0,
                "Quality Inspection Time (mins)": 0,
                "Total Parts Produced": 1840,
                "Total Good Parts": 1824,
                "Actual Availability (mins)": 1290,
                "Actual Availability (%)": "89.58",
                "Actual Cycle Time (s)": 42.065,
                "Actual Performance Rate": "99.84",
                "Actual QUALITY Rate": "99.13",
                "Actual OEE": "88.67",
                "Remarks": "No major issues"
            }
        ]

        var months = [
            "January", "February", "March", "April", "May", "June", "July",
            "August", "September", "October", "November", "December"
        ]

        function getMonthNumber(month) {
            return months.indexOf(month) + 1
        }

        function fetchData() {
            let from_year = $("#year_id").val();
            let from_month = $("#month_id").val();
            let from_day = $("#day_id").val();

            let to_year = $("#year_id2").val();
            let to_month = $("#month_id2").val();
            let to_day = $("#day_id2").val();

            const startDate = moment(`${from_year}-${getMonthNumber(from_month)}-${from_day}`)
            const endDate = moment(`${to_year}-${getMonthNumber(to_month)}-${to_day}`)

            const filteredData = fetchDataByDateRange(startDate, endDate, data)
            let actualAvailability = calcluateAvailability(filteredData)
            let plannedAvailability = getPlannedAvailability(filteredData)
            let actualCycleTime = getCycleTime(filteredData)
            let plannedCycleTime = getPlannedCycleTime(filteredData)
            let plannedPerformance = getPlannedPerformance(filteredData)
            let actualPerformance = getActualPerformance(filteredData)
            let actualOEE = getActualOEE(filteredData)
            let plannedOEE = getPlannedOEE(filteredData)
            let plannedQuality = getPlannedQuality(filteredData)
            let actualQuality = getActualQuality(filteredData)

            drawChart('cycleChart', actualCycleTime, plannedCycleTime, 'Actual Cycle Time', 'Planned Cycle Time')
            drawChart('performanceChart', actualPerformance, plannedPerformance, 'Actual Performance',
                'Planned Performance')
            drawChart('qualityChart', actualQuality, plannedQuality, 'Actual Quality', 'PlannedQuality')
            drawChart('availabilityChart', actualAvailability, plannedAvailability, 'Actual Availability',
                'Planned Availability')
            drawChart('oeeChart', actualOEE, plannedOEE, 'Actual OEE', 'Planned OEE')
        }

        function parseDateFromString(dateString) {
            return moment(dateString, 'DD/MM/YYYY');
        }

        function fetchDataByDateRange(startDate, endDate, jsonData) {
            const filteredData = jsonData.filter(row => {
                const rowDate = parseDateFromString(row.Date);
                console.log(rowDate, 'Row Date')
                return rowDate.isSameOrAfter(startDate) && rowDate.isSameOrBefore(endDate);
            });

            return filteredData;
        }

        function getPlannedCycleTime(data) {
            let cycleTime = []
            for (let i = 0; i < data.length; i++) {
                let row = data[i];
                let cycle = row["Machine Planned Cycle Time (s)"] || 0
                cycleTime.push({
                    date: row.Date,
                    value: cycle
                })
            }
            return cycleTime
        }

        function getCycleTime(data) {
            let cycleTime = []
            for (let i = 0; i < data.length; i++) {
                let row = data[i];
                let totalPartsProduced = row["Total Parts Produced"] || 1
                let actualAvailability = row["Actual Availability (mins)"] || 0
                let time = (actualAvailability * 60) / totalPartsProduced;
                cycleTime.push({
                    value: time,
                    date: row.Date
                })
            }
            return cycleTime
        }

        function getPlannedAvailability(data) {
            let plannedAvailability = [];
            for (let i = 0; i < data.length; i++) {
                let row = data[i];
                let plannedBreakTime = row["Total Break Time (mins)"] || 0;
                let plannedMaintenanceTime = row["Planned Maintenance Time (mins)"] || 0
                let changeoverAndSetup = row["Changeover + Setup Time (mins)"] || 0
                let plannedQualityInspectionTime = row["Quality Inspection Time (mins)"] || 0
                let totalPlanned = plannedBreakTime + plannedMaintenanceTime + changeoverAndSetup +
                    plannedQualityInspectionTime;
                let result = row["Total Production Hours"] * 60 - totalPlanned;
                let percentage = (result / (row["Total Production Hours"] * 60)) * 100
                plannedAvailability.push({
                    value: percentage,
                    date: row.Date
                });
            }
            return plannedAvailability;
        }

        function calcluateAvailability(data) {
            actualAvailability = []
            for (let i = 0; i < data.length; i++) {
                let row = data[i];

                let totalBreakTime = row["Total Break Time (mins)"] || 0;
                let totalPlannedMaintenance = row["Planned Maintenance Time (mins)"] || 0;
                let changeOverAndSetup = row["Changeover+ Setup Time (mins)"] || 0
                let totalUnplannedMaintenance = row["Unplanned Maintenance Time (mins)"] || 0
                let qualityInspectionTime = row["Quality Inspection Time (mins)"] || 0
                let otherFactors = row["Quality Inspection Time (mins)"] || 0
                let totalTimeOn = totalBreakTime + totalPlannedMaintenance + totalUnplannedMaintenance +
                    changeOverAndSetup + qualityInspectionTime + otherFactors;
                let actualAvailabilityResult = (row["Total Production Hours"] * 60 - totalTimeOn) / (row[
                    "Total Production Hours"] * 60);
                let percentage = actualAvailabilityResult * 100;
                actualAvailability.push({
                    value: +percentage.toFixed(2),
                    date: row.Date
                });
            }
            return actualAvailability;
        }

        function getActualPerformance(data) {
            let actualPerformance = []
            for (let i = 0; i < data.length; i++) {
                let row = data[i];
                actualPerformance.push({
                    value: +row["Actual Performance Rate"] || 0,
                    date: row.Date
                })
            }
            return actualPerformance
        }

        function getPlannedPerformance(data) {
            let plannedPerformance = []
            for (let i = 0; i < data.length; i++) {
                let row = data[i];
                plannedPerformance.push({
                    date: row.Date,
                    value: +row["Planned Performance Rate"] || 0
                })
            }
            return plannedPerformance
        }

        function getActualOEE(data) {
            let actualOEE = []
            for (let i = 0; i < data.length; i++) {
                let row = data[i];
                actualOEE.push({
                    date: row.Date,
                    value: +row["Actual OEE"] || 0
                })
            }
            return actualOEE
        }

        function getPlannedOEE(data) {
            let plannedOEE = []
            for (let i = 0; i < data.length; i++) {
                let row = data[i];
                plannedOEE.push({
                    date: row.Date,
                    value: +row["Planned OEE"] || 0
                })
            }
            return plannedOEE
        }

        function getActualQuality(data) {
            let actualQuality = []
            for (let i = 0; i < data.length; i++) {
                let row = data[i];
                actualQuality.push({
                    date: row.Date,
                    value: +row["Actual QUALITY Rate"] || 0
                })
            }
            return actualQuality
        }

        function getPlannedQuality(data) {
            let plannedQuality = []
            for (let i = 0; i < data.length; i++) {
                let row = data[i];
                plannedQuality.push({
                    date: row.Date,
                    value: +row["Target Quality Rate"] || 0
                })
            }
            return plannedQuality
        }

        function drawChart(id, data_actual, data_planned, label1, label2) {
            const ctx = document.getElementById(id).getContext('2d');

            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data_actual.map(entry => entry.date), // Use the date as labels
                    datasets: [{
                        label: label1,
                        data: data_actual.map(entry => entry.value),
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        fill: true,
                    }, {
                        label: label2,
                        data: data_planned.map(entry => entry.value),
                        borderColor: 'rgba(0, 1, 192, 1)',
                        borderWidth: 1,
                        fill: false,
                    }],
                },
            });
        }
    </script>
@endsection
