@extends('layouts._layout')

@section('css')
@endsection

@section('header')
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Profile</h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('home') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">User Management</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Profile</li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div id="kt_app_content_container" class="app-container container-xxl">
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">Update profile</div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                        <button form="update_profile_form" type="submit" class="btn btn-primary" id="update_profile_btn">Update</button>
                    </div>
                </div>
            </div>
            <div class="card-body py-4" id="profile_form">
                <form id="update_profile_form" class="form" action="#">
                    @csrf
                    <div class="d-flex flex-column scroll-y me-n7 pe-7">
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Full Name</label>
                            <input type="text" name="name"
                                   class="form-control form-control-solid mb-3 mb-lg-0"
                                   placeholder="Full name" value="{{ auth()->user()->name }}"/>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Email</label>
                            <input type="email" name="email"
                                   class="form-control form-control-solid mb-3 mb-lg-0"
                                   placeholder="example@domain.com" value="{{ auth()->user()->email }}"/>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Password</label>
                            <input type="password" name="password"
                                   class="form-control form-control-solid mb-3 mb-lg-0"
                                   placeholder="********" value=""/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).on('submit', '#update_profile_form', function (event) {
            event.preventDefault();
            var update_btn = $('update_profile_btn');
            var form = $(this)[0];
            var form_data = new FormData(form);
            $.ajax({
                url: "{{ route('users.profile.post') }}",
                method: 'POST',
                data: form_data,
                processData: false,
                contentType: false,
                dataType: "json",
                beforeSend: function (xhr) {
                    update_btn.attr('disabled',true)
                },
                complete: function (xhr, status) {
                    update_btn.attr('disabled',false)
                },
                success: function (res) {
                    if (res.success) {
                        swal("Save!", res.message, "success");
                        $('#kt_modal_add_user_form').modal('hide');
                    } else {
                        swal("Error", res.message, "error");
                    }
                    update_btn.attr('disabled',false)
                },
                error: function (xhr, status, message) {
                    swal("Cancelled", "Something went wrong!", "error");
                    update_btn.attr('disabled',false)
                }
            });
        });
    </script>
@endsection
