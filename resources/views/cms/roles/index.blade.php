@extends('layouts._layout')

@section('css')
@endsection

@section('header')
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Roles
                    List</h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('home') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Role Management</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Roles</li>
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
                        <input type="text" data-kt-role-table-filter="search" id="search_txt"
                               class="form-control form-control-solid w-250px ps-14" placeholder="Search role"/>
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-role-table-toolbar="base">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#kt_modal_add_role">
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1"
                                          transform="rotate(-90 11.364 20.364)" fill="currentColor"/>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"/>
                                </svg>
                            </span>Add Role
                        </button>
                    </div>
                    <div class="d-flex justify-content-end align-items-center d-none"
                         data-kt-role-table-toolbar="selected">
                        <div class="fw-bold me-5">
                            <span class="me-2" data-kt-role-table-select="selected_count"></span>Selected
                        </div>
                        <button type="button" class="btn btn-danger" data-kt-role-table-select="delete_selected">Delete
                            Selected
                        </button>
                    </div>
                    @include('cms.roles.partials._modal')
                </div>
            </div>
            <div class="card-body py-4" id="roles_table"></div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            getRoles();
        });

        $(document).on('click', '.pagination a', function (event) {
            event.preventDefault();
            let page = $(this).attr('href').split('page=')[1];
            getRoles(page)
        });

        $(document).on('keyup', '#search_txt', function (event) {
            let search_txt = $(this).val();
            if(search_txt.length > 3 || search_txt.length == 0){
                getRoles()
            }
        });

        $(document).on('click', '.edit_role', function (e) {
            e.preventDefault();
            let request_url = $(this).data('action');
            $('#role_temp_div').hide();
            getRoleById(request_url);
        });

        $(document).on('click', '.delete_role', function (e) {
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
            }).then((willDelete) => {
                if (willDelete){
                    $.ajax({
                        url: request_url,
                        method: 'POST',
                        processData: false,
                        contentType: false,
                        dataType: "json",
                        beforeSend: function (xhr) {
                            Oee.blockUI({target: '#role_table'});
                        },
                        complete: function (xhr, status) {
                            Oee.unblockUI('#role_table');
                        },
                        success: function (res) {
                            if (res.success) {
                                getRoles()
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

        $(document).on('hidden.bs.modal', '#kt_modal_add_role', function (e) {
            $('#kt_modal_add_role_form').attr('action',"{{ route('roles.store') }}");
            $(this)
                .find("input,textarea").val('').end()
                .find("select").each(function() {this.selectedIndex = 0;}).end()
                .find("input[type=checkbox], input[type=radio]").prop("checked", "").end();
        })

        $(document).on('submit', '#kt_modal_add_role_form', function (event) {
            event.preventDefault();
            let request_url = $(this).attr('action');
            var form = $(this)[0];
            var form_data = new FormData(form);
            $.ajax({
                url: request_url,
                method: 'POST',
                data: form_data,
                processData: false,
                contentType: false,
                dataType: "json",
                beforeSend: function (xhr) {
                    $('#new_role_btn').attr('disabled',true)
                },
                complete: function (xhr, status) {
                    $('#new_role_btn').attr('disabled',false)
                    getRoleTempAvailable()
                },
                success: function (res) {
                    if (res.success) {
                        getRoles()
                        swal("Save!", res.message, "success");
                        $('#kt_modal_add_role').modal('hide');
                        $('#role_temp_div').show();
                        $('#kt_modal_add_user_form').attr('action',"{{ route('roles.store') }}");
                    } else {
                        swal("Error", res.message, "error");
                    }
                },
                error: function (xhr, status, message) {
                    swal("Cancelled", "Something went wrong!", "error");
                }
            });
        });

        function getRoles(page = 1) {
            let search_txt = $('#search_txt').val();
            $.ajax({
                url: "{{ route('roles.index') }}",
                data: {
                    'page': page,
                    'search': search_txt,
                },
                beforeSend: function (xhr) {
                    Oee.blockUI({target: '#roles_table'});
                },
                complete: function (xhr, status) {
                    Oee.unblockUI('#roles_table');
                },
                success: function (res) {
                    $('#roles_table').empty().append(res.data.view_render);
                },
                error: function (xhr, status, message) {
                    swal("Cancelled", "Something went wrong!", "error");
                }
            });
        }

        function getRoleById(request_url) {
            $.ajax({
                url: request_url,
                beforeSend: function (xhr) {
                    Oee.blockUI({target: '#roles_table'});
                },
                complete: function (xhr, status) {
                    Oee.unblockUI('#roles_table');
                },
                success: function (res) {
                    $('#kt_modal_add_role_form').attr('action',"{{ route('roles.update') }}");
                    $('#role_id').val(res.data.id);
                    $('#name').val(res.data.name);
                    $('#kt_modal_add_role').modal('show');
                },
                error: function (xhr, status, message) {
                    swal("Cancelled", "Something went wrong!", "error");
                }
            });
        }
    </script>
@endsection
