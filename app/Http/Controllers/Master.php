<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use Auth;
use File;
use Session;
use Config;

class Master extends Controller {

    public function __construct(){
        
        //echo Theme::init($this->systemConfig());die;
    }
    
    /*
     * @Get All the System configuration
     */
    public function systemConfig($system_name=null) {
        $system_val = '';
        if(!empty($system_name)){ 
            $system = \App\SystemConfig::select('system_val')->where('system_name','=', $system_name)->first();
            $system_val = $system->system_val;
        }  
        return $system_val;         
    } 

 

    /*
     * @Get load the theme
     */
    public static function loadFrontTheme($path) {
        if(session('default_theme') == null){
            $default_theme = \App\SystemConfig::getSystemVal('DEFAULT_THEME');
            \Session::put('default_theme', $default_theme);
        }
        return session('default_theme').'.'.$path;
    }


    function getConfiguration($type) {
   
        $conf_lists = \App\SystemConfig::getSystemConfig($type); 
        $conf_lists = $conf_lists->toArray();
        foreach($conf_lists as $val) {            
            $config_arr[$val['system_name']] = $val['system_val'];
        }
        //echo '<pre>';print_r($config_arr);die;
        return $config_arr;
    }



    public static function Render($name){
        return view('admin.'.$name);

    }



}
