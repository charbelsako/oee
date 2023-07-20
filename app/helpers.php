<?php

use Spatie\Permission\Exceptions\UnauthorizedException;

if (!function_exists("check_user_has_not_permission")) {
    function check_user_has_not_permission($permission='') {

        /*if(!auth()->check()){
            return redirect('/home')->with('msgstatus', 'error')->with('messagetext','You are not login');
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
