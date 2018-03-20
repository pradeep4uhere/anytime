<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\Language;
use App\Country;
use App\AttributeValue;
use App\Menu;
use App\MenusPermission;
use App\Province;
use App\Currency;
use Config;
use Auth;
use Form;
use Session;
use Lang;
class CustomHelpers {

    public static function getUserMenu() {

        $default_lang_code = '';

        $default_lang_code = session('lang_code');

        //dd(session()->all());

        // to select parent menu
        $menu_path = '/'.\Request::path();

        $menu_path = str_replace($default_lang_code.'/', '', $menu_path);

        $menus = Menu::select('id', 'parent_id')->where('url','=', $menu_path)->first();
        
        $main_menus_id = 0;
        $sub_menus_id = 0;
        $final_menus_id = 0;
        
        if(!empty($menus)) {
            
            if($menus->parent_id > 0){
                $sub_menus_id = $menus->id;
                $main_menus_id = $menus->parent_id;

                $menus2 = Menu::select('parent_id')->where('id','=', $menus->parent_id)->first();

                if(!empty($menus2)){

                    $final_menus_id = $menus->id;
                    $sub_menus_id = $menus->parent_id;
                    $main_menus_id = $menus2->parent_id;
                }
            }
            else {
                $main_menus_id = $menus->id;
            }            
        }

        //echo '====>'.$main_menus_id.'=='.$sub_menus_id.'=='.$final_menus_id;

        // to select parent menu        

        $menu_str = '';

        $users_main_menus = Menu::getAdminMenu();
        foreach ($users_main_menus as $users_main_menu){
            
            $menu_link = 'javascript:void(0);';
            if($users_main_menu->menu_type == '1'){
                $menu_link = $default_lang_code.$users_main_menu->url;
            }

            $class_str = '';                            
            if($main_menus_id == $users_main_menu->id) {                               
                $class_str = 'class="active"';
            }             
          
            $menu_icon = 'icon-seller-menu3';
            if(!empty($users_main_menu->icon_class)) {
                $menu_icon = $users_main_menu->icon_class;
            }

            $menu_str .= '<li '.$class_str.'><a href="'.$menu_link.'" title="'.$users_main_menu->name.'"> <span class="icon '.$menu_icon.'"></span>'.$users_main_menu->name.'</a>';

            $users_sub_menus = Menu::getAdminMenu($users_main_menu->id);       
            if(count($users_sub_menus) > 0) {                  

                $menu_str .= '<div class="submenu-wrapper">
                                <div class="close-menu"><span class="icon-remove"></span></div>
                                <h3>'.$users_main_menu->name.'</h3>
                                <ul class="adm-submenu">';
                                                       
                foreach ($users_sub_menus as $users_sub_menu){
                    
                    $menu_link = 'javascript:void(0);';
                    $arrow_sign = '<span class="glyphicon glyphicon-menu-right"></span>';
                    if($users_sub_menu->menu_type == '1'){
                        $menu_link = $default_lang_code.$users_sub_menu->url;
                        $arrow_sign = '';
                    } 

                    $class_str = '';                            
                    if($sub_menus_id == $users_sub_menu->id) {
                        $class_str = 'class="active"';
                    }                                      
                    
                    $menu_str .= '<li '.$class_str.' id="'.$users_sub_menu->slug.'"><a href="'.$menu_link.'" title="'.$users_sub_menu->name.'">'.$users_sub_menu->name.' '.$arrow_sign.'</a>';

                    $users_final_menus = Menu::getAdminMenu($users_sub_menu->id);
                    if(count($users_final_menus)) { 

                        $menu_str .= '<ul>'; 

                        foreach ($users_final_menus as $users_final_menu){
                            
                            $menu_link = 'javascript:void(0);';
                            if($users_final_menu->menu_type == '1'){
                                $menu_link = $default_lang_code.$users_final_menu->url;
                            }  

                            $class_str = '';                            
                            if($final_menus_id == $users_final_menu->id) {                               
                                $class_str = 'class="active"';
                            } 

                            $menu_str .= '<li '.$class_str.'><a href="'.$menu_link.'" title="'.$users_final_menu->name.'">'.$users_final_menu->name.'</a></li>';
                        }
                        $menu_str .= '</ul>'; 
                    }
                    $menu_str .= '</li>';
                }
                $menu_str .= '</ul></div>'; 
            }
            $menu_str .= '</li>';
        }
        
        return $menu_str;
    } 

    public static function getRoleMenu() {

        $main_menus = Menu::getAdminRoleMenu();

        $menu_str = '';
        
        foreach ($main_menus as $main_menu) {

            $menu_str .= '<ul class="rolelist-check admin_menu_wrapper">
                            <li><label class="check-wrap"><input type="checkbox" name="menu_check[]" value="'.$main_menu->id.'"> <span class="chk-label">'.$main_menu->name.'</span></label>';

                $sub_menus = Menu::getAdminRoleMenu($main_menu->id);
                foreach ($sub_menus as $sub_menu) {
                    $menu_str .= '<ul>
                                    <li><label class="check-wrap"><input type="checkbox" name="menu_check[]" value="'.$sub_menu->id.'"> <span class="chk-label">'.$sub_menu->name.'</span></label>';

                        $final_menus = Menu::getAdminRoleMenu($sub_menu->id);                   
                        foreach ($final_menus as $final_menu) {
                            $menu_str .= '<ul>
                                            <li><label class="check-wrap"><input type="checkbox" name="menu_check[]" value="'.$final_menu->id.'"> <span class="chk-label">'.$final_menu->name.'</span></label>';

                                $finals = Menu::getAdminRoleMenu($final_menu->id);
                                foreach ($finals as $final) {
                                    $menu_str .= '<ul>
                                                    <li><label class="check-wrap"><input type="checkbox" name="menu_check[]" value="'.$final->id.'"> <span class="chk-label">'.$final->name.'</span></label>
                                                    </li>
                                                </ul>';
                                }
                                $menu_str .= '</li>
                            </ul>';
                        }
                        $menu_str .= '</li>
                    </ul>'; 
                }
                $menu_str .= '</li>
            </ul>';  
        }

        return $menu_str;       
    }     

    public static function getRoleMenuEdit($group_id) {

        $role_permisions = MenusPermission::where('role_id', '=', $group_id)->get();
        
        $menu_permision_arr = array();
        foreach($role_permisions as $role_permision){
            $menu_permision_arr[] = $role_permision->menu_id;
        }        

        $menu_str = '';

        $main_menus = Menu::getAdminRoleMenu();
        foreach ($main_menus as $main_menu) {

            $checked = '';
            if(in_array($main_menu->id, $menu_permision_arr)) {
                $checked = 'checked=checked';
            }

            $menu_str .= '<ul class="rolelist-check admin_menu_wrapper">
                            <li><label class="check-wrap"><input type="checkbox" name="menu_check[]" value="'.$main_menu->id.'" '.$checked.'> <span class="chk-label">'.$main_menu->name.'</span></label>';

                $sub_menus = Menu::getAdminRoleMenu($main_menu->id);
                foreach ($sub_menus as $sub_menu) {

                    $checked = '';
                    if(in_array($sub_menu->id, $menu_permision_arr)) {
                        $checked = 'checked=checked';
                    }

                    $menu_str .= '<ul>
                                    <li><label class="check-wrap"><input type="checkbox" name="menu_check[]" value="'.$sub_menu->id.'" '.$checked.'> <span class="chk-label">'.$sub_menu->name.'</span></label>';

                        $final_menus = Menu::getAdminRoleMenu($sub_menu->id);                   
                        foreach ($final_menus as $final_menu) {

                            $checked = '';
                            if(in_array($final_menu->id, $menu_permision_arr)) {
                                $checked = 'checked=checked';
                            }

                            $menu_str .= '<ul>
                                            <li><label class="check-wrap"><input type="checkbox" name="menu_check[]" value="'.$final_menu->id.'" '.$checked.'> <span class="chk-label">'.$final_menu->name.'</span></label>';

                                $finals = Menu::getAdminRoleMenu($final_menu->id);
                                foreach ($finals as $final) {

                                    $checked = '';
                                    if(in_array($final->id, $menu_permision_arr)) {
                                        $checked = 'checked=checked';
                                    }

                                    $menu_str .= '<ul>
                                                    <li><label class="check-wrap"><input type="checkbox" name="menu_check[]" value="'.$final->id.'" '.$checked.'> <span class="chk-label">'.$final->name.'</span></label>
                                                    </li>
                                                </ul>';
                                }
                                $menu_str .= '</li>
                            </ul>';
                        }
                        $menu_str .= '</li>
                    </ul>'; 
                }
                $menu_str .= '</li>
            </ul>';  
        }

        return $menu_str;       
    }

    public static function getRoleMenuDisplay($role_id) {        

        $menu_str = '';
        $i = 0;

        $main_menus = Menu::getAdminRoleMenu(0, $role_id);
        foreach ($main_menus as $main_menu) {

            $menu_str .= '<div class="rolemenu-row">
                <span class="menu-num">'.++$i.'</span>
                <ul class="menulist">
                    <li><a href="javascript:void(0)">'.$main_menu->name.'<i class="glyphicon glyphicon-menu-down"></i></a>';

            $sub_menus = Menu::getAdminRoleMenu($main_menu->id, $role_id);
            if(count($sub_menus) > 0) {
                $menu_str .= '<ul class="submenulist">';
                foreach ($sub_menus as $sub_menu) {
                    $menu_str .= '<li> <a href="javascript:void(0)">'.$sub_menu->name.'</a>';

                    $final_menus = Menu::getAdminRoleMenu($sub_menu->id, $role_id); 
                    if(count($sub_menus) > 0) { 
                        $menu_str .= '<ul class="submenulist">';                 
                        foreach ($final_menus as $final_menu) {
                            $menu_str .= '<li> <a href="javascript:void(0)">'.$final_menu->name.'</a>';

                            $finals = Menu::getAdminRoleMenu($final_menu->id, $role_id);
                            if(count($sub_menus) > 0) {
                                $menu_str .= '<ul class="submenulist">';
                                foreach ($finals as $final) {
                                    $menu_str .= '<li> <a href="javascript:void(0)">'.$final->name.'</a></li>';
                                }
                                $menu_str .= '</ul>';
                            }
                            $menu_str .= '</li>';
                        }
                        $menu_str .= '</ul>';
                    }
                    $menu_str .= '</li>'; 
                }
                $menu_str .= '</ul>';
            }
            $menu_str .= '</li></ul></div>';  
        }

        return $menu_str;       
    }                         

    public static function getCurrencyDorpDown($currency_id=null,$currency_id_arr=array()) {

        $currency_lists = \App\Currency::select('id', 'currency_code')->where('status','1')->get();
        
        $currency_str = '';
        
        foreach($currency_lists as $currency)
        {            
           $selected = ''; 
            
           if($currency->id == $currency_id || in_array($currency->id, $currency_id_arr)) {
              
               $selected = 'selected="selected"'; 
           }
            
           $currency_str .= '<option value="'.$currency->id.'" '.$selected.'>'.$currency->currency_code.'</option>';
        } 
        
        //echo '====>'.$country_str;die;
        
        return $currency_str;
    }

    public static function getOfflinePaymentOption($payment_option_id=null) {

        $offline_pay_opt_lists = \App\PaymentOption::select('id')->where(['status'=>'1', 'payment_type'=>'2'])->get();
        
        $pay_opt_str = '';
        
        foreach($offline_pay_opt_lists as $pay_opt)
        {            
           $selected = ''; 
            
           if($pay_opt->id == $payment_option_id || $pay_opt->id == old('payment_option_id')) {
              
               $selected = 'selected="selected"'; 
           }
            
           $pay_opt_str .= '<option value="'.$pay_opt->id.'" '.$selected.'>'.$pay_opt->paymentOptName->payment_option_name.'</option>';
        } 
        
        //echo '====>'.$country_str;die;
        
        return $pay_opt_str;
    }

    public static function textWithEditLanuage($fieldType, $name, $tablename, $table_id, $table_field, $edtorClass=null, $errors=null, $errorkey=null, $validatorClass=null) {

        //return 'Amit';

        $languages = Language::where('status', '1')->orderBy('isDefault', 'desc')->get();
        $genTable = '';

        $datas = DB::table($tablename)->select($name, 'lang_id')->where([$table_field => $table_id])->get();
        $langdata = array();
        foreach ($datas as $data) {
            if (!empty($data->lang_id)) {
                $langdata[$name][$data->lang_id] = $data->$name;
            }
        }
        foreach ($languages as $language) {
            
            $field_class = '';
            $error_class = '';
            
            if($language->isDefault == '1' && !empty($validatorClass)) {
                $field_class = $validatorClass;
                $error_class = 'has-error';
            }            

            $genTable .= '<div class="form-group">';
            $genTable .= '<div class="col-sm-2">';
            $genTable .= !empty($language->languageFlag) ?
                    '<img src="' . Config::get('constants.language_url') . $language->languageFlag . '" width="20" height="20" '
                    . 'title="' . $language->languageName . '" class="pull-right">' : '';
            $genTable .= '</div>';
            $genTable .= '<div class="col-sm-10 '.$error_class.'">';
            $genTable .= Form::$fieldType($name . '[' . $language->id . ']', old($name . '[' . $language->id . ']', isset($langdata[$name][$language->id]) ? $langdata[$name][$language->id] : ""), ['class' => 'form-control '.$edtorClass.' '.$field_class, 'placeholder' => '']);

            if($language->isDefault == '1'){
             if(count($errors)>0 && !empty($errorkey)){
                if($errors->first($errorkey)){
                  $genTable .='<p id="name-error" class="error error-msg">'.$errors->first($errorkey).'</p>';
                }
              
             }

            }  
            $genTable .= '</div>';
            $genTable .= '</div>';
        }
        return $genTable;
    }    

    public static function fieldstabWithLanuage($inputfielddatas, $tabseq = null, $errors=null, $is_angular = null ) {
        

        $lang_prefered = explode(',', session('lang_prefered')); 

        $default_lang = session('admin_default_lang');

        $languages = Language::where('status', '1')->orderBy('id', 'asc')
         ->orderByRaw(DB::raw("FIELD(id, $default_lang) DESC"))
        ->get();



        //dd($languages);

        

        $langTab = '<ul class="nav nav-tabs lang-nav-tabs">';
        $genTable = '<div class="tab-content language-tab">';
        $i = 1;
        foreach ($languages as $language) {    
            $viewtab = '';

            if(!empty(session('lang_prefered'))) {
                if(!in_array($language->id, $lang_prefered)){
                   $viewtab = 'style="display:none"';  //hide tab of which lang no selected
                }
            }

            $langTab .= '<li  class="' . (($default_lang == $language->id) ? 'active' : '') . ' tablang_'.$tabseq . $language->id .'" '.$viewtab.'  ><a data-toggle="tab" href="#lang' . $tabseq . $language->id . '">';
            $langTab .= !empty($language->languageFlag) ?
                    '<img src="' . Config::get('constants.language_url') . $language->languageFlag . '" width="29" height="29" '
                    . 'title="' . $language->languageName . '">' : '';
            $langTab .= '</a></li>';
            $genTable .= '<div id="lang' . $tabseq . $language->id . '" class="tab-pane fade in ' . (($default_lang == $language->id) ? 'active' : '') . '"'.$viewtab.'>';
            foreach ($inputfielddatas as $inputfielddata) {
                $field = isset($inputfielddata['field']) ? $inputfielddata['field'] : 'text';
                $cssClass = isset($inputfielddata['cssClass']) ? $inputfielddata['cssClass'] : '';
                $placeHolder = isset($inputfielddata['label']) ? ucwords($inputfielddata['label']) : '';
                $name = isset($inputfielddata['name']) ? $inputfielddata['name'] : 'name';
                $required_filed = isset($inputfielddata['required']) ? $inputfielddata['required'] : '';

                $froalaOptions = $froala = 'null';
                if(isset($inputfielddata['froala'])){
                    $froala = 'froala';
                } 
                $errorkey=isset($inputfielddata['errorkey']) ? $inputfielddata['errorkey'] : '';
               
                $error_class=""; 
                $required = "";
                $name_required = '';
                if($language->isDefault == '1'){
                    if(!empty($errorkey)){
                        if($errors->first($errorkey))
                        {
                                $error_class="error";
                        }    
                    }

                    if(!empty($required_filed)){
                       // $required = "'required'".'=>'."'".$required_filed."'";
                        //dd($required);
                        $required = $required_filed;
                    }


                }
                
                



                $genTable .= '<div class="form-row '.$error_class.'" >';
                $genTable .= isset($inputfielddata['label']) ? '<label>' . $inputfielddata['label'] . '</label>' : '';

                //dd(!empty($is_angular));
                if(!empty($is_angular)){

                    if($field=='textarea'){
                        $genTable .= Form::$field($name.'['.$language->id.']', old($name . '[' . $language->id . ']'), ['ng-model'=>$name.'[' . $language->id . ']','class' => 'form-control ' . $cssClass, 'placeholder' => '', $froala=>'' ]); 
                    }
                    else{
                        if($language->id == session('default_lang')){
                            $genTable .= Form::$field($name.'_'.$language->id, old($name . '[' . $language->id . ']'), ['ng-model'=>$name.'['.$language->id.']','class' => 'form-control ' . $cssClass, 'placeholder' => '','required'=>'required']);

                            $genTable .= '<span ng-show="(productform.productName_'.$language->id.'.$touched || productform.$submitted) && productform.productName_'.$language->id.'.$error.required" class="error-msg block">'.
                                    Lang::get('product.required').'</span>';
                        }
                        else{
                            $genTable .= Form::$field($name.'_'.$language->id, old($name . '[' . $language->id . ']'), ['ng-model'=>$name.'['.$language->id.']','class' => 'form-control ' . $cssClass, 'placeholder' => '']);
                        }                             
                    }


                }else {
                  
                    if($field=='textarea'){
                        
                        $genTable .= Form::$field($name.'['.$language->id.']', old($name . '[' . $language->id . ']'), ['ng-model'=>$name.'[' . $language->id . ']','class' => 'form-control ' . $cssClass, 'placeholder' => '', $froala=>'' ]);
                    }
                    else{
                        
                        $genTable .= Form::$field($name.'['.$language->id.']', old($name . '[' . $language->id . ']'), ['ng-model'=>$name.'['.$language->id.']','class' => 'form-control '. $cssClass, 'placeholder' => '',$required]);    
                    }
                }
                
               
                if($language->isDefault == '1'){
                    if(count($errors)>0 && !empty($errorkey)){
                        if($errors->first($errorkey)){
                            $genTable .='<p id="name-error" class="red">'.$errors->first($errorkey).'</p>';
                        }
                    }
                }                    
                $genTable .= '</div>';
            }
            $genTable .= '</div>';
            $i++;
        }

        $genTable .= '</div>';
        $langTab .= '</ul>';

        return $langTab . $genTable;
    }

    public static function fieldstabWithLanuageEdit($inputfielddatas, $tabseq = null, $table_field, $table_id, $tableName, $errors=null, $is_angular=null) {
        $fetchField = array();
        foreach ($inputfielddatas as $fieldName) {
            $fetchField[] = $fieldName['name'];
        }
        $fetchField[] = 'lang_id';
        $datas = DB::table($tableName)->select($fetchField)->where([$table_field => $table_id])->get();
        $langdata = array();
        foreach ($datas as $data) {
            if (!empty($data->lang_id)) {
                foreach ($fetchField as $fieldName) {
                    $langdata[$fieldName][$data->lang_id] = $data->$fieldName;
                }
            }
        }

       $default_lang = session('admin_default_lang');
       $languages = Language::where('status', '1')->orderBy('id', 'asc')
         ->orderByRaw(DB::raw("FIELD(id, $default_lang) DESC"))
        ->get();
        
        $langTab = '<ul class="nav nav-tabs lang-nav-tabs">';
        $genTable = '<div class="tab-content language-tab">';
        $i = 1;
        foreach ($languages as $language) {
            $langTab .= '<li class="' . ($i == 1 ? 'active' : '') . ' tablang_'.$tabseq . $language->id.'"><a data-toggle="tab" href="#lang' . $tabseq . $language->id . '">';
            $langTab .= !empty($language->languageFlag) ?
                    '<img src="' . Config::get('constants.language_url') . $language->languageFlag . '" width="29" height="29" '
                    . 'title="' . $language->languageName . '">' : '';
            $langTab .= '</a></li>';
            $genTable .= '<div id="lang' . $tabseq . $language->id . '" class="tab-pane fade in ' . ($i == 1 ? 'active' : '') . '">';
            foreach ($inputfielddatas as $inputfielddata) {
                $field = isset($inputfielddata['field']) ? $inputfielddata['field'] : 'text';
                $cssClass = isset($inputfielddata['cssClass']) ? $inputfielddata['cssClass'] : '';
                $placeHolder = isset($inputfielddata['label']) ? ucwords($inputfielddata['label']) : '';
                $name = isset($inputfielddata['name']) ? $inputfielddata['name'] : 'name';
                $required_filed = isset($inputfielddata['required']) ? $inputfielddata['required'] : '';

                $froalaOptions = $froala = 'null';
                if(isset($inputfielddata['froala'])){
                    $froala = 'froala';
                } 
                $errorkey=isset($inputfielddata['errorkey']) ? $inputfielddata['errorkey'] : '';
               

                $error_class=""; 
                $required = "";
                $name_required = '';
                if($language->isDefault == '1'){
                    if(!empty($errorkey)){
                        if($errors->first($errorkey))
                        {
                                $error_class="error";
                        }    
                    }

                    if(!empty($required_filed)){
                       // $required = "'required'".'=>'."'".$required_filed."'";
                        //dd($required);
                        $required = $required_filed;
                    }


                }
                

                
                $genTable .= '<div class="form-row '.$error_class.'">';
                $genTable .= isset($inputfielddata['label']) ? '<label>' . $inputfielddata['label'] . '</label>' : '';

                $fld_value = isset($langdata[$name][$language->id])?stripslashes($langdata[$name][$language->id]):'';

                if($is_angular=='angular'){

                    $froala = '';
                    if($field=='textarea'){
                        $froala = 'froala';
                    }

                    $genTable .= Form::$field($name.'['.$language->id.']', old($name . '[' . $language->id . ']', $fld_value), ['ng-model'=>$name.'[' . $language->id . ']', 'ng-init'=>$name.$language->id."='".$fld_value."'", 'class' => "form-control" . $cssClass, 'placeholder'=>'', $froala=>'']);    
                }
                else{

                    $froala = '';
                    if($field=='textarea'){
                        $froala = 'froala-editor-apply';
                    }

                    $genTable .= Form::$field($name . '[' . $language->id . ']', old($name . '[' . $language->id . ']', isset($langdata[$name][$language->id]) ? stripslashes($langdata[$name][$language->id]) : ""), ['class' => "form-control $froala" . $cssClass, 'placeholder' => '' ]);
                }

                $errorkey=isset($inputfielddata['errorkey']) ? $inputfielddata['errorkey'] : '';
                if($language->isDefault == '1'){
                    if(count($errors)>0 && !empty($errorkey)){
                        if($errors->first($errorkey)){
                            $genTable .='<p id="name-error" class="red">'.$errors->first($errorkey).'</p>';
                        }
                    }
                } 
                
                $genTable .= '</div>';
            }
            $genTable .= '</div>';
            $i++;
        }

        $genTable .= '</div>';
        $langTab .= '</ul>';

        return $langTab . $genTable;
    }         

    public static function SabinaCss(){

        $css = '<link rel="stylesheet" type="text/css" href="'.Config::get('constants.theme_url').'sabina/css/bootstrap.css" /> 
                <link rel="stylesheet" type="text/css" href="'.Config::get('constants.theme_url').'sabina/css/global.css" />   
                <link rel="stylesheet" type="text/css" href="'.Config::get('constants.theme_url').'sabina/css/style-front.css" /> 
                <link rel="stylesheet" type="text/css" href="'.Config::get('constants.theme_url').'sabina/css/fonts.css" />
               <link rel="stylesheet" media="screen and (max-width: 1023px)" type="text/css" href="'.Config::get('constants.theme_url').'sabina/css/jquery.mmenu.all.css" />     
                <link rel="stylesheet" type="text/css" href="'.Config::get('constants.theme_url').'sabina/css/font-awesome.css" />
                <link rel="stylesheet" type="text/css" href="'.Config::get('constants.css_url').'jquery-ui.css" />
                ';
        echo $css;
    }

    public static function SabinaJs(){

        $js = '<script src="'.Config::get('constants.js_url').'jquery.min.js"></script> 
               <script src="'.Config::get('constants.js_url').'bootstrap.min.js" ></script>
               <script src="'.Config::get('constants.theme_url').'sabina/js/jquery.mmenu.all.min.js"></script>
               <script src="'.Config::get('constants.js_url').'jquery-ui.min.js"></script>
               <script src="'.Config::get('constants.js_url').'toastr.min.js"></script>
               <script src="'.Config::get('constants.theme_url').'sabina/js/custom.js"></script>
               <script src="'.Config::get('constants.js_url').'common.js" ></script>';
        echo $js;
    }

   public static function texttabWithLanuageMultiArrayChangeDesign($inputfielddatas, $tabseq=null, $text_color_image=null, $errors =null) {

        
        $default_lang = session('admin_default_lang');
        $languages = Language::where('status', '1')->orderBy('id', 'asc')
         ->orderByRaw(DB::raw("FIELD(id, $default_lang) DESC"))
        ->get();

 
        $langdatas = old('values');

        $langdataslen = count($langdatas[$default_lang]);
        $color_codes = old('color_code');
        $positions = old('position');
        $p=0;
        $color_code_old = $position_old = [];
        if(!empty($color_codes)){
            foreach($color_codes as $colors){
              $color_code_old[$p] = $colors; 
              $p++; 
            }
        }
        $p=0;
        if(!empty($positions)){
            foreach($positions as $position){
              $position_old[$p] = $position; 
              $p++; 
            }
        }

        
        if(!empty($langdatas)){
          $html = '';

          //$html = str_repeat(' ', 1000);  
          for($j=0; $j<$langdataslen; $j++) { 
             $i = 1;
             $class = $j==0?'original':'cloneData';

             $maindiv = '<div class="'.$class.' rows row"><div class="col-sm-6"><div class="row">';
             $langTab = '<ul class="tab-list">';
             $genTable = '<div class="tab-content">';
             //$i = $j = 1;
             foreach ($languages as $language) {

                $langTab .= '<li class="' . ($i == 1 ? 'active' : '') . ' tablang_'.$tabseq . $language->id .'"><a data-toggle="tab" href="#lang' . $tabseq . $language->id .'_'.$i.'">';
                $langTab .= !empty($language->languageFlag) ?
                        '<img src="' . Config::get('constants.language_url') . $language->languageFlag . '" width="29" height="29" '
                        . 'title="' . $language->languageName . '">' : '';
                $langTab .= '</a></li>';
                $genTable .= '<div id="lang' . $tabseq . $language->id . '_'.$i.'" class="tab-pane fade in ' . ($i == 1 ? 'active' : '') . '">';
                foreach ($inputfielddatas as $inputfielddata) {
                    $field = isset($inputfielddata['field']) ? $inputfielddata['field'] : 'text';
                    $cssClass = isset($inputfielddata['cssClass']) ? $inputfielddata['cssClass'] : '';
                    $placeHolder = isset($inputfielddata['label']) ? ucwords($inputfielddata['label']) : '';
                    $name = isset($inputfielddata['name']) ? $inputfielddata['name'] : 'name';
                    $genTable .= '<div class="form-row"><div class="box1">';
                    $genTable .= isset($inputfielddata['label']) ? '<label>' . $inputfielddata['label'] . '</label>' : '';
                    $genTable .= Form::$field($name . '[' . $language->id . ']['.$j.']', old($name . '[' . $language->id . ']['.$j.']'), ['class' => 'form-control ' . $cssClass, 'placeholder' => '']);
                     $genTable .= '</div></div>';
                     $errorskey = 'attribute.'. $j.'.'.$language->id.'.value';
                     if ($errors->has($errorskey)){       
                        $genTable .= '<p id="name-error" class="error error-msg">'.$errors->first($errorskey).'</p>';
                     }
                 }
                 $genTable .= '</div>';
                 $i++;
             }

            $langTab .= '<li class="lang-label">'.Lang::get('attribute.values').'</li></ul>';
            $genTable .= '</div></div></div>';

            if(!empty($text_color_image) ){
               
                $attribute_type_value = old('attribute_type_value');
                $color_picker_show = ' style="display:none"';
                if($attribute_type_value == 'text_color_image') $color_picker_show = ''; 
                
                $genTable .= '<div class="col-sm-3 push-col color_picker" '.$color_picker_show.'>
                              <div class="form-row color_code">
                                <label>'.Lang::get('attribute.color_code').'<i class="red">*</i><div class="input-inline"><label class="radio-wrap">'.Form::Radio('select_color_or_image['.$j.']', '1', true,['class' => 'select_color_or_image']).'<span class="radio-label"></span></label><div class="color_picker_2">';
                //$genTable .=  Form::text('color_code['.$j.']', old('color_code', '#000'), ['placeholder'=>'', 'class'=>'form-control']);
                
                $color_code_old_val = isset($color_code_old[$j])?$color_code_old[$j]:'#000';

                $genTable .=  '<input placeholder="" class="form-control" name="color_code['.$j.']" value="'.$color_code_old_val.'" type="text"> ';                  
                                             
                $genTable .= '<span class="input-group-addon coloraddon"><i></i></span></div></div></div><div class="mt-5">
                     <label>'.Lang::get('attribute.color_image').'<i class="red">*</i></label><div class="input-inline"><label class="radio-wrap">'.Form::Radio('select_color_or_image['.$j.']', '2', false, ['class' => 'select_color_or_image']).'<span class="radio-label"></span></label>
                     <div class="file-wrapper">
                      <span class="add-files">
                         <img src="images/browse-btn3.png" width="" height="38">
                      </span>';
                      $genTable .= Form::file('color_file['.$j.']', ['class'=>'form-control', 'disabled'=>'disabled']);
                      $genTable .= '</div></div></div>';

                      $errorsimg_colorkey = 'img_color.'. $j.'.color_image';
                      if ($errors->has($errorsimg_colorkey)){       
                            $genTable .= '<p id="name-error" class="error error-msg">'.$errors->first($errorsimg_colorkey).'</p>';
                      }

                      $errorsimg_colorkey = 'img_color.'. $j.'.color_code';
                      if ($errors->has($errorsimg_colorkey)){       
                            $genTable .= '<p id="name-error" class="error error-msg">'.$errors->first($errorsimg_colorkey).'</p>';
                      }


                    $genTable .= '</div>'; 

                }

                $genTable .= '<div class="col-sm-1 push-col"><div class="form-row position"><label>'.Lang::get('attribute.position').'</label>';
                //$genTable .=  Form::text('position[]', old('position[]'), ['placeholder'=>'', 'class'=>'form-control']);
                $position_old_val = isset($position_old[$j])?$position_old[$j]:'0';
                $genTable .= '<input placeholder="" class="form-control" name="position['.$j.']" value="'.$position_old_val.'" type="text">';


                $genTable .= '</div></div>';

                $genTable .= '<div class="col-sm-1 push-col nopadding"><label>'.Lang::get('attribute.is_default').'</label>'; 
                $genTable .= Form::Radio('default_value', $j, old('default_value') == $j+1);
                $genTable .= '</div>';

                $genTable .='<div class="col-sm-1 tab-top actionsClone"></div></div>';
                
                $html .= $maindiv.$langTab.$genTable;
            }   
            return $html;

        }else{
        
        $maindiv = '<div class="original rows row"><div class="col-sm-6"><div class="row">';
        $langTab = '<ul class="tab-list">';
        $genTable = '<div class="tab-content">';
        $i = $j = 1;
        foreach ($languages as $language) {

            $langTab .= '<li class="' . ($i == 1 ? 'active' : '') . ' tablang_'.$tabseq . $language->id .'"><a data-toggle="tab" href="#lang' . $tabseq . $language->id .'_'.$i.'">';
            $langTab .= !empty($language->languageFlag) ?
                    '<img src="' . Config::get('constants.language_url') . $language->languageFlag . '" width="29" height="29" '
                    . 'title="' . $language->languageName . '">' : '';
            $langTab .= '</a></li>';
            $genTable .= '<div id="lang' . $tabseq . $language->id . '_'.$i.'" class="tab-pane fade in ' . ($i == 1 ? 'active' : '') . '">';
            foreach ($inputfielddatas as $inputfielddata) {
                $field = isset($inputfielddata['field']) ? $inputfielddata['field'] : 'text';
                $cssClass = isset($inputfielddata['cssClass']) ? $inputfielddata['cssClass'] : '';
                $placeHolder = isset($inputfielddata['label']) ? ucwords($inputfielddata['label']) : '';
                $name = isset($inputfielddata['name']) ? $inputfielddata['name'] : 'name';
                $genTable .= '<div class="form-row"><div class="box1">';
                $genTable .= isset($inputfielddata['label']) ? '<label>' . $inputfielddata['label'] . '</label>' : '';
                $genTable .= Form::$field($name . '[' . $language->id . '][]', old($name . '[' . $language->id . '][]'), ['class' => 'form-control ' . $cssClass, 'placeholder' => '']);

                $genTable .= '</div></div>';
                  
                 

             }
             $genTable .= '</div>';
             $i++;
        }

        $langTab .= '<li class="lang-label">'.Lang::get('attribute.values').'</li></ul>';
        $genTable .= '</div></div></div>';
        
         if(!empty($text_color_image)){
            $genTable .= '<div class="col-sm-3 push-col color_picker" style="display:none">
                          <div class="form-row color_code">
                            <label>'.Lang::get('attribute.color_code').'<i class="red">*</i><div class="input-inline"><label class="radio-wrap">'.Form::Radio('select_color_or_image[]', '1', true,['class' => 'select_color_or_image']).'<span class="radio-label"></span></label><div class="color_picker_2">';
            $genTable .=  Form::text('color_code[]', old('color_code', '#000'), ['placeholder'=>'', 'class'=>'form-control']);
                                         
            $genTable .= '<span class="input-group-addon coloraddon"><i></i></span></div></div></div><div class="mt-5">
                 <label>'.Lang::get('attribute.color_image').'<i class="red">*</i></label><div class="input-inline"><label class="radio-wrap">'.Form::Radio('select_color_or_image[]', '2', false, ['class' => 'select_color_or_image']).'<span class="radio-label"></span></label>
                 <div class="file-wrapper">
                  <span class="add-files">
                     <img src="images/browse-btn3.png" width="" height="38">
                  </span>';
                  $genTable .= Form::file('color_file[]', ['class'=>'form-control', 'disabled'=>'disabled']);
                  $genTable .= '</div></div></div></div>'; 

            }

            $genTable .= '<div class="col-sm-1 push-col"><div class="form-row position"><label>'.Lang::get('attribute.position').'</label>';
            $genTable .=  Form::text('position[]', '0', ['placeholder'=>'', 'class'=>'form-control']);
            $genTable .= '</div></div>';

            $genTable .= '<div class="col-sm-1 push-col nopadding"><label>'.Lang::get('attribute.is_default').'</label>'; 
            $genTable .= Form::Radio('default_value', '1', true);
            $genTable .= '</div>';

            $genTable .='<div class="col-sm-1 tab-top actionsClone"></div>';


           return $maindiv.$langTab.$genTable;

        }
         




    } 

    public static function texttabWithLanuageNewsletterMultiArrayChangeDesign($inputfielddatas, $tabseq=null, $text_color_image=null, $errors =null) {

        
        $default_lang = session('admin_default_lang');
        $languages = Language::where('status', '1')->orderBy('id', 'asc')
         ->orderByRaw(DB::raw("FIELD(id, $default_lang) DESC"))
        ->get();

        
        
        $genTable = '';
        $langTab = '';
        $maindiv = '';
        $allcon = '';

             $j= 1;
        

             $class = $j==1?'original':'cloneData';

             $maindiv = '<div class="'.$class.' rows row">';
             
             $maindiv .= '<div class="col-sm-6"><div class="row">';

             $langTab = '<ul class="tab-list">';
             $genTable = '<div class="tab-content">';

             $genTable .= Form::hidden('attr_val_id', old($tabseq, $tabseq), ['id' => 'attr_val_id']);
             
             $i =1;
             foreach ($languages as $language) {
                
                $langTab .= '<li class="' . ($i == 1 ? 'active' : '') . ' tablang_'.$tabseq . $language->id .'"><a data-toggle="tab" href="#lang' . $tabseq . $language->id .$j. '">';
                $langTab .= !empty($language->languageFlag) ?
                        '<img src="' . Config::get('constants.language_url') . $language->languageFlag . '" width="29" height="29" '
                        . 'title="' . $language->languageName . '" class="pull-right">' : '';
                $langTab .= '</a></li>';
                
                $genTable .= '<div id="lang' . $tabseq . $language->id .$j. '" class="tab-pane fade in ' . ($i == 1 ? 'active' : '') . '">';
                $genTable .= Form::text('values[1][' . $language->id . ']', '', ['class' => 'form-control ', 'placeholder' => '']);
                $genTable .= '</div><div class="search_data" style="display: none"><a href="#">'.'</a></div>';


                $i++;
            }
            $genTable .= '</div></div></div>';

            $genTable .='<!--div class="col-sm-1 push-col actionsClone">';
            if($j == 1) {
                $genTable .= '<a href="#" class="add-clone"><i class="glyphicon glyphicon-plus"></i></a>';
            } else {
                $genTable .= '<a href="#" class="minus-clone"><i class="glyphicon glyphicon-minus"></i></a>';
            }
            $genTable .= '</div-->';


                 $genTable .= '<div class="col-sm-1 push-col"><div class="form-row position">
                                 <label>Position</label>';
                 $genTable .=  Form::text('position[]', '', ['placeholder'=>'', 'class'=>'form-control']);
                                                 
                 $genTable .= '</div></div>';

                  $genTable .= '<div class="col-sm-1"><label>'.Lang::get('attribute.is_default').'</label>'; 
                  $genTable .= Form::radio('default_value', $j, '');
                  $genTable .= '</div>';



                 
                 if ($j == 1) {

                     $genTable .='<div class="col-sm-1 push-col actionsClone">';
                     
                    

                     $genTable .= '</div>';

               }else{

                   $genTable .='<div class="col-sm-1 actionsClone">';
                     
                     $genTable .= '<a href="#" class="minus-clone">
                                    <i class="glyphicon glyphicon-remove icon-close"></i>
                                   </a>';

                  $genTable .= '</div>';

               }

             

            $genTable .= '</div>';
           
            $langTab .= '<li class="lang-label">values</li></ul>';
            $allcon .= $maindiv.$langTab.$genTable;
        
        return $allcon;


    }

    public static function textTabWithEditLanuageMultiArrayChangeDesign($fieldType, $name, $tablename, $table_id, $table_field, $tabseq = null, $edtorClass = null, $text_color_image = null, $search = null, $errors=null) {

        
        
        $default_lang = session('admin_default_lang');
        $languages = Language::where('status', '1')->orderBy('id', 'asc')
         ->orderByRaw(DB::raw("FIELD(id, $default_lang) DESC"))
        ->get();

        $genTable = '';
        $langTab = '';
        $maindiv = '';
        $allcon = '';

        

        if(!empty($search)){

           $datasall = AttributeValue::where([$table_field => $table_id])

           ->whereIn('id', $search)

           ->with('attrvalDetails')->orderBy('position','ASC')->get();

        }else{
          
           $datasall = AttributeValue::where([$table_field => $table_id])->with('attrvalDetails')->orderBy('position','ASC')->get();

        }
        
        //dd($datasall);
        $langdatas = array();
        $colorimages = array(); 
        $colorcode = array();
        $default_selected = array();
        $select_color_or_image = array();
        

        /*$langdatas = old('values');
        if(empty($langdatas)){
          foreach ($datasall as $datas) {
            foreach ($datas->attrvalDetails as $key => $data) {
                if (!empty($data->lang_id)) {
                    $langdatas[$data->attr_val_id][$data->lang_id] = $data->$name;
                    
                }
               
            }
            $colorimages[$datas->id] =  $datas->color_image;
            $colorcode[$datas->id] = $datas->color_code;
            $position[$datas->id] = $datas->position;
            $select_color_or_image[$datas->id] = $datas->select_color_or_image;
            $default_selected[$datas->id] = $datas->default_value;

         }
       }else{

         
          //dd($langdatas);
          
          $p = 1;
          //$select_color_or_image = $position = $colorcode = [];
          $select_color_or_image_old = old('select_color_or_image');
          $positions_old = old('position');
          $color_codes_old = old('color_code');
         // dd($color_codes_old);
          foreach ($langdatas as $key => $langdata){   
             $default_selected[$key] = old('default_value') == $p?'1':'0';
             $select_color_or_image[$key] = $select_color_or_image_old[$key];
             $position[$key] = $positions_old[$key]; 
             $colorcode[$key] = isset($color_codes_old[$key])?$color_codes_old[$key]:'';
             //$colorimages[$key] =  '';
             $p++;
          }
         
      }*/

      foreach ($datasall as $datas) {
            foreach ($datas->attrvalDetails as $key => $data) {
                if (!empty($data->lang_id)) {
                    $langdatas[$data->attr_val_id][$data->lang_id] = $data->$name;
                    
                }
               
            }
            $colorimages[$datas->id] =  $datas->color_image;
            $colorcode[$datas->id] = $datas->color_code;
            $position[$datas->id] = $datas->position;
            $default_selected[$datas->id] = $datas->default_value;
            $select_color_or_image[$datas->id] = $datas->select_color_or_image;


        }

        $langdatasold = old('values');

        if(!empty($langdatasold)){  
          $p = 1;
          $select_color_or_image_old = old('select_color_or_image');
          $positions_old = old('position');
          $color_codes_old = old('color_code');
          foreach ($langdatasold as $key => $langdata){   
             $default_selected[$key] = old('default_value') == $p?'1':'0';
             $select_color_or_image[$key] = $select_color_or_image_old[$key];
             $position[$key] = $positions_old[$key]; 
             $colorcode[$key] = isset($color_codes_old[$key])?$color_codes_old[$key]:'';
             //$colorimages[$key] =  '';
             $p++;
          }
          $langdatas = $langdatasold;
        }
      
        $j= 1;
        foreach ($langdatas as $key => $langdata) {

             $class = $j==1?'original':'cloneData';
             //$class = 'cloneData';
             $maindiv = '<div class="'.$class.' rows row">';
             /*$maindiv .= '<div class="col-sm-1"><label class="radio-wrap pull-right">';
             $maindiv .= Form::radio('default_value', $j, $default_selected[$key]);
             $maindiv .= '<span class="radio-label"> </span></label></div>';*/
             $maindiv .= '<div class="col-sm-6"><div class="row">';

             $langTab = '<ul class="tab-list">';
             $genTable = '<div class="tab-content">';

             $genTable .= Form::hidden('attr_val_id[]', $key, ['id' => 'attr_val_id']);
             $i =1;
             foreach ($languages as $language) {
                
                $langTab .= '<li class="' . ($i == 1 ? 'active' : '') . ' tablang_'.$tabseq . $language->id .'"><a data-toggle="tab" href="#lang' . $tabseq . $language->id .$j. '">';
                $langTab .= !empty($language->languageFlag) ?
                        '<img src="' . Config::get('constants.language_url') . $language->languageFlag . '" width="29" height="29" '
                        . 'title="' . $language->languageName . '" class="pull-right">' : '';
                $langTab .= '</a></li>';
                
                $genTable .= '<div id="lang' . $tabseq . $language->id .$j. '" class="tab-pane fade in ' . ($i == 1 ? 'active' : '') . '">';
                $genTable .= Form::$fieldType($name . '['.$key.'][' . $language->id . ']', old($name . '['.$key.'][' . $language->id . ']', isset($langdata[$language->id]) ? $langdata[$language->id] : ""), ['class' => 'form-control ' . $edtorClass, 'placeholder' => '']);

                $errorskey = 'attribute.'. $j.'.'.$language->id.'.value';
                if (!empty($errors->has($errorskey))){       
                   $genTable .= '<p id="name-error" class="error error-msg">'.$errors->first($errorskey).'</p>';
                }
                $genTable .= '</div><div class="search_data" style="display: none"><a href="#">'.(isset($langdata[$language->id]) ? $langdata[$language->id] : "").'</a></div>';


                $i++;
            }
            $genTable .= '</div></div></div>';
            //$genTable .= '<label class="radio-wrap pull-right">';
            //$genTable .= Form::radio('default_value', $j, $default_selected[$key]);
            //$genTable .= '<span class="radio-label "> </span></label>';
            $genTable .='<!--div class="col-sm-1 push-col actionsClone">';
            if($j == 1) {
                $genTable .= '<a href="#" class="add-clone"><i class="glyphicon glyphicon-plus"></i></a>';
            } else {
                $genTable .= '<a href="#" class="minus-clone"><i class="glyphicon glyphicon-minus"></i></a>';
            }
            $genTable .= '</div-->';

           if(!empty($text_color_image)){
                  
                  $genTable .= '<div class="col-sm-3 push-col color_picker">
                                  <div class="form-row color_code">
                                    <label>'.Lang::get('attribute.color_code').'<i class="red">*</i><div class="input-inline"><label class="radio-wrap">'.Form::Radio('select_color_or_image['.$key.']', '1', $select_color_or_image[$key]==1?true:false,['class' => 'select_color_or_image']).'<span class="radio-label"></span></label><div class="color_picker_2">';
                   $genTable .=  Form::text('color_code['.$key.']', isset($colorcode[$key])?$colorcode[$key]:'', ['placeholder'=>'', 'class'=>'form-control', 'disabled'=> $select_color_or_image[$key] == '2'?true:false]);
                                                 
                    $genTable .= '<span class="input-group-addon coloraddon"><i style="background-color:'.(isset($colorcode[$key])&&!empty($colorcode[$key])?$colorcode[$key]:'#000').'"></i></span></div></div></div><div class="mt-5">
                         <label>'.Lang::get('attribute.color_image').'<i class="red">*</i></label><div class="input-inline"><label class="radio-wrap">'.Form::Radio('select_color_or_image['.$key.']', '2', $select_color_or_image[$key]==2?true:false, ['class' => 'select_color_or_image']).'<span class="radio-label"></span></label>
                         <div class="file-wrapper">
                           <span class="add-files">';
                             if(isset($colorimages[$key]) && !empty($colorimages[$key])){
                                
                                $genTable .= '<img src="' . Config::get('constants.color_url') . $colorimages[$key]. '" width="" height="38" class="switherimage">';

                                $genTable .= Form::hidden('colorimageold['.$key.']', $colorimages[$key]);



                            }else{

                              $genTable .= '<img height="38" width="38" src="images/browse-btn3.png"/>';

                           }

                          $genTable .='</span>';
                        
                          $genTable .= Form::file('color_file['.$key.']', ['class'=>'form-control', 'disabled'=> $select_color_or_image[$key] == '1'?true:false]);
                          $genTable .= '</div></div></div>';

                          $errorsimg_colorkey = 'img_color.'. $j.'.color_image';
                          if ($errors->has($errorsimg_colorkey)){       
                                $genTable .= '<p id="name-error" class="error error-msg">'.$errors->first($errorsimg_colorkey).'</p>';
                          }

                          $errorsimg_colorkey = 'img_color.'. $j.'.color_code';
                          if ($errors->has($errorsimg_colorkey)){       
                                $genTable .= '<p id="name-error" class="error error-msg">'.$errors->first($errorsimg_colorkey).'</p>';
                          }



                          $genTable .= '</div>'; 

                 }

                 $genTable .= '<div class="col-sm-1 push-col"><div class="form-row position">
                                 <label>Position</label>';
                 $genTable .=  Form::text('position['.$key.']', old('position', isset($position[$key])?$position[$key]:'0'), ['placeholder'=>'', 'class'=>'form-control']);
                                                 
                 $genTable .= '</div></div>';

                  $genTable .= '<div class="col-sm-1 push-col nopadding"><label>'.Lang::get('attribute.is_default').'</label>'; 
                  $genTable .= Form::radio('default_value', $j, $default_selected[$key]);
                  $genTable .= '</div>';



                 
                 if ($j == 1) {

                     $genTable .='<div class="col-sm-1 push-col actionsClone">';
                     $genTable .= '</div>';

               }else{

                   $genTable .='<div class="col-sm-1 push-col actionsClone">';
                   $genTable .= '<a href="#" class="minus-clone">
                                    <i class="glyphicon glyphicon-remove icon-close"></i>
                                   </a>';
                  $genTable .= '</div>';

               }

             

            $genTable .= '</div>';
            $j++;
            $langTab .= '<li class="lang-label">values</li></ul>';
            $allcon .= $maindiv.$langTab.$genTable;
        }
        return $allcon;
    } 

    public static function textTabWithEditLanuageCustomerAttrValue($fieldType, $name, $tablename, $table_id, $table_field, $tabseq = null, $edtorClass = null, $text_color_image = null, $search = null) {

        
        
        $default_lang = session('admin_default_lang');
        $languages = Language::where('status', '1')->orderBy('id', 'asc')
         ->orderByRaw(DB::raw("FIELD(id, $default_lang) DESC"))
        ->get();


        $genTable = '';
        $langTab = '';
        $maindiv = '';
        $allcon = '';

        $datasall = \App\CustomerAttrValue::where([$table_field => $table_id])->with('attrvalDetails')->orderBy('position','ASC')->get();
        
        //dd($datasall);
        $langdatas = array();
        $default_selected = array();

        foreach ($datasall as $datas) {
            foreach ($datas->attrvalDetails as $key => $data) {
                if (!empty($data->lang_id)) {
                    $langdatas[$data->cust_attr_val_id][$data->lang_id] = $data->$name;
                    
                }
               
            }
            $position[$datas->id] = $datas->position;
            $default_selected[$datas->id] = $datas->is_default;
        }

        $j= 1;
        foreach ($langdatas as $key => $langdata) {

             $class = $j==1?'original':'cloneData';

             $maindiv = '<div class="'.$class.' rows row">';
             
             $maindiv .= '<div class="col-sm-6"><div class="row">';

             $langTab = '<ul class="tab-list">';
             $genTable = '<div class="tab-content">';

             $genTable .= Form::hidden('attr_val_id[]', old($key, $key), ['id' => 'attr_val_id']);
             $i =1;
             foreach ($languages as $language) {
                
                $langTab .= '<li class="' . ($i == 1 ? 'active' : '') . ' tablang_'.$tabseq . $language->id .'"><a data-toggle="tab" href="#lang' . $tabseq . $language->id .$j. '">';
                $langTab .= !empty($language->languageFlag) ?
                        '<img src="' . Config::get('constants.language_url') . $language->languageFlag . '" width="29" height="29" '
                        . 'title="' . $language->languageName . '" class="pull-right">' : '';
                $langTab .= '</a></li>';
                
                $genTable .= '<div id="lang' . $tabseq . $language->id .$j. '" class="tab-pane fade in ' . ($i == 1 ? 'active' : '') . '">';
                $genTable .= Form::$fieldType($name . '['.$key.'][' . $language->id . ']', old($name . '['.$key.'][' . $language->id . ']', isset($langdata[$language->id]) ? $langdata[$language->id] : ""), ['class' => 'form-control ' . $edtorClass, 'placeholder' => '']);
                $genTable .= '</div><div class="search_data" style="display: none"><a href="#">'.(isset($langdata[$language->id]) ? $langdata[$language->id] : "").'</a></div>';


                $i++;
            }
            $genTable .= '</div></div></div>';

            $genTable .='<!--div class="col-sm-1 push-col actionsClone">';
            if($j == 1) {
                $genTable .= '<a href="#" class="add-clone"><i class="glyphicon glyphicon-plus"></i></a>';
            } else {
                $genTable .= '<a href="#" class="minus-clone"><i class="glyphicon glyphicon-minus"></i></a>';
            }
            $genTable .= '</div-->';


                 $genTable .= '<div class="col-sm-1 push-col"><div class="form-row position">
                                 <label>Position</label>';
                 $genTable .=  Form::text('position['.$key.']', old('position', isset($position[$key])?$position[$key]:'0'), ['placeholder'=>'', 'class'=>'form-control']);
                                                 
                 $genTable .= '</div></div>';

                  $genTable .= '<div class="col-sm-1 push-col nopadding"><label>'.Lang::get('attribute.is_default').'</label>'; 
                  $genTable .= Form::radio('default_value', $j, $default_selected[$key]);
                  $genTable .= '</div>';



                 
                 if ($j == 1) {

                     $genTable .='<div class="col-sm-1 push-col actionsClone">';
                     
                    

                     $genTable .= '</div>';

               }else{

                   $genTable .='<div class="col-sm-1 actionsClone">';
                     
                     $genTable .= '<a href="#" class="minus-clone">
                                    <i class="glyphicon glyphicon-remove icon-close"></i>
                                   </a>';

                  $genTable .= '</div>';

               }

             

            $genTable .= '</div>';
            $j++;
            $langTab .= '<li class="lang-label">values</li></ul>';
            $allcon .= $maindiv.$langTab.$genTable;
        }
        return $allcon;
    }



     public static function textTabWithEditLanuageNewsletterAttrValue($fieldType, $name, $tablename, $table_id, $table_field, $tabseq = null, $edtorClass = null, $text_color_image = null, $search = null) {

        
        
        $default_lang = session('admin_default_lang');
        $languages = Language::where('status', '1')->orderBy('id', 'asc')
         ->orderByRaw(DB::raw("FIELD(id, $default_lang) DESC"))
        ->get();


        $genTable = '';
        $langTab = '';
        $maindiv = '';
        $allcon = '';

        $datasall = \App\NewsletterAttrValue::where([$table_field => $table_id])->with('attrvalDetails')->orderBy('position','ASC')->get();
        
        //dd($datasall);
        $langdatas = array();
        $default_selected = array();

        foreach ($datasall as $datas) {
            foreach ($datas->attrvalDetails as $key => $data) {
                if (!empty($data->lang_id)) {
                    $langdatas[$data->news_attr_val_id][$data->lang_id] = $data->$name;
                    
                }
               
            }
            $position[$datas->id] = $datas->position;
            $default_selected[$datas->id] = $datas->is_default;
        }

        $j= 1;
        foreach ($langdatas as $key => $langdata) {

             $class = $j==1?'original':'cloneData';

             $maindiv = '<div class="'.$class.' rows row">';
             
             $maindiv .= '<div class="col-sm-6"><div class="row">';

             $langTab = '<ul class="tab-list">';
             $genTable = '<div class="tab-content">';

             $genTable .= Form::hidden('attr_val_id[]', old($key, $key), ['id' => 'attr_val_id']);
             $i =1;
             foreach ($languages as $language) {
                
                $langTab .= '<li class="' . ($i == 1 ? 'active' : '') . ' tablang_'.$tabseq . $language->id .'"><a data-toggle="tab" href="#lang' . $tabseq . $language->id .$j. '">';
                $langTab .= !empty($language->languageFlag) ?
                        '<img src="' . Config::get('constants.language_url') . $language->languageFlag . '" width="29" height="29" '
                        . 'title="' . $language->languageName . '" class="pull-right">' : '';
                $langTab .= '</a></li>';
                
                $genTable .= '<div id="lang' . $tabseq . $language->id .$j. '" class="tab-pane fade in ' . ($i == 1 ? 'active' : '') . '">';
                $genTable .= Form::$fieldType($name . '['.$key.'][' . $language->id . ']', old($name . '['.$key.'][' . $language->id . ']', isset($langdata[$language->id]) ? $langdata[$language->id] : ""), ['class' => 'form-control ' . $edtorClass, 'placeholder' => '']);
                $genTable .= '</div><div class="search_data" style="display: none"><a href="#">'.(isset($langdata[$language->id]) ? $langdata[$language->id] : "").'</a></div>';


                $i++;
            }
            $genTable .= '</div></div></div>';

            $genTable .='<!--div class="col-sm-1 push-col actionsClone">';
            if($j == 1) {
                $genTable .= '<a href="#" class="add-clone"><i class="glyphicon glyphicon-plus"></i></a>';
            } else {
                $genTable .= '<a href="#" class="minus-clone"><i class="glyphicon glyphicon-minus"></i></a>';
            }
            $genTable .= '</div-->';


                 $genTable .= '<div class="col-sm-1 push-col"><div class="form-row position">
                                 <label>Position</label>';
                 $genTable .=  Form::text('position['.$key.']', old('position', isset($position[$key])?$position[$key]:'0'), ['placeholder'=>'', 'class'=>'form-control']);
                                                 
                 $genTable .= '</div></div>';

                  $genTable .= '<div class="col-sm-1"><label>'.Lang::get('attribute.is_default').'</label>'; 
                  $genTable .= Form::radio('default_value', $j, $default_selected[$key]);
                  $genTable .= '</div>';



                 
                 if ($j == 1) {

                     $genTable .='<div class="col-sm-1 push-col actionsClone">';
                     
                    

                     $genTable .= '</div>';

               }else{

                   $genTable .='<div class="col-sm-1 actionsClone">';
                     
                     $genTable .= '<a href="#" class="minus-clone">
                                    <i class="glyphicon glyphicon-remove icon-close"></i>
                                   </a>';

                  $genTable .= '</div>';

               }

             

            $genTable .= '</div>';
            $j++;
            $langTab .= '<li class="lang-label">values</li></ul>';
            $allcon .= $maindiv.$langTab.$genTable;
        }
        return $allcon;
    }


    public static function getErrorMessage($message=null) {
       $msg_str = '';
       if(!empty($message)){
        $msg_str .= '<div id="error-msg" class="modal fade in" role="dialog">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content"> 
                                <div class="error-msg">
                                    <div class="clearfix">
                                        <span class="close icon-close close-msg" data-dismiss="modal"></span>
                                    </div>                    
                                    <div class="ok">
                                        <span class="icon-close icon-check error-icon glyphicon-ok"></span>
                                    </div>
                                    <h3 class="red">'.$message.'</h3>
                                    <div class="btn-group">
                                        <button class="btn-grey ok-msg" data-dismiss="modal">Ok</button>
                                    </div>
                                </div>
                            </div> 
                        </div><script>$(document).ready(function () {$(\'#error-msg\').modal(\'show\'); });</script>
                    </div>';
        }            

        return $msg_str;
    }





    public static function getSuccessMessage($message=null) {
       $msg_str = '';
       if(!empty($message)){
        $msg_str .= '<div id="sucess-msg" class="modal fade in" role="dialog">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content"> 
                                <div class="sucess-msg">
                                    <div class="clearfix">
                                        <span class="close icon-close close-msg" data-dismiss="modal"></span>
                                    </div>                    
                                    <div class="ok">
                                        <span class="icon-check error-icon glyphicon-ok"></span>
                                    </div>
                                    <h3 class="red">'.$message.'</h3>
                                    <div class="btn-group">
                                        <button class="btn-grey ok-msg" data-dismiss="modal">Ok</button>
                                    </div>
                                </div>
                            </div> 
                        </div><script>$(document).ready(function () {$(\'#sucess-msg\').modal(\'show\'); });</script>
                    </div>';
        }            

        return $msg_str;
    }

    public static function getCountryDorpDown($country_id=null,$country_id_arr=array()) {
        
        $country_lists = Country::select('id', 'country_isd', 'short_code')->get();
        
        $country_str = '';
        foreach($country_lists as $country)
        {            
           $selected = ''; 
           if($country->id == $country_id || in_array($country->id, $country_id_arr)) {
               $selected = 'selected="selected"'; 
           }
            
           $country_str .= '<option isd_code="'.$country->country_isd.'" value="'.$country->id.'" '.$selected.'>'.$country->countryName->country_name.'</option>';
        } 
        //echo '====>'.$country_str;die;
        
        return $country_str;
    }

    public static function getProvinceStateDD($country_id, $province_id='') {
        $province_list = \App\CountryProvinceState::getProvinceList($country_id);
        //dd($province_list);
        $option_str = '';
        if(count($province_list) > 0) {
            foreach ($province_list as $province_details) {
                $selected = '';
                if($province_details->id == $province_id) {
                    $selected = 'selected="selected"';
                }
                $option_str .= '<option value="'.$province_details->id.'" '.$selected.'>'.$province_details->provinceName->province_state_name.'</option>';
            }
        }
        return $option_str;
    }

    public static function getCityDistrictDD($province_id, $city_id='') {
        $city_list = \App\CountryCityDistrict::getCityList($province_id);
        //dd($province_list);
        $option_str = '';
        if(count($city_list) > 0) {
            foreach ($city_list as $city_details) {
                $selected = '';
                if($city_details->id == $city_id) {
                    $selected = 'selected="selected"';
                }
                $option_str .= '<option value="'.$city_details->id.'" '.$selected.'>'.$city_details->cityName->city_district_name.'</option>';
            }
        }
        return $option_str;
    }

    public static function textWithLanuage($fieldType, $name, $edtorClass = null, $validatorClass = null, $errors=null, $errorkey = null) {

        $languages = Language::where('status', '1')->orderBy('isDefault', 'desc')->get();
        $genTable = '';
        foreach ($languages as $language) {
            
            $field_class = '';
            $error_class = '';
            $def_lang = '';
            
            if($language->isDefault == '1' && !empty($validatorClass)) {
                $field_class = $validatorClass;
                $error_class = 'has-error';
                $def_lang = '<input type="hidden" name="def_lang_id" value="'.$language->id.'">';
            }
            
            $genTable .= $def_lang;
            $genTable .= '<div class="form-group">';
            $genTable .= '<div class="col-sm-2">';
            $genTable .= !empty($language->languageFlag) ?
                    '<img src="' . Config::get('constants.language_url') . $language->languageFlag . '" width="20" height="20" '
                    . 'title="' . $language->languageName . '" class="pull-right">' : '';
            $genTable .= '</div>';
            $genTable .= '<div class="col-sm-10 '.$error_class.'">';
            $genTable .= Form::$fieldType($name . '[' . $language->id . ']', old($name . '[' . $language->id . ']'), ['class' => 'form-control ' . $edtorClass .' '.$field_class, 'placeholder' => '']);

            
            if($language->isDefault == '1'){
             if(count($errors)>0 && !empty($errorkey)){
                if($errors->first($errorkey)){
                  $genTable .='<p id="name-error" class="error error-msg">'.$errors->first($errorkey).'</p>';
                }
              
             }

            }               
                           
            $genTable .= '</div>';
            $genTable .= '</div>';
        }
        return $genTable;
    }

    public static function textWithLanuageEdit($fieldType, $name, $dataarray, $type, $edtorClass = null, $validatorClass = null, $errors=null, $errorkey = null) {

        $darray = $dataarray->toArray();
        //dd($darray,$name,$type);
        $languages = Language::where('status', '1')->orderBy('isDefault', 'desc')->get();
        $genTable = '';
        foreach ($languages as $language) {
            $langdata = [];
            if(count($darray)){
                foreach($darray as $ind=>$unit){
                    if($unit['lang_id'] == $language->id){
                        $langdata = $darray[$ind];

                        if($type == 'name'){
                            
                            $categoryname = $unit['category_name'];                 

                        }
                        else if ($type == 'description'){
                            $categorydescription = $unit['cat_description'];
                        }
                    }
                }    
            }
            
            
            
            $field_class = '';
            $error_class = '';
            $def_lang = '';
            
            if($language->isDefault == '1' && !empty($validatorClass)) {
                $field_class = $validatorClass;
                $error_class = 'has-error';
                $def_lang = '<input type="hidden" name="def_lang_id" value="'.$language->id.'">';
            }
            
            $genTable .= $def_lang;
            $genTable .= '<div class="form-group">';
            $genTable .= '<div class="col-sm-2">';
            $genTable .= !empty($language->languageFlag) ?
                    '<img src="' . Config::get('constants.language_url') . $language->languageFlag . '" width="20" height="20" '
                    . 'title="' . $language->languageName . '" class="pull-right">' : '';
            $genTable .= '</div>';
            $genTable .= '<div class="col-sm-10 '.$error_class.'">';

            if($type == 'name'){
                $genTable .= Form::$fieldType($name . '[' . $language->id . ']', $categoryname , ['class' => 'form-control ' . $edtorClass .' '.$field_class, 'placeholder' => '']);
            }
            else if ($type == 'description'){
                $genTable .= Form::$fieldType($name . '[' . $language->id . ']', $categorydescription, ['class' => 'form-control ' . $edtorClass .' '.$field_class, 'placeholder' => '']);
            }
            
            if($language->isDefault == '1'){
             if(count($errors)>0 && !empty($errorkey)){
                if($errors->first($errorkey)){
                  $genTable .='<p id="name-error" class="error error-msg">'.$errors->first($errorkey).'</p>';
                }
              
             }

            }               
                           
            $genTable .= '</div>';
            $genTable .= '</div>';
        }
        return $genTable;
    }

    public static function buyerBillTo($orderInfoJson){

        $html = '<strong class="name">'. $orderInfoJson['billing_address']['first_name'].' '.$orderInfoJson['billing_address']['last_name'].'</strong>';
        $html .= '<p>'.$orderInfoJson['billing_address']['address'].'<br>';
        if(!empty($orderInfoJson['billing_address']['sub_district'])) {
            $html .= $orderInfoJson['billing_address']['sub_district'].', ';
        }
        if(!empty($orderInfoJson['billing_address']['district'])) {
            $html .= $orderInfoJson['billing_address']['district'].', ';
        }
        $html .= $orderInfoJson['billing_address']['provice'].'<br>'.$orderInfoJson['billing_address']['country'].' '.$orderInfoJson['billing_address']['zip_code'].'<br/> <a href="tel:0'.$orderInfoJson['billing_address']['isd_code'].$orderInfoJson['billing_address']['ph_number'].'">+'.$orderInfoJson['billing_address']['isd_code'].'-'.$orderInfoJson['billing_address']['ph_number'].'</a><br/>'.$orderInfoJson['billing_address']['email'].'</p>';

        echo $html;
    } 

    public static function buyerShipTo($orderInfoJson){

        $html = '<strong class="name">'. $orderInfoJson['shipping_address']['first_name'].' '.$orderInfoJson['shipping_address']['last_name'].'</strong>';
        $html .= '<p>'.$orderInfoJson['shipping_address']['address'].'<br/>';
        if(!empty($orderInfoJson['shipping_address']['sub_district'])) {
            $html .= $orderInfoJson['shipping_address']['sub_district'].', ';
        }
        if(!empty($orderInfoJson['shipping_address']['district'])) {
            $html .= $orderInfoJson['shipping_address']['district'].', ';
        }        
        $html .= $orderInfoJson['shipping_address']['provice'].'<br/>'.$orderInfoJson['shipping_address']['country'].' '.$orderInfoJson['shipping_address']['zip_code'].'<br/><a href="tel:0'.$orderInfoJson['shipping_address']['isd_code'].$orderInfoJson['shipping_address']['ph_number'].'">+'.$orderInfoJson['shipping_address']['isd_code'].'-'.$orderInfoJson['shipping_address']['ph_number'].'</a><br/>'.$orderInfoJson['shipping_address']['email'].'</p>';

        echo $html;
    }

    public static function getOrderUserInfo($orderInfo){
        $user_str = '<span class="name-detail">'.Lang::get('order.name').' : '.$orderInfo->userDetail->display_name.' </span>
            <span class="name-detail">'.Lang::get('order.email').' : '.$orderInfo->userDetail->email .'</span>
            <span class="name-detail">'.Lang::get('order.customer_group').' : </span>
            <span class="name-detail not-log">'.$orderInfo->userGroup->group_name.'</span>';

        echo $user_str;
    }

    public static function getAdminOrderUserInfo($orderInfo){
        $user_str = '<span class="block">'.Lang::get('order.name').'<i class="strick">*</i> '.$orderInfo->user_name.'</span>
            <span class="email">'.Lang::get('order.email').'<i class="strick">*</i> <a href="mailto:'.$orderInfo->user_email.'">'.$orderInfo->user_email.'</a></span>
            <span>'.Lang::get('order.customer_group').'<i class="strick">*</i>'.$orderInfo->userGroup->group_name.'</span>';

        echo $user_str;
    }    

    public static function getOrderAgentInfo($orderInfo){

        $agent_str = '<div class="order-status-content">
            <div class="user-img">
                <img src="'.getUserImageUrl($orderInfo->agentDetail->image, $orderInfo->agentDetail->gender).'" width="40">
            </div>
            <div class="user-detail">
                <span class="name"> '.$orderInfo->agentDetail->nick_name.' ('.getDepartmentName($orderInfo->agentDetail->role_id).')</span>
                <a href="mailto:'.$orderInfo->agentDetail->email.'" >'.$orderInfo->agentDetail->email.'</a>
                <a href="tel:'.$orderInfo->agentDetail->contact_no.'" >'.$orderInfo->agentDetail->contact_no.'</a>
            </div>
        </div>';

        echo $agent_str;
    }

    public static function getAdminOrderAgentInfo($orderInfo){

        $agent_str = '<div class="border-box">
            <div class="form-row">
                <h3 class="buy-title">Agent Information</h3>
                <img src="'.getUserImageUrl($orderInfo->agentDetail->image, $orderInfo->agentDetail->gender).'" alt="" width="60" height="60" class="img-circle"> 
            </div>
            <div class="form-row">
                <div class="user-detail-row">
                    <label>Name <i class="strick">*</i> </label> <span class="u-name"> '.$orderInfo->agentDetail->nick_name.' </span>
                </div>
                <div class="user-detail-row">
                    <label>Email <i class="strick">*</i> </label> <span class="u-name"> <a href="mailto:'.$orderInfo->agentDetail->email.'" >'.$orderInfo->agentDetail->email.'</a> </span>
                </div>
                <div class="user-detail-row">
                    <label>Contact <i class="strick">*</i> </label> <span class="u-name"> <a href="tel:'.$orderInfo->agentDetail->contact_no.'" >'.$orderInfo->agentDetail->contact_no.'</a> </span>
                </div>
                <div class="user-detail-row">
                    <label>Team <i class="strick">*</i> </label> <span class="u-name"> '.getDepartmentName($orderInfo->agentDetail->role_id).' </span>
                </div>
            </div>
        </div>';

        echo $agent_str;
    }             

    public static function orderProductDetail($orderInfo,$ordDetailJson){
        $promo = \App\OrderPromotion::where('order_id',$orderInfo->id)->get();
        $promotion = '';
        if(count($promo)){
            foreach ($promo as $pkey => $pvalue) {
                if($pvalue->coupon_code){
                    $promotion .= Lang::get('checkout.coupon_code').' '.$pvalue->coupon_code.', ';
                }else{
                    $promotion .= $pvalue->pro_name.', ';
                }
                # code...
            }
            $promotion = rtrim($promotion,', ');
        }
        $html = '';
        $html .='<h2>'.Lang::get('order.items_ordered').'</h2>';
        $html .='<div class="table-wrapper cart-col"> 
                    <div class="table">
                    <div class="table-header">';
        $html .='<ul class="card-item-header">
                    <li class="col-sm-5">'.Lang::get('checkout.product').'</li>
                    <li class="col-sm-3">'.Lang::get('checkout.price').'</li>
                    <!--<li>'.Lang::get('checkout.discuount_amt').'</li>-->
                    <li class="col-sm-1">'.Lang::get('checkout.qty').'</li>
                    <li class="col-sm-3">'.Lang::get('checkout.row_total').'</li>
                </ul>';
        $html .='</div>';
        $html .= '<div class="cart-item-container">';
        if(count($orderInfo)){
            foreach($orderInfo->orderDetails as $key => $orderDetailRes){
                $html .='<ul class="cart-item-list">
                            <li class="col-sm-5">
                            <div class="cart-img">
                             <a href="'.getProductUrl($orderDetailRes->url).'"><img width="60" src="'.getProductImageUrl($ordDetailJson[$orderDetailRes->id]['thumbnail_image']).'" alt=""></a>
                            </div>
                             <div class="cart-item-desc">
                             <h3>
                             <a href="'.getProductUrl($orderDetailRes->url).'">'.$ordDetailJson[$orderDetailRes->id]['name'][session('default_lang')].'</a>
                             </h3>
                             <span class="sku">'.$ordDetailJson[$orderDetailRes->id]['sku'].'</span>';

                            $html .=Self::getOrderAttributeDetails($ordDetailJson[$orderDetailRes->id]);
                            $html.= '</div>';
                            $html.='</li>

                            <li class="col-sm-3 price-wrap">'.numberFormat($orderDetailRes->unit_price).' '.$orderInfo->getCurrency->currency_code.' </li>
                           <!--<li class="discount-amt">'.numberFormat($orderDetailRes->sale_discount).' '.$orderInfo->getCurrency->currency_code.' </li>-->
                            <li class="col-sm-1 qty">'.$orderDetailRes->quantity.'</li>
                            <li class="col-sm-3 total">'.numberFormat($orderDetailRes->total_final_price).' '.$orderInfo->getCurrency->currency_code.'
                            </li>
                        </ul>';
            }
        }
        $html .='</div>
                </div>';
        $html .='<div class="table-footer">';
            $html .='<div class="footer-row">
                        <span class="col-sm-6 col-xs-6">'.Lang::get('checkout.total_units').'</span>
                        <span class="col-sm-6 col-xs-6">'.$orderInfo->ttl_unit.'</span>
                    </div>';
            $html .='<div class="footer-row">
                        <span class="col-sm-6 col-xs-6">'.Lang::get('checkout.item_total').'</span>
                        <span class="col-sm-6 col-xs-6">'.numberFormat($orderInfo->total_core_cost).' '.$orderInfo->getCurrency->currency_code.'</span>
                    </div>';
            if($orderInfo->sale_special_discount > 0 || $orderInfo->total_promotion_discount){
                $html .='<div class="footer-row">
                            <span class="col-sm-6 col-xs-6">'.Lang::get('order.discount');
   
                        if($promotion) {
                        $html .='<br><b class="error">- '.$promotion.' </b> ';
                        }

                    $html.='</span>';
                    $html.='<span class="col-sm-6 col-xs-6 error">-'.numberFormat($orderInfo->total_promotion_discount).' '.$orderInfo->getCurrency->currency_code.'</span>';
                                                    
                $html .= '</div>';
                $subtotal = $orderInfo->total_core_cost-($orderInfo->total_promotion_discount);
                $html.='<div class="footer-row">
                            <span class="col-sm-6 col-xs-6"> '.Lang::get('checkout.sub_total').' </span>
                            <span class="col-sm-6 col-xs-6">'.($subtotal>0?numberFormat($subtotal):'0.00').' '.$orderInfo->getCurrency->currency_code.'</span>
                    </div> ';
            }
            $html .='<div class="footer-row">
                        <span class="col-sm-6 col-xs-6">'.Lang::get('checkout.shipping_cost').'</span>
                        <span class="col-sm-6 col-xs-6">'.numberFormat($orderInfo->total_shipping_cost).' '.$orderInfo->getCurrency->currency_code.'</span>
                    </div>';
            $html .='<!--<div class="footer-row">
                        <span class="col-sm-6 col-xs-6">'.Lang::get('checkout.plus_vat').'('.$orderInfo->vat.')</span>
                        <span class="col-sm-6 col-xs-6">'.numberFormat($orderInfo->vat_amount).' '.$orderInfo->getCurrency->currency_code.'</span>
                    </div>-->';
            $html .='<div class="footer-row total">
                        <span class="col-sm-6 col-xs-6">'.Lang::get('checkout.total').'</span>
                        <strong class="col-sm-6 col-xs-6">'.numberFormat($orderInfo->total_final_price).' '.$orderInfo->getCurrency->currency_code.'</strong>
                    </div>';
        $html .='</div>';
        $html .='</div>';
        echo $html;
    }

    public static function getOrderAttributeDetails($orderAttribute){
        $html = '';
        //dd($orderAttribute);
        if(isset($orderAttribute['attributeDetail'])){
            foreach ($orderAttribute['attributeDetail'] as $attrDet) {
                if(isset($attrDet['attribute_type'])&&$attrDet['attribute_type'] == 2){

                    $html .= "<div class='size-color-row'>";
                    $html .=$attrDet['attribute_name'][session('default_lang')].' ';
                    $html .='<label class="shop-item-size skyblue">';
                    if($attrDet['front_input']=='text' || $attrDet['front_input']=='textarea'){
                        $html.= $attrDet['attribute_value'].' ';
                    }elseif($attrDet['front_input']=='browse_file'){
                        $html .=' <a href="'.Config::get('constants.cart_option_url').$attrDet['attribute_value'].'" target="_blank">Image</a> ';
                    }else{
                        $html.=$attrDet['attribute_value_name'][session('default_lang')].' ';
                    }
                    $html.='</label>';
                    $html .= "</div>";

                }else{

                    $html .= "<div class='size-color-row'>";

                    $html .= $attrDet['attribute_name'][session('default_lang')];
                    if(isset($attrDet['color_code_image']) && count($attrDet['color_code_image'])){
                        
                        if($attrDet['color_code_image']['color_image'] != ''){
                            $html .= '<img src= "'.attrValImgUrl($attrDet['color_code_image']['color_image']).'" width="16" height="16">';
                        }elseif($attrDet['color_code_image']['color_code'] != ''){
                            $html .='<div style="background:'.$attrDet['color_code_image']['color_code'].'; width:16px; height:16px; margin-left:5px; display:inline-block">&nbsp</div>';
                        }else{
                            $html .='<label class="shop-item-size skyblue">'.$attrDet['attribute_value_name'][session('default_lang')].'
                            </label>';
                        }
                    }else{
                         $html .='<label class="shop-item-size skyblue"> : '.$attrDet['attribute_value_name'][session('default_lang')].'
                            </label>';
                    }

                   $html .= "</div>";
                }
            }
        }
        return $html;

    }

    public static function getOrderHistory($orderId){

        //$orderInfo = \App\Orders::where('id',$orderId)->first();
        $history = \App\OrderTransaction::select('comment','created_at')->where('order_id',$orderId)->get();
        $dateArr = [];
     
        if(count($history)){
            foreach ($history as $key => $value) {
                $date = getDateFormat($value->created_at,1);
                $dateArr[] = ['comment'=>$value->comment,'date'=>$date];
            }
        }
        $str='';
        if(count($dateArr)){
            $str .='<div class="order-list-row history-list border-none">';
            foreach ($dateArr as $key => $hisValue) {
                $str .='<div class="form-row">
                            <span><span class="ord-txt">'.$hisValue["comment"].'</span><span class="time">'.$hisValue["date"].'</span>
                            </span>
                        </div>';
            }
            $str .='</div>';
        }
        return $str;
    }

    public static function getInvoiceProductDetail($invoice_dtl, $invoice_prod_dtl, $orderInfo){

        $prod_dtl_str = '<div class="table-wrapper">
            <div class="table">
                <div class="table-header">
                    <ul>
                        <li class="">'.Lang::get('order.product').'</li>
                        <li class="">'.Lang::get('order.price').'</li>
                        <li class="">'.Lang::get('order.qty').'</li>
                    </ul>
                </div>
                <div class="table-content">';
                foreach($invoice_prod_dtl as $key => $invoice_prod_detail) {

                    $orderDetailJson = json_decode($invoice_prod_detail->order_detail_json, true);

                    $prod_dtl_str .= '<ul>
                        <li class="product">
                            <a href="'.getProductUrl($invoice_prod_detail->url).'"><img src="'.getProductImageUrl($orderDetailJson['thumbnail_image']).'" width="42" height="42"></a>
                            <a href="'.getProductUrl($invoice_prod_detail->url).'"><span class="name">'.$orderDetailJson['name'][session('default_lang')].'</span></a>
                            <span class="block">'.$orderDetailJson['sku'].'</span>
                        </li>
                        <li class="price">'.numberFormat($invoice_prod_detail->unit_price).' '.$orderInfo->getCurrency->currency_code.'</li>
                        <li class="qty">'.$invoice_prod_detail->quantity.'</li>
                    </ul>';
                }                                                  
                $prod_dtl_str .= '</div>
            </div>
            <div class="table-footer">  
                <div class="footer-row">
                    <span class="col-sm-6">'.Lang::get('order.total_units').'</span>
                    <span class="col-sm-6">'.$invoice_dtl->total_qty.' '.Lang::get('order.units').'</span>
                </div>
            </div>                      
        </div>';

        //dd('====>', $prod_dtl_str);

        return $prod_dtl_str;
    }

    // public static function getLanguage(){
    //     $lang = '';
    //     $language = \App\Language::where('status','1')->get();
    //     foreach ($language as $key => $value) {
    //         $default = ($value->isDefault == '1') ? 'active' : '';
    //         $lang .= '<li><a href="#" class="'.$default.'">'.$value->languageCode.'</a></li>';
    //     }
    //     echo $lang;
    // }

    public static function getLanguageSwitcher() {

        $cur_url = \Request::path();
        $cur_url_arr = explode('/', $cur_url);
        unset($cur_url_arr['0']);
        $cur_url = implode('/', $cur_url_arr);

        $ajax_url = action('AjaxController@updateSession');
        
        //$cur_url = str_replace($default_lang_code,'',$cur_url);
        //echo '====>'.$cur_url.'====>'.$default_lang_code;

        $languages = \App\Language::getLangugeDetails();

        $lang_str = '<span class="dropdown-toggle" data-toggle="dropdown">
                        <span class="lang-name">'.session('lang_code').' </span>
                        <i class="caret"></i>
                    </span>                   
                    <ul class="dropdown-menu">';
                                
        foreach ($languages as $value) {

            $lang_url = Config::get('constants.public_url').$value->languageCode.'/'.$cur_url;

            $lang_str .= '<li><a href="javascript:void(0);" onClick="switchLanguage(\''.$value->id.'\', \''.$value->languageCode.'\', \''.$lang_url.'\', \''.$ajax_url.'\')">'.$value->languageCode.'</a></li>';
        }
        $lang_str .= '</ul>';

        return $lang_str;
    } 

    public static function getRmaConfigOption($type){
        $config_option_str = '';
        $config_option = \App\AdminRmaConfig::getRmaConfig($type);
        //dd($config_option);
        if(count($config_option) > 0) {
            foreach ($config_option as $value) {
                $config_option_str .= '<option>'.$value->reason->description.'</option>';
            }
        }
        return $config_option_str;        
    }

    public static function getChatDetail($entity_id, $entity_type, $user_type=null){ 

        $chat = \App\UserComment::chatDetail($entity_id, $entity_type, $user_type);

        $chat_details = '';
        if(count($chat) > 0) {
            foreach ($chat as $value) {

                $date = getcommentDateFormat($value->created_at);

                if($user_type == 'admin' && $value->user_type == '1') {
                    $class = 'right';
                }
                elseif($user_type === null && $value->user_type == '0') {
                    $class = 'right';
                }
                else {
                    $class = '';
                }

                if($value->user_type == '1') {
                    $name = $value->adminUserName->nick_name;
                }else {
                    $name = $value->userName->display_name;
                }

                $chat_details .= '<div class="cmt-order-row '.$class.'">
                        <h3 class="name skyblue">'.$name.'</h3>';
                        if(!empty($value->file)) {
                            $chat_details .= '<span class="filename"><a class="skyblue" href="'.Config::get('constants.comment_file_url').$value->file.'" target="_blank">'.$value->file.'</a></span><br>';
                        }
                        $chat_details .= '<span>'.$value->comment.'</span>
                        <span class="datetime">'.$date.'</span>
                    </div>';
            }
        }
        
        return $chat_details;
    }

    public static function getRmaConfig($type, $config_count=1) {
        $language_dtl = \App\Language::getLangugeDetails();

        $config_str = '<div class="form-group property_div" data-attr="new">';
        foreach($language_dtl as $languages) {
            $error_class = '';
            if($languages->isDefault == '1'){
                $error_class = 'has-error';
            }
            $config_str .= '<div class="col-md-2 '.$error_class.'">
                <img src="'.Config::get('constants.language_url').$languages->languageFlag.'" title="'.$languages->languageName.'"  width="20" height="20">
                <input type="text" class="form-control" name="rma_config[new_'.$config_count.']['.$languages->id.']">
            </div>';
        }
        $config_str .= '<div class="col-md-2 mt-15"><a class="property_div_remove secondary mt-5" href="javascript:void(0);">-Remove</a></div></div>';

        return $config_str;
    }    

    public static function getRmaConfigValue($type) {

        $language_dtl = \App\Language::getLangugeDetails();
        $rma_config = \App\AdminRmaConfig::getRmaConfig($type);

        //dd($language_dtl, $rma_config);

        $config_str = '';

        if(count($rma_config) > 0) {
            $rma_desc_arr = [];
            foreach($rma_config as $rma_config_value) {
                foreach($rma_config_value->reasons as $rma_desc) {
                    $rma_desc_arr[$rma_config_value->id][$rma_desc->lang_id] = $rma_desc->description;
                }
            }
            //dd($rma_desc_arr);
            foreach($rma_config as $rma_config_value) {
                $config_str .= '<div class="form-group property_div" data-attr="'.$rma_config_value->id.'">';
                foreach($language_dtl as $languages) {
                    $error_class = '';
                    if($languages->isDefault == '1'){
                        $error_class = 'has-error';
                    }
                    $rma_desc_val = '';
                    if(isset($rma_desc_arr[$rma_config_value->id][$languages->id])) {
                        $rma_desc_val = $rma_desc_arr[$rma_config_value->id][$languages->id];
                    }
                    $config_str .= '<div class="col-md-2 '.$error_class.'">
                        <img src="'.Config::get('constants.language_url').$languages->languageFlag.'" title="'.$languages->languageName.'"  width="20" height="20">
                        <input type="text" class="form-control" name="rma_config['.$rma_config_value->id.']['.$languages->id.']" value="'.$rma_desc_val.'">
                    </div>';
                }
                $config_str .= '<div class="col-md-2 mt-15"><a class="property_div_remove secondary mt-5" href="javascript:void(0);">-Remove</a></div></div>';
            }
        }

        return $config_str;
    } 

    /**
    * This helper finction will fetch the contents of newsletter from and return html contents to footer script
    * Added By @Dinesh Kumar Kovid | ***** Start ***** | Date : 29/01/2017
    */ 

    public static function getNewsletterFormContemts(){

        $customer_group_id = (isset(Auth::user()->group_id)) ? Auth::user()->group_id : 1;
        $default_lang = session('default_lang');

        $newsletterDataArray = \App\NewsletterAttribute::whereRaw("FIND_IN_SET('".$customer_group_id."',customer_group)")->with(['newsletterAttributeDesc','newsletterAttributeValue'])->orderBy('position','ASC')->get();
        
        $newsletterContents = "";
           
        foreach($newsletterDataArray as $key => $formFieldAttr){
                
            $newsletterContents .= "<div class='form-row'><div class='row'><lable class='col-sm-4'>".ucfirst($formFieldAttr->newsletterAttributeDesc->name)." : </lable><div class='col-sm-8'>";

            switch ($formFieldAttr->input_type) {
                case 'radio':
                    if(!empty($formFieldAttr->newsletterAttributeValue)){

                        foreach($formFieldAttr->newsletterAttributeValue as $at_val_key => $at_val_value){
                            $attr_value = (!empty($at_val_value->attrvalDetails) && isset($at_val_value->attrvalDetails[0]['values'])) ? $at_val_value->attrvalDetails[0]['values'] :'';
                            $selected = ($at_val_key=='0')?"checked":"";

                            $required = ($formFieldAttr->is_required==1) ? "required": "";
                            $newsletterContents .= "<label class='radio-wrap'><input type='".$formFieldAttr->input_type."' value='".$attr_value."' name='".$formFieldAttr->input_name."' ".$required." ".$selected."> <span class='radio-label'>".$attr_value."</span></label>";
                        }   
                    }else{
                        $newsletterContents .= "";
                    }

                break;
                
                case 'select':
                    $required = ($formFieldAttr->is_required==1) ? "required": "";
                    $newsletterContents .= "<select name='".$formFieldAttr->input_name."' ".$required.">";
                    ////////////////////////////////////
                    if(!empty($formFieldAttr->newsletterAttributeValue)){

                        foreach($formFieldAttr->newsletterAttributeValue as $at_val_key => $at_val_value){

                           $selected = ($at_val_key==0)?"selected":"";
                           $attr_value = (!empty($at_val_value->attrvalDetails) && isset($at_val_value->attrvalDetails[0]['values'])) ? $at_val_value->attrvalDetails[0]['values'] :'';

                            $newsletterContents .= "<option value='".$attr_value."' ".$selected.">".$attr_value."</option>";
                        }
                    }else{
                        $newsletterContents .= "";
                    }
                    $newsletterContents .= "</select>";
                    
                break;

                case 'multiselect':
                    $required = ($formFieldAttr->is_required==1) ? "required": "";
                    $newsletterContents .= "<select class='chosen-select' multiple='multiple' name='".$formFieldAttr->input_name."[]' ".$required." multiple >";
                    ////////////////////////////////////
                    if(!empty($formFieldAttr->newsletterAttributeValue)){

                        foreach($formFieldAttr->newsletterAttributeValue as $at_val_key => $at_val_value){

                           $attr_value = (!empty($at_val_value->attrvalDetails) && isset($at_val_value->attrvalDetails[0]['values'])) ? $at_val_value->attrvalDetails[0]['values'] :'';

                           $selected = ($at_val_key==0)?"selected":"";

                            $newsletterContents .= "<option value='".$attr_value."' ".$selected.">".$attr_value."</option>";
                        }
                    }else{
                        $newsletterContents .= "";
                    }
                    $newsletterContents .= "</select>";
                    
                break;
                case 'checkbox':
                    
                    $newsletterContents .= "<div>";
                    if(!empty($formFieldAttr->newsletterAttributeValue)){

                        foreach($formFieldAttr->newsletterAttributeValue as $at_val_key => $at_val_value){
                            $required = ($formFieldAttr->is_required==1) ? "required": "";
                            $attr_value = (!empty($at_val_value->attrvalDetails) && isset($at_val_value->attrvalDetails[0]['values'])) ? $at_val_value->attrvalDetails[0]['values'] :'';

                            $selected = ($at_val_key==0)?"checked":"";

                            $newsletterContents .= "<label class='check-wrap'><input type='".$formFieldAttr->input_type."' value='".$attr_value."' name='".$formFieldAttr->input_name."[]' ".$required. " ".$selected." ><span class='chk-label'>".$attr_value."</span></label>";
                        }
                    }else{
                        $newsletterContents .= "";
                    }
                    $newsletterContents .= "</div>";
                    
                break;
                case 'date_range':
                    $required = ($formFieldAttr->is_required==1) ? "required": "";
                    $newsletterContents .= "<input class='date-select date-picker col-sm-6' type='text' name='from_".$formFieldAttr->input_name."' placeholder='From ".$formFieldAttr->newsletterAttributeDesc->name."' ".$required.">";
                    $newsletterContents .= "<input class='date-select date-picker col-sm-6' type='text' name='to_".$formFieldAttr->input_name."' placeholder='To ".$formFieldAttr->newsletterAttributeDesc->name."' ".$required.">";
                break;

                case 'range':
                    $required = ($formFieldAttr->is_required==1) ? "required": "";
                    $newsletterContents .= "<input class='date-select col-sm-6' type='text' name='from_".$formFieldAttr->input_name."' placeholder='From ".$formFieldAttr->newsletterAttributeDesc->name."' ".$required.">";
                    $newsletterContents .= "<input class='date-select col-sm-6' type='text' name='to_".$formFieldAttr->input_name."' placeholder='To ".$formFieldAttr->newsletterAttributeDesc->name."' ".$required.">";
                break;
                
                default:
                    $required = ($formFieldAttr->is_required==1) ? "required": "";
                    $newsletterContents .= "<input type='".$formFieldAttr->input_type."' name='".$formFieldAttr->input_name."' placeholder='".$formFieldAttr->newsletterAttributeDesc->name."' ".$required." id='".$formFieldAttr->validation_type."'><span id='text_error'></span>";
                break;
            }
            
            

            $newsletterContents .= "</div></div></div>";
            

        }
        
        return $newsletterContents;
        

    }
    /**
    * This helper finction will fetch the contents of newsletter from and return html contents to footer script
    * Added By @Dinesh Kumar Kovid | ***** End ***** | Date : 29/01/2017
    */        

    public static function getBlogCategories(){

        $blogCategoryList = \App\BlogCategory::where('parent_id','0')->with('blogcategorydesc')->get();
        
        $blogCatMenu = "";
        foreach($blogCategoryList as $blog_cat_key => $blogCatDetail){
            $url = action('BlogController@sabinaClubUrl',['blogCat_id'=>$blogCatDetail->blogcategorydesc->category_name]);
            $blogCatMenu .= "<li><a href='".$url."' data-toggle='tooltip' data-placement='right' title='".$blogCatDetail->blogcategorydesc->category_name."''>".$blogCatDetail->blogcategorydesc->category_name."</a></li>";

        }
        
        return $blogCatMenu;
    } 

    public static function getStaticBlockError($page_url){

        $statickBlockErrorContent = \App\StaticBlock::where(['default_item'=>'1','url'=>$page_url])->with('staticBlockDesc')->first();
        if(!empty($statickBlockErrorContent)){
            $return = $statickBlockErrorContent->staticBlockDesc->page_desc;
        }else{
           $return = "No Content found for this error page."; 
        }
        return $return;
    }   

    public static function getStatickPageError($page_url){
        $statickPageErrorContent = \App\StaticPage::where(['default_item'=>'1','url'=>$page_url])->with('staticPageDesc')->first();

        if(!empty($statickPageErrorContent)){
            $return = $statickPageErrorContent->staticPageDesc->page_desc;
        }else{
           $return = "No Content found for this error page."; 
        }
        return $return;
    }
}
