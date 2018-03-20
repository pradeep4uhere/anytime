<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SystemConfig extends Model
{
    protected $table = 'system_config';
    
    public $timestamps = false;

    public static function getSystemConfig($type) {
    	return self::where('system_type', $type)->get();
    }    

    public static function getSystemVal($system_name) {
    	return self::where('system_name', $system_name)->value('system_val');
    } 
}
