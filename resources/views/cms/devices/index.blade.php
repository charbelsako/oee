@extends('layouts._layout')

@section('css')
@endsection

@section('header')
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Devices
                    List</h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('home') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Device Management</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Devices</li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div id="kt_app_content_container" class="app-container container-xxl">
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1"
                                      transform="rotate(45 17.0365 15.1223)" fill="currentColor"/>
                                <path
                                    d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                    fill="currentColor"/>
                            </svg>
                        </span>
                        <input type="text" data-kt-device-table-filter="search"
                               class="form-control form-control-solid w-250px ps-14" placeholder="Search device"/>
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-device-table-toolbar="base">
                        <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click"
                                data-kt-menu-placement="bottom-end">
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
                                        fill="currentColor"/>
                                </svg>
                            </span>
                            Filter
                        </button>
                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                            <div class="px-7 py-5">
                                <div class="fs-5 text-dark fw-bold">Filter Options</div>
                            </div>
                            <div class="separator border-gray-200"></div>
                            <div class="px-7 py-5" data-kt-device-table-filter="form">
                                <div class="mb-10">
                                    <label class="form-label fs-6 fw-semibold">Status:</label>
                                    <select class="form-select form-select-solid fw-bold" data-kt-select2="true"
                                            data-placeholder="Select option" data-allow-clear="true"
                                            data-kt-device-table-filter="role" data-hide-search="true">
                                        <option></option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="reset"
                                            class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6"
                                            data-kt-menu-dismiss="true" data-kt-device-table-filter="reset">Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary fw-semibold px-6"
                                            data-kt-menu-dismiss="true" data-kt-device-table-filter="filter">Apply
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#kt_modal_add_device">
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1"
                                          transform="rotate(-90 11.364 20.364)" fill="currentColor"/>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"/>
                                </svg>
                            </span>Add Device
                        </button>
                    </div>
                    <div class="d-flex justify-content-end align-items-center d-none"
                         data-kt-device-table-toolbar="selected">
                        <div class="fw-bold me-5">
                            <span class="me-2" data-kt-device-table-select="selected_count"></span>Selected
                        </div>
                        <button type="button" class="btn btn-danger" data-kt-device-table-select="delete_selected">Delete
                            Selected
                        </button>
                    </div>
                    @include('cms.devices.partials._modal')
                </div>
            </div>
            <div class="card-body py-4" id="devices_table"></div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            getDevices();
        });

        $(document).on('click', '.pagination a', function (event) {
            event.preventDefault();
            let page = $(this).attr('href').split('page=')[1];
            getDevices(page)
        });

        function getDevices(page = 1) {
            $.ajax({
                url: "{{ route('devices.index') }}",
                data: {
                    'page': page,
                },
                beforeSend: function (xhr) {
                    Oee.blockUI({target: '#devices_table'});
                },
                complete: function (xhr, status) {
                    Oee.unblockUI('#devices_table');
                },
                success: function (res) {
                    $('#devices_table').empty().append(res.data.view_render);
                },
                error: function (xhr, status, message) {
                    swal("Cancelled", "Something went wrong!", "error");
                }
            });
        }

        $(document).on('click', '.delete_device', function (e) {
            e.preventDefault();
            let request_url = $(this).data('action');
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this row!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel plx!",
                closeOnConfirm: true,
                closeOnCancel: false
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: request_url,
                        method: 'POST',
                        processData: false,
                        contentType: false,
                        dataType: "json",
                        beforeSend: function (xhr) {
                            Oee.blockUI({target: '#device_table'});
                        },
                        complete: function (xhr, status) {
                            Oee.unblockUI('#device_table');
                        },
                        success: function (res) {
                            if (res.success) {
                                getDevices()
                                swal("Deleted!", res.message, "success");
                            } else {
                                swal("Error", res.message, "error");
                            }
                        },
                        error: function (xhr, status, message) {
                            swal("Cancelled", "Something went wrong!", "error");
                        }
                    });
                } else {
                    swal("Cancelled", "Your row is not deleted :)", "error");
                }
            });
        });

        $(document).on('submit', '#kt_modal_add_device_form', function (event) {
            event.preventDefault();
            var form = $(this)[0];
            var form_data = new FormData(form);
            $.ajax({
                url: "{{ route('devices.store') }}",
                method: 'POST',
                data: form_data,
                processData: false,
                contentType: false,
                dataType: "json",
                beforeSend: function (xhr) {
                    $('#new_device_btn').attr('disabled',true)
                },
                complete: function (xhr, status) {
                    $('#new_device_btn').attr('disabled',false)
                },
                success: function (res) {
                    if (res.success) {
                        getDevices()
                        swal("Save!", res.message, "success");
                        $('#kt_modal_add_device_form').modal('hide');
                    } else {
                        swal("Error", res.message, "error");
                    }
                },
                error: function (xhr, status, message) {
                    swal("Cancelled", "Something went wrong!", "error");
                }
            });
        });
    </script>
@endsection
