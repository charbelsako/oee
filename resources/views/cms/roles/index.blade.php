@extends('layouts._layout')

@section('css')
    <style>
        .custom-body {
            padding: 1rem;
            border-radius: 8px;
        }

        #role_table thead th {
            text-align: center;
            vertical-align: middle;
        }

        #role_table {
            border-collapse: separate;
            border-spacing: 0 4px;
        }

        #role_table tbody td:first-child {
            border-top-left-radius: 8px;
        }

        #role_table tbody td:last-child {
            border-top-right-radius: 8px;
        }

        #role_table td:first-child {
            border-bottom-left-radius: 8px;
        }

        #role_table td:last-child {
            border-bottom-right-radius: 8px;
        }

        .add_new_btn {
            font-size: 18px !important;
        }
    </style>
@endsection

@section('content')
    <section class="content-header">
        <h1>Roles</h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('welcome') }}"> Home</a></li>
            <li class="active"> Roles</li>
        </ol>
    </section>
    <div class="custom-body p-t-10 bg-gray">
        @can('role_add')
            <a class="btn btn-brand custom-add-btn br-15 p-10 text-white" href="{{ route('roles.create') }}">
                <span class="fa fa-plus-circle"></span><span class="add_new_btn"> Add new role</span></a>
        @endcan
        <div style="margin-top: 5px" id="table_div"></div>
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

        function getRoles(page = 1) {
            $.ajax({
                url: "{{ route('roles.index') }}",
                data: {
                    'page': page,
                },
                success: function (res) {
                    $('#table_div').empty().append(res.data.view_render);
                }
            });
        }

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
                cancelButtonText: "No, cancel please!",
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
                            Numero.blockUI({target: '#role_table'});
                        },
                        complete: function (xhr, status) {
                            Numero.unblockUI('#role_table');
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
    </script>
@endsection
