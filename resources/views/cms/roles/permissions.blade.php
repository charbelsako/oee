@extends('layouts._layout')
@php
    function stringToId($str){

        // Using str_ireplace() function
        // to replace the word
        $res = str_ireplace( array( '\'', '"',
        ',' , ';', '<', '>', '/' ), ' ', $str);

        // returning the result

        return   str_replace(' ', '', $res);
        }

    function isChecked($id, $arr) {
        return count($arr) && in_array($id, $arr) ? 'checked' : '';
    }
@endphp
@section('css')
    <style>
        .panel-group .panel + .panel {
            margin-top: unset;
        }
        .bg-numero {
            background-color: #A9003E !important;
        }
        .panel .panel-heading .panel-title {
            color: #fff !important;
        }
        .panel-group .panel {
            margin: 0 0 20px 0;
        }
        .panel .panel-heading {
            border-radius: 8px 8px 0 0;
        }
        .panel .panel-body {
            border-radius: 0 0 8px 8px;
            height: 200px;
            overflow: auto;
        }
        .panel .panel-title label {
            display: flex;
            justify-content: space-between;
            flex-wrap: nowrap;
            align-content: stretch;
            align-items: center;
            width: 100%;
        }
        .panel .permission label {
            display: flex;
            justify-content: space-between;
        }
    </style>
@endsection

@section('content')
    <section class="content-header">
        <h1>Permissions {{isset($role) ? "($role->name)" : ''}}</h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('welcome') }}"> Home</a></li>
            <li> <a href="{{ route('roles.index') }}"> Roles</a></li>
            @isset($role)
             <li> <a href="{{ route('roles.edit', $role->id) }}"> {{$role->name}}</a></li>
            @endisset
            <li class="active">Permissions</li>
        </ol>
    </section>

    <div class="custom-body p-t-10">
        <form id="permissions-form">
            <div class="row m-t-10 text-center">
                <div class="col-sm-12 custom-btn-center center-block">
                    <a class="btn custom-btn-info p-x-4 m-x-2" href="{{ route('roles.index') }}">Cancel</a>
                    <button {{!$role->is_edit ? 'disabled' : ''}} id="role_btn" class="btn custom-btn-brand btn-md p-x-4 m-x-2" type="submit">Save</button>
                </div>
            </div>
            <div class="panel-group row" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="col-md-12">
                    <label>
                        Select All
                        <input {{!$role->is_edit ? 'disabled' : ''}} onchange="selectAllPermissions()" id="select-all" type="checkbox">
                    </label>
                </div>
                @if(isset($permissions) && count($permissions))
                    @foreach($permissions as $group => $permission)
                        <div class=" col-lg-4 col-sm-6 panel panel-default">
                            <div class="panel-heading bg-numero" role="tab" id="heading-{{stringToId($group)}}">
                                <h4 class="panel-title">
                                    <label for="group-{{stringToId($group)}}">
                                    <span @if(strlen($group) >= 20) style="font-size: 10px"  @endif>
                                        {{$group}}
                                    </span>
                                        <input data-group-id="{{stringToId($group)}}" {{!$role->is_edit ? 'disabled' : ''}} onchange="selectGroup('{{stringToId($group)}}')" class="group-input" id="group-{{stringToId($group)}}" type="checkbox">
                                    </label>
                                </h4>
                            </div>
                            <div id="collapse-{{stringToId($group)}}" class="panel-collapse collapse in " role="tabpanel" aria-labelledby="heading-{{stringToId($group)}}">
                                <div class="panel-body  bg-gray">
                                    @foreach($permission as $perm)
                                        <div class="permission">
                                            <label>
                                           <span>
                                                {{$perm['label']}}
                                           </span>
                                                <input onchange="selectPermission()" {{!$role->is_edit ? 'disabled' : ''}} type="checkbox" {{isChecked($perm['id'], $checked_permissions)}} class="permission-input permission-group-{{stringToId($group)}}" value="{{$perm['id']}}" name="checked_permissions[]">
                                            </label>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

            </div>
            <div class="row m-t-10 text-center">
                <div class="col-sm-12 custom-btn-center center-block">
                    <a class="btn custom-btn-info p-x-4 m-x-2" href="{{ route('roles.index') }}">Cancel</a>
                    <button {{!$role->is_edit ? 'disabled' : ''}} id="role_btn" class="btn custom-btn-brand btn-md p-x-4 m-x-2" type="submit">Save</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>

        $(document).ready(function () {
            checkGroups();
        });

        $('#permissions-form').on('submit',function (e) {
            e.preventDefault();
            save();
        });

        function save(){
            let form = $('#permissions-form')[0];
            let form_data = new FormData(form);
            let request_url = "{{ route('roles.updateRolePermissions',$role->id) }}"
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
                    Numero.blockUI({target:'#permissions-form'});
                },
                complete: function (xhr, status) {
                    Numero.unblockUI('#permissions-form');
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

        function selectGroup(id) {
            let permissions  = document.querySelectorAll(`.permission-group-${id}`),
                group = document.querySelector(`#group-${id}`);

            if (group) {
                permissions.forEach(permission => {
                    permission.checked = group.checked;
                })
            }
        }

        function selectAllPermissions() {
            let selectAll = document.querySelector('#select-all'),
                permissions = document.querySelectorAll('.permission-input'),
                groups = document.querySelectorAll('.group-input');

            if (permissions.length) {
                permissions.forEach(permission => {
                    permission.checked = selectAll.checked;
                })
            }
            if (groups.length) {
                groups.forEach(group => {
                    group.checked = selectAll.checked;
                })
            }
        }

        function checkGroups() {
            let groups = document.querySelectorAll('.group-input');

            if (groups.length) {
                groups.forEach(group => {
                   let groupId = group.getAttribute('data-group-id');
                   let permissions = document.querySelectorAll(`.permission-group-${groupId}`);

                   let checked = true;
                   permissions.forEach(perm => {
                       if (!perm.checked) {
                           checked = false;
                       }
                   })

                    group.checked = checked;

                })
            }
        }

        function selectPermission() {
            checkGroups()
        }


    </script>
@endsection
