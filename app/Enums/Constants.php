<?php

namespace App\Enums;

class Constants
{
    // user status
    const STATUS = [
        'inactive'  => 101,
        'active'    => 102,
    ];
    const STATUSNAME = [
        101    => ['name'=>'active','label'=>'Active'],
        102    => ['name'=>'inactive','label'=>'Inactive'],
    ];
    // user editable status
    const EDITABLESTATUS = [
        'editable'     => 151,
        'not_editable' => 152,
    ];
    const EDITABLESTATUSNAME = [
        151    => ['name'=>'editable','label'=>'Editable'],
        152    => ['name'=>'not_editable','label'=>'Not Editable'],
    ];

    // device status
    const DEVICESTATUS = [
        'running'           => 201,
        'paused'            => 202,
        'pause_for_checked' => 203,
        'pause_for_error'   => 204,
    ];
    const DEVICESTATUSNAME = [
        201    => ['name'=>'running','label'=>'Running'],
        202    => ['name'=>'paused','label'=>'Paused'],
        203    => ['name'=>'pause_for_checked','label'=>'Pause for checked'],
        204    => ['name'=>'pause_for_error','label'=>'Pause for error'],
    ];


    public static function getIdByName($key)
    {
        $key = strtolower($key);
        return self::allId()[$key]??0;
    }

    public static function getNameById($key,$type='label')
    {
        $key = strtolower($key);
        return self::allName()[$key][$type]??"-";
    }

    public static function allId($type='all')
    {
        if ($type=='status') {
            return self::STATUS;
        }elseif ($type=='editable') {
            return self::EDITABLESTATUS;
        }elseif ($type=='device') {
            return self::DEVICESTATUS;
        }else{
            return array_merge(self::STATUS,self::EDITABLESTATUS,self::DEVICESTATUS);
        }
    }

    public static function allName($type='all')
    {
        if ($type=='status') {
            return self::STATUSNAME;
        }elseif ($type=='editable') {
            return self::EDITABLESTATUSNAME;
        }elseif ($type=='device') {
            return self::DEVICESTATUSNAME;
        }else{
            return array_merge(self::STATUSNAME,self::EDITABLESTATUSNAME,self::DEVICESTATUSNAME);
        }
    }
}
