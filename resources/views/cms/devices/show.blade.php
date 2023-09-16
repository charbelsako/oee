@extends('layouts._layout')

@section('css')
@endsection

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
                            <span class="h6">{{ $item->created_at }}</span>
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
                            <span class="h2">Filters</span>
                        </div>
                        <div class="col-2">
                            <label for="yearSelect" class="form-control">Year:</label>
                            <select id="yearSelect" class="form-control">
                                @for($y=2023;$y<2026;$y++)
                                <option value="{{$y}}">{{$y}}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="monthSelect" class="form-control">Month:</label>
                            <select id="monthSelect" class="form-control">
                                <!-- Month options will be added dynamically here based on the year selection -->
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="daySelect" class="form-control">Day:</label>
                            <select id="daySelect" class="form-control">
                                <!-- Day options will be added dynamically here based on the month selection -->
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="yearSelect" class="form-control">Shift:</label>
                            <select id="yearSelect" class="form-control">
                                <option value="1">6:00 - 14:00</option>
                                <option value="2">14:00 - 22:00</option>
                                <option value="3">22:00 - 6:00</option>
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="yearSelect" class="form-control">Is live:</label>
                            <select id="yearSelect" class="form-control">
                                <option value="1">Yes</option>
                                <option value="2">No</option>
                            </select>
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
                            <span class="fw-semibold h1 text-gray-400">OEE</span>
                        </div>
                        <div class="m-0 col-5">
                            <span class="fw-semibold badge badge-light-success fs-1">{{ $oee }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-body row">
                        <div class="m-0 col-7">
                            <span class="fw-semibold h1 text-gray-400">Availability</span>
                        </div>
                        <div class="m-0 col-5">
                            <span class="fw-semibold badge badge-light-success fs-1">{{ $availability }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 mb-xl-10">
                <div class="card h-lg-100">
                    <div class="card-body row">
                        <div class="m-0 col-7">
                            <span class="fw-semibold h1 text-gray-400">Performance</span>
                        </div>
                        <div class="m-0 col-5">
                            <span class="fw-semibold badge badge-light-success fs-1">{{{ $performance }}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 mb-xl-10" >
                <div class="card h-lg-100">
                    <div class="card-body row">
                        <div class="m-0 col-7">
                            <span class="fw-semibold h1 text-gray-400">Quality</span>
                        </div>
                        <div class="m-0 col-5">
                            <span class="fw-semibold badge badge-light-success fs-1">{{ $quality }}</span>
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
                            <span class="fw-semibold badge badge-light-success fs-1">{{ $ok_parts . '/' . $nok_parts }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            const monthsByYear = {
                "2023": ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                "2024": ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                "2025": ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]
            };

            const dayOptions = Array.from({ length: 31 }, (_, i) => i + 1);

            // Initialize the month and day selects
            updateMonthSelect();
            updateDaySelect();

            // Event listener for the year select
            $("#yearSelect").on("change", function() {
                updateMonthSelect();
                updateDaySelect();
            });

            // Event listener for the month select
            $("#monthSelect").on("change", function() {
                updateDaySelect();
            });

            function updateMonthSelect() {
                const selectedYear = $("#yearSelect").val();
                const $monthSelect = $("#monthSelect");

                // Clear previous options
                $monthSelect.empty();

                // Add new month options based on the selected year
                $.each(monthsByYear[selectedYear], function(index, month) {
                    $monthSelect.append($("<option>", {
                        value: month,
                        text: month
                    }));
                });
            }

            function updateDaySelect() {
                const selectedMonth = $("#monthSelect").val();
                const $daySelect = $("#daySelect");

                // Clear previous options
                $daySelect.empty();

                // Get the number of days in the selected month
                const daysInMonth = new Date($("#yearSelect").val(), monthsByYear[$("#yearSelect").val()].indexOf(selectedMonth) + 1, 0).getDate();

                // Add new day options based on the selected month
                for (let day = 1; day <= daysInMonth; day++) {
                    $daySelect.append($("<option>", {
                        value: day,
                        text: day
                    }));
                }
            }
        });
    </script>
@endsection
