@extends('layouts._layout')

@section('css')
    <style>
        #form_div {
            padding: 0 5rem 5rem 5rem !important;
            margin: 5rem !important;
        }
        .img-offer {
            width: 50px;
            height: 50px;
        }
    </style>
@endsection

@section('content')
    <section class="content-header">
        <h1>Roles</h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('welcome') }}"> Home</a></li>
            <li><a href="{{route('roles.index')}}">Roles</a></li>
            <li class="active">Add new role</li>
        </ol>
    </section>

    <div class="custom-body p-t-10 bg-gray">
        <div id="form_div">
            @include('cms.roles.partials._form')
        </div>
    </div>
@endsection

@section('js')
    <script>
        $('#role_form').on('submit',function (e) {
            e.preventDefault();
            create_role_request();
        });

        function create_role_request(){
            let form = $('#role_form')[0];
            let form_data = new FormData(form);
            let request_url = "{{ isset($item)?route('roles.update',$item->id):route('roles.store') }}"
            $.ajax({
                url: request_url,
                method: 'POST',
                data: form_data,
                processData: false,
                contentType: false,
                dataType: "json",
                beforeSend: function (xhr) {
                    $("#role_btn").prop('disabled',true);
                    $(".form-control").removeClass('is-invalid');
                    $('.invalid-feedback').empty()
                    Numero.blockUI({target:'#role_form'});
                },
                complete: function (xhr, status) {
                    Numero.unblockUI('#role_form');
                },
                success: function (res) {
                    if (res.success) {
                        notyMessage(res.message);
                        setTimeout(function() {
                            window.location.href = "{{ route('roles.index') }}";
                        }, 3000);
                    } else {
                        $.each(res.errors, function (key, value) {
                            var id = '#'+key;
                            $(id+":not([class*='is-invalid'])").addClass("is-invalid");
                            $(id).closest('.form-group').find('.invalid-feedback').html(value[0])
                        });
                        notyMessageError(res.message);
                        $("#role_btn").prop('disabled',false);
                    }
                },
                error: function (xhr, status, message) {
                    $("#role_btn").prop('disabled',false);
                    notyMessageError('Something went wrong!');
                }
            });
        }
    </script>
@endsection
