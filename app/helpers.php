<?php

use Spatie\Permission\Exceptions\UnauthorizedException;

if (!function_exists("check_user_has_not_permission")) {
    function check_user_has_not_permission($permission='') {

        /*if(!auth()->check()){
            return redirect('/home')->with('msgstatus', 'error')->with('messagetext','You are not logged in');
        }
        if($permission == '' || auth()->user()->cannot($permission)) {*/
            throw new UnauthorizedException(403,"Access denied");
//        }
    }
}
if (!function_exists("check_user_has_not_multiple_permissions")) {
    function check_user_has_not_multiple_permissions($permissions=[]) {

        /*if(!auth()->check()){
            return redirect('user/login')->with('msgstatus', 'error')->with('messagetext','You are not login');
        }
        if( !count($permissions) || !auth()->user()->hasAnyPermission($permissions)) {*/
            throw new UnauthorizedException(403,"Access denied");
//        }
    }
}
if (!function_exists("generate_new_device_uuid_code")) {
    function generate_new_device_uuid_code($first,$second) {
        $number = random_int(0000,9999);
        $uuid = $first . '-' . $second . $number;
        $check_uuid = \App\Models\Device::where('uuid',$uuid)->count();
        if ($check_uuid) {
            generate_new_device_uuid_code($first,$second);
        }
        return $uuid;
    }
}

function find_pattern($string, $value) {
    $pattern = '/'.$value.'[0-9,.]*/i';
    $matches = array();
    preg_match($pattern, $string, $matches);
    $matches = str_replace($value, '', $matches[0]);
    $result = explode(',', $matches);
    $result = array_filter($result, function($value) {
        if ($value !== '') {
            return $value;
        }
    });
    return $result;
}