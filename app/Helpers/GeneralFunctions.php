<?php
namespace App\Helpers;
use Illuminate\Support\Facades\DB;

use App\Country;
use App\CountryDesc;
use Carbon\Carbon;
use Auth;
use Lang;
use Request;
use Config;

class GeneralFunctions {

    public static function getCountryName($country_id) {
        
        $country_name = '';
        if(!empty($country_id)) { 
            $country = CountryDesc::select('country_name')->where([['country_id', '=', $country_id],['lang_id', '=', session('default_lang')]])->first();
            $country_name = $country->country_name;
        }
        return $country_name;
    } 
    
    public static function getProvinceName($province_id) {

        $province_name = '';
        if(!empty($province_id)) {         
            $province = \App\CountryProvinceStateDesc::select('province_state_name')->where([['province_state_id', '=', $province_id],['lang_id', '=', session('default_lang')]])->first();
            $province_name = $province->province_name;
        }
        return $province_name;
    }

    public static function getCityName($city_id) {

        $city_name = '';
        if(!empty($province_id)) {         
            $city = \App\CountryCityDistrictDesc::select('city_district_name')->where([['city_district_id', '=', $city_id],['lang_id', '=', session('default_lang')]])->first();
            $city_name = $province->province_name;
        }
        return $city_name;
    }     
    
    public static function getCountryArr() {
        
        $country_lists = Country::select('id')->get();
        
        $country_arr[''] = '--select country--';
        
        foreach($country_lists as $country)
        {                      
           $country_arr[$country->id] = $country->countryName->country_name;
        } 
        
        //echo '<pre>';print_r($country_arr);die;
        
        return $country_arr;
    }

    public static function getIsdCodeArr() {
        
        $country_lists = Country::select('id', 'country_isd')->get();
        
        $country_arr[''] = '--isd code--';
        
        foreach($country_lists as $country)
        {                      
           $country_arr[$country->country_isd] = $country->country_isd;
        }
        
        //echo '<pre>';print_r($country_arr);die;
        
        return $country_arr;
    }


    /* it will return cartisian product of arrays */

    public static function getCartesianProduct($arrays)
    {
        $result = array();
        $arrays = array_values($arrays);
        $sizeIn = sizeof($arrays);
        $size = $sizeIn > 0 ? 1 : 0;
        foreach ($arrays as $array)
            $size = $size * sizeof($array);
        for ($i = 0; $i < $size; $i ++)
        {
            $result[$i] = array();
            for ($j = 0; $j < $sizeIn; $j ++)
                array_push($result[$i], current($arrays[$j]));
            for ($j = ($sizeIn -1); $j >= 0; $j --)
            {
                if (next($arrays[$j]))
                    break;
                elseif (isset ($arrays[$j]))
                    reset($arrays[$j]);
            }
        }
        return $result;
    }


    //for formating alias 
    public static function changeCategoryUrl($url) {
        $val = '--';
        $matchVal = strpos($url, $val);
        if ($matchVal === false) {
          return $url;
        } else {
            $url = str_replace('--', '-', $url);
            return $this->changeCategoryUrl($url);
        }
    }

    public static function getProductPriceById($productId,$quantity=1){
        $date = date('Y-m-d');
        $res = \App\Product::select('special_price','initial_price','has_sp_tp_bp','sp_tp_bp_type','to_date','from_date')->where(['id' => $productId])->first();
        //dd($res,$productId);
        if($res->has_sp_tp_bp == 'true'){
            /***for special price*****/
            if($res->sp_tp_bp_type == 0){
                
                return $res->initial_price;

            }
            elseif($res->sp_tp_bp_type == 1){
                if(strtotime($date) >= strtotime($res->from_date) && strtotime($date) <= strtotime($res->to_date)){
                    return $res->special_price;
                }else{
                    return $res->initial_price;
                }
            }
            /****for tier price***/
            elseif($res->sp_tp_bp_type == 2){  
                $startQty = \App\ProductTireBundlePrice::select('quantity')->where('product_id',$productId)->first();
                if(count($startQty)){
                    $start_quantity = $startQty->quantity;
                }else{
                    return $res->initial_price;
                }
                
                if($quantity < $start_quantity){
                    return $res->initial_price;
                }else{ /**calculating tier price**/

                    $tableName = with(new \App\ProductTireBundlePrice)->getTable();
                    $prefix = DB::getTablePrefix();
                    $tire = DB::select( DB::raw("SELECT * FROM $prefix$tableName WHERE (($quantity BETWEEN `quantity` and `end_quantity`) or (`quantity` <= $quantity And `is_end` = '1')) and product_id = $productId ") )[0];

                    return $tire->price;
                }
            }elseif($res->sp_tp_bp_type == 3){ /***FOR BUNDLE PRICE*****/
                $data = \App\ProductTireBundlePrice::where('product_id',$productId)->select('price','quantity')->get();
                if(count($data)){
                    $dataArr = $data->toArray();
                    /*****adding initial price to price array********/
                    $dataArr[] = ['price'=>$res->initial_price,'quantity'=>1];
                    //dd($dataArr);
                    $totPrice = $i = $reach = 0;
                    $quantityAtt = array_column($dataArr, 'quantity');
                     
                    while($quantity > 0){

                        $max = max($quantityAtt);
                        $maxkey = array_search($max, $quantityAtt);
                        $reach = $quantity - $max;
                        if($reach >=0){
                          $quantity = $reach;
                          $totPrice = $totPrice + $dataArr[$maxkey]['price'] * $dataArr[$maxkey]['quantity'];
                        }
                        
                        if($quantity >= 0){
                            /*****removing reached quantity index******/
                            if($quantity < $max){
                                unset($quantityAtt[$maxkey]);
                            }
                        }
                       /* if($i==0){
                          echo $totPrice.'-'.$quantity;
                          dd($quantityAtt);
                          exit;
                        }*/
                        $i++;
                       
                    }
                   
                    return $totPrice;
                }else
                return $res->initial_price;
            }
          
        }else{
          return $res->initial_price;
        }
    }

    function getmultiply($qty,$max){
        $i = 0;
        $my = 0;
        if($qty >= $max){
        while($my <= $qty){
          $my = $max + $my;
          $i++;
        }
        return $i-1;
        }else return $i;
    }

    public static function getProductQtyById($productId){
        $warehouse_id = $quantity = 0;
        $warehouse_id = \App\Warehouse::where(['status' => '1','online_store'=>'YES'])->value('id');

        if($warehouse_id > 0){
            $prd_warehouse = \App\ProductWarehouse::where(['product_id'=>$productId,'warehouse_id'=>$warehouse_id])->first();
            if(count($prd_warehouse)){
                if($prd_warehouse->has_unlimited == '1'){
                    $quantity = 1000;
                }elseif($prd_warehouse->has_unlimited == '2'){
                    $quantity = $prd_warehouse->quantity;
                }else{
                    $quantity = 0;
                }
            }
        }

        return $quantity;
    }

    public static function getFormattedId() {
        return time().sprintf("%04d", mt_rand(1, 9999));
    } 

    public static function numberFormat($price, $shopId=Null)
    {
        return number_format($price,2);
    }

    public static function printData($data){
        return strip_tags($data);
    }

    public static function getCategoryBlogCount($cat_id) {

        $tblProduct =  with(new \App\Product)->getTable();
        $tblProductSellerCat =  with(new \App\ProductSellerCat)->getTable();
        
        return DB::table($tblProduct.' as p')
                    ->join($tblProductSellerCat.' as psc', 'p.id', '=', 'psc.product_id')
                    ->where(['p.product_type' => 'blog', 'psc.cat_id' => $cat_id])
                    ->count();
    } 

    public static function getBloggerHeaderDetails() {

        $bloggerHeaderDetails = \App\Blogger::bloggerDetail();

        $blogger_arr['blogger_id'] = $bloggerHeaderDetails->blogger_id_show;
        $blogger_arr['blogger_name'] = $bloggerHeaderDetails->nickname;

        $image_url = Config::get('constants.image_url').'default-image-95x95.png';

        if(empty($bloggerHeaderDetails->image)) {
            $blogger_arr['blogger_image'] = $image_url;
        }
        else {
            $blogger_arr['blogger_image'] = Config::get('constants.blog_url').'blogger/'.$bloggerHeaderDetails->id.'/'.$bloggerHeaderDetails->image;
        }

        $blogger_arr['created_at'] = getDateFormat($bloggerHeaderDetails->created_at, '5');
        $blogger_arr['blogger_email'] = Auth::user()->email;
        $blogger_arr['total_blogs'] = \App\Product::getTotalBlog();
        $blogger_arr['total_category'] = \App\BlogCategory::getTotalBlogCategory();
        $blogger_arr['total_review'] = \App\BlogReview::getTotalBlogReview();

        //echo '<pre>';print_r($blogger_arr);die;

        return $blogger_arr;
    }

    public static function getUserDetail($user_id) {

        $user_detail = \App\User::userDetail($user_id);

        $user_detail->formated_id = $user_detail->id;
        $user_detail->user_name = ucfirst($user_detail->name).' '.ucfirst($user_detail->sur_name);
        $user_detail->display_name = ucfirst($user_detail->display_name);
        $user_detail->user_dob = getDateFormat($user_detail->dob);        

        if($user_detail->mobile > 0) {
            $user_detail->contact_no = $user_detail->mobile_isd_code.$user_detail->mobile;
        }

        if($user_detail->gender == 'M') {
            $user_detail->gender_new = 'Male';
        }        
        elseif($user_detail->gender == 'F') {
            $user_detail->gender_new = 'Female';
        }
        else {
            $user_detail->gender_new = 'Undefined';
        }

        if(!empty($user_detail->image)) {
            $user_detail->image_url = Config::get('constants.users_url').$user_detail->image;
        } 
        else if($user_detail->gender == 'M' || $user_detail->gender == 'U') {
            $user_detail->image_url = self::getPlaceholderImage('USER_IMAGE');
        }        
        elseif($user_detail->gender == 'F') {
             $user_detail->image_url = self::getPlaceholderImage('USER_IMAGE_FEMALE');
        }
        //echo '<pre>';print_r($user_detail->toArray());die;

        return $user_detail;        
    }

    

    public static function getCategoryDropDownData($cat_path) {

        $blog_mkt_cat_opt_arr = array();

        $blog_main_cat_arr = array_filter(explode('-', $cat_path));
        foreach ($blog_main_cat_arr as $value) {

            $blog_mkt_cat_opt_dtl = \App\Category::getMarketPlaceCatOpt($value);

            $blog_mkt_cat_opt_tmp = array();
            foreach ($blog_mkt_cat_opt_dtl->parentCategory as $cat_opt_value) {
                if(isset($cat_opt_value->categorydesc->name)){
                    $blog_mkt_cat_opt_tmp[] = ['cat_id'=>$cat_opt_value->id, 'cat_name'=>$cat_opt_value->categorydesc->name, 'selected_cat_id'=>$value];    
                }
                
            }
            $blog_mkt_cat_opt_arr[] = $blog_mkt_cat_opt_tmp;
        }

        return $blog_mkt_cat_opt_arr;
    } 

    public static function fetchValue($model, $field, $id) {
        return $model::select($field)->where('id', $id)->first()->$field;
    }

    public static function fetchValueDesc($modelDesc, $field, $match_field, $match_id) {
        return $modelDesc::select($field)->where([$match_field=>$match_id, 'lang_id'=>session('default_lang')])->first()->$field;
    }


    public static function getPlaceholderImage($systemname){
        
        $placeholder = '';
        $system = \App\SystemConfig::select('system_val')->where(['system_name'=>$systemname])->first();
        if(count($system)){
            if($systemname == 'USER_IMAGE' || $systemname == 'USER_IMAGE_FEMALE') {
                $img_name = \App\AdminAvatar::select('name')->where(['id'=>$system->system_val])->first();
                $placeholder = Config::get('constants.avtar_images_url').$img_name->name;
            }
            else {
                $placeholder = Config::get('constants.placeholder_url').$system->system_val;
            }           
        }
        return $placeholder;
    }

    public static function payStatusCircle($value){
        return ($value == '0')?'':'c-tot';
    }

    public static function invoiceStatusCircle($value){
        if($value == '0') {
            $invoice_status = '';
        }
        elseif($value == '1') {
             $invoice_status = 'c-half';
        }
        else {
            $invoice_status = 'c-tot';
        }
        return $invoice_status;
    }    

    public static function shipStatusCircle($value){
        if($value == '0') {
            $ship_status = '';
        }
        elseif($value == '1') {
             $ship_status = 'c-half';
        }
        else {
            $ship_status = 'c-tot';
        }
        return $ship_status;
    }

    public static function getOrderStatus($status_id) {
        $status = \App\OrderStatus::orderStatus($status_id);
        return @$status->orderStatusDesc->value;
    }

    public static function addDaysToDate($date,$numdays){
        return date('Y-m-d H:i:s', strtotime($date. ' + '.$numdays.' days'));
    }

    public static function dateDiffDetails($largeDate,$smallDate){
        $start_date = new \DateTime($smallDate);
        $since_start = $start_date->diff(new \DateTime($largeDate));
        $details = ['d'=>$since_start->d,'h'=>$since_start->h,'m'=>$since_start->i,'s'=>$since_start->s];
        return $details;
    }

    public static function getDateByTimezone($dt, $tz1, $df1, $tz2, $df2) {
      //echo '====>'.$dt.'==='.$tz1.'==='.$df1.'==='.$tz2.'==='.$df2;  
      // create DateTime object
      $d = \DateTime::createFromFormat($df1, $dt, new \DateTimeZone($tz1));
      // convert timezone
      $d->setTimeZone(new \DateTimeZone($tz2));
      // convert dateformat
      return $d->format($df2);
    }

    public static function waitingPayment(){
        $qry = \App\Orders::waitingPayment(Auth::User()->id);
        return $qry;
    }

    public static function waitingDelivery(){

        $qry = \App\Orders::waitingDelivery(Auth::User()->id);
        return $qry;
    }

    public static function completeOrders(){

        $qry = \App\Orders::completeOrders(Auth::User()->id);
        return $qry;
    }


    public static function productImageUrl($imageName, $user_id, $folder ='large_572'){

        $image = Config::get('constants.product_url').'noimages/product-no.jpg';
        $imagePath = Config::get('constants.product_url').$folder.'/'.$user_id.'/'.$imageName;

       /* if(!empty($imageName)){
            $image = $imagePath;
        }*/
        $check_file = @getimagesize($imagePath);

        if(isset($check_file[0]) && !empty($check_file[0])) {
              $image = $imagePath;
        }
        return $image;
    }
  
    public static function calculateTimeDifference($from, $to=null, $ype=1){

        if(empty($to)){
            $to = Carbon::now();

        }else{
            $to = new Carbon($to);

        }

        $returns_times = new Carbon($from);

       // echo $returns_times->diff($to)->h;

        if ($returns_times->diffInMinutes($to) <= 1 ) {
            $lastOnline = "a minute ago";
        } elseif ($returns_times->diffInHours($to) < 1) {
            $lastOnline = $returns_times->diffInMinutes($to) > 1 ? sprintf(" %d minutes ago", $returns_times->diffInMinutes($to)) : sprintf(" %d minute ago", $returns_times->diffInMinutes($to));
        } elseif ($returns_times->diffInDays($to) < 1) {
            $lastOnline = $returns_times->diffInHours($to) > 1 ? sprintf(" %d hours ago", $returns_times->diffInHours($to)) : sprintf(" %d hour ago", $returns_times->diffInHours($to));
        } elseif ($returns_times->diffInWeeks($to) < 1) {
            $lastOnline = $returns_times->diffInDays($to) > 1 ? sprintf(" %d days ago", $returns_times->diffInDays($to)) : sprintf(" %d day ago", $returns_times->diffInDays($to));
        } elseif ($returns_times->diffInMonths($to) < 1) {
            $lastOnline = $returns_times->diffInWeeks($to) > 1 ? sprintf(" %d weeks ago", $returns_times->diffInWeeks($to)) : sprintf(" %d week ago", $returns_times->diffInWeeks($to));
        } elseif ($returns_times->diffInYears($to) < 1) {
            $lastOnline = $returns_times->diffInMonths($to) > 1 ? sprintf(" %d months ago", $returns_times->diffInMonths($to)) : sprintf(" %d month ago", $returns_times->diffInMonths($to));
        } else {
            $lastOnline = $returns_times->diffInYears($to) > 1 ? sprintf(" %d years ago", $returns_times->diffInYears($to)) : sprintf(" %d year ago", $returns_times->diffInYears($to));
        }
        return $lastOnline;
    }


    public static function systemConfig($system_name=null) {
        $system_val = '';
        if(!empty($system_name)){ 
            $system = \App\SystemConfig::select('system_val')->where('system_name','=', $system_name)->first();
            $system_val = $system->system_val;
        }  
        return $system_val;         
    } 

    public static function getDefaultCountryDetail() {
        
        $def_country_code = self::getCountryByIp('country_code');
        //$def_country_code = 'IN';
        if(!empty($def_country_code)) {
            $def_country_dtl = Country::getCountryDetail($def_country_code, 'country_code');
        }
        else {
            $def_country_dtl = Country::getCountryDetail('', 'default');
        }

        return $def_country_dtl;
    }

    public static function getCountryByIp($return_type=null) {
        
        if(Config::get('constants.localmode') === true) {
            
            $data = '';
        }
        else {

            $user_ip = request()->ip();
            $ip_detail = file_get_contents('https://ipapi.co/'.$user_ip.'/json/');
            $ip_detail = json_decode($ip_detail);
            //dd($ip_detail);

            if($return_type === null) {
                $data = $ip_detail;
            }
            else if(isset($ip_detail->country) && $return_type == 'country_code') {
                $data = $ip_detail->country;
            }
            else{
                $data = '';
            }            
        }
        
        return $data;        
    }

    public static function getTotalReply($review_id) {
        return \App\BlogReview::totalReply($review_id);
    }




    public static function  getGoogleShortUrl($url){

        $target = 'https://www.googleapis.com/urlshortener/v1/url?';
        $extended = false;
        $apiKey ='AIzaSyDjwue1pEpGtGKz-k_KbcTlozDv4ezjoMw';

        if ( $apiKey != null ) {
            $apiKey = $apiKey;
            $target .= 'key='.$apiKey.'&';
        }


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $target);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $data = array( 'longUrl' => $url );
        $data_string = '{ "longUrl": "'.$url.'" }';

        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array('Content-Type: application/json'));
       // $ret = json_decode(curl_exec($ch));
        //curl_close($ch);
        //return $ret;
       if ( $extended) {
            $ret = json_decode(curl_exec($ch));
            curl_close($ch);

        } else {
             $ret = json_decode(curl_exec($ch))->id;
            //$ret = json_decode(curl_exec($ch))->longUrl;
            curl_close($ch);
            return $ret;

        }
    }

    public static function getSiteLogo($system_name) {
        //$logo_name =  getConfigValue($system_name);
        $logo_name = '';
        $logo_url =  Config('constants.site_logo_url').$logo_name;
        return $logo_url;
    }

    public static function getRMAExpiredStatus($created_at, $rma_status_id) {

        $rma_expired_days = self::systemConfig('RMA_EXPIRE_DAYS');

        $date_today = new \DateTime();
        $date_created = new \DateTime($created_at);
        $difference = $date_today->diff($date_created);
        //dd($created_at, $difference->d, $rma_expired_days);
        $rma_created_days = $difference->d;

        if(($rma_created_days>=$rma_expired_days) || $rma_status_id=='26') {
            return true;
        }
        else {
            return false;
        }        
    }

    public static function sectionData($page){

        $page = \Request::path();
         $static_left = $static_header = $static_right = $slider = $static_footer = 0;
          $section = \App\Block::getBlockByIdArr();
          $section_arr = [];
          foreach ($section as $key => $value) {
            $section_arr[$value->section_id][] = $value;
          }
          //dd($section_arr);
          $header_section = isset($section_arr[1]) ? $section_arr[1] : [];
          $left_section = isset($section_arr[2]) ? $section_arr[2] : [];
          $right_section = isset($section_arr[3]) ? $section_arr[3] : [];
          $main_section = isset($section_arr[4]) ? $section_arr[4] : [];
          $footer_section = isset($section_arr[5]) ? $section_arr[5] : [];
          //dd($header_section);
          $header_content = Self::checkSection($header_section,$page);
          $left_content = Self::checkSection($left_section,$page);
          $right_content = Self::checkSection($right_section,$page);
          $main_content = Self::checkSection($main_section,$page);
          $footer_content = Self::checkSection($footer_section,$page);
          //dd($main_content);
          if(count($header_content)){
            $static_header = 1;
          }
          if(count($left_content)){
            $static_left = 1;
          }
          if(count($right_content)){
            $static_right = 1;
          }
          if(count($footer_content)){
            $static_footer = 1;
          }

          if(count($main_content)){
            foreach ($main_content as $key => $value) {
              if(isset($value->banner_type) && $value->banner_type == 'slider'){
                $slider = 1;
              }
            }
          }

          return ['header'=>$static_header,'left'=>$static_left,'right'=>$static_right,'header_content'=>$header_content,'left_content'=>$left_content,'right_content'=>$right_content,'main_content'=>$main_content,'footer_content'=>$footer_content,'slider'=>$slider];
    }


    public static function checkSection($contentSection,$page=''){
        //$sec_id = 4;
        $block_id_arr = [];
        $groupCon = $pageCon = 0;
        $user_group_id = (Auth::check()) ? session('user_group_id') : 1;
        //dd($contentSection);
        //$contentSection = \App\Block::where('section_id',$sec_id)->orderBy('order_by')->get();
        if(count($contentSection)){
            foreach ($contentSection as $key => $section) {
                /***checking group*****/
                if($section->customer_group == 1){
                    $groupCon = 1;
                }elseif ($section->customer_group == 2) {
                   $checkGroup = \App\BlockCustomerGroup::checkGroup($section->id,$user_group_id);
                   $groupCon = ($checkGroup) ? 1 : 0;
                }else{
                    $checkGroup = \App\BlockCustomerGroup::checkGroup($section->id,$user_group_id);
                    $groupCon = ($checkGroup) ? 0 : 1;
                }

                if($groupCon){
                    /***checking pages****/
                    if($section->pages == 1){
                        $pageCon = 1;
                    }elseif ($section->pages == 2) {
                       $checkPage = \App\BlockPage::checkPage($section->id,$page);
                       $pageCon = ($checkPage) ? 1 : 0;
                    }else{
                        $checkPage = \App\BlockPage::checkPage($section->id,$page);
                        $pageCon = ($checkPage) ? 0 : 1;
                    }

                    if($pageCon){
                        $block_id_arr[$section->id] = $section;
                    }
                }

                if($groupCon && $pageCon){
                    switch($section->type){

                        case 'static-block' :
                        $static_block_desc = \App\StaticBlockDesc::where('static_block_id',$section->type_id)->where('lang_id',session('default_lang'))->first();
                        $contentSection[$key]->static_title = $static_block_desc->page_title;
                        $contentSection[$key]->static_desc = $static_block_desc->page_desc;
                        $contentSection[$key]->block_url_key = \App\StaticBlock::where('id',$section->type_id)->value('url');
                        break;

                        case 'banner' :
                        $banner_detail = \App\Banner::getBannerDetail($section->type_id);
                        $banner_type = (count($banner_detail) ==1) ? 'banner' : 'slider';
                        $contentSection[$key]['slider'] = $banner_detail;
                        $contentSection[$key]['banner_type'] = $banner_type;
                        $contentSection[$key]->block_url_key = \App\BannerGroup::where('id',$section->type_id)->value('group_name');
                    }
                }else{
                    unset($contentSection[$key]);
                }
                
            }


            return count($block_id_arr) ? $contentSection : [];
        }
        else
            return [];
    }

    public static function getBanner($banner_group_id){
        $banner_detail = \App\Banner::getBannerDetail($banner_group_id);
        $html = '';
        if(count($banner_detail)){
            foreach ($banner_detail as $key => $value) {
                # code...
            }
        }
        
    }

    public static function checkFixSection($sec_id,$page='',$block_key,$type){

        return \App\Block::checkBlockExist($sec_id,$page,$block_key,$type);
    }

    public static function userAttributeData($group_id, $show_on='2'){

        return \App\CustomerAttribute::attributeByGroup($group_id, $show_on);
    }

    public static function getPaymentInfo($orderInfo) {
        $orderId = $orderInfo->id;
        $paymentType = $orderInfo->paymentType->payment_type;
        
        if($paymentType == 1){ /**online**/
            $str = '<span class="name-detail not-log">Online ('.$orderInfo->paymentOptName->payment_option_name.')</span>';
            $str .= '<span class="name-detail block">Status : Paid</span>';
            $str .= '<span class="name-detail block">Transaction Id : '.$orderInfo->txn_id.'</span>';
        }else{  /**offline**/
            $str = '<span class="name-detail not-log block">Offline ('.$orderInfo->paymentOptName->payment_option_name.')</span>';
            $offPayInfo = \App\OrderOfflinePayment::where('order_id',$orderId)->with('toBank')->orderBy('id','Desc')->first();
            if(count($offPayInfo)){
                $str.= '<span class="name-detail block">'.Lang::get('checkout.name').' : '.$offPayInfo->name.'<span>';
                $str.='<span class="name-detail block">'.Lang::get('checkout.amount').' : '.numberFormat($offPayInfo->amount,$orderInfo->currency_id).' '.session('default_currency_code').'</span>';


                $str.= '<span class="name-detail block">'.Lang::get('checkout.to_account_no').' : '.$offPayInfo->to_account_no.' / '.$offPayInfo->toBank->bank_name.'<span>';
                $str .='<span class="name-detail block">'.Lang::get('checkout.transfer_date').' : '.$offPayInfo->transfer_date.'</span>';
                if($offPayInfo->file)
                    $str.='<span class="name-detail block"><a href="'.Config::get('constants.buyer_payment_url').$offPayInfo->file.'" target="_blank"><img src="'.Config::get('constants.buyer_payment_url').$offPayInfo->file.'" width="100" height="100"></a> <span>';
                if($offPayInfo->status == '1'){
                    $str.='<span class="name-detail block">Status : Paid</span>';
                }elseif($offPayInfo->status == '0'){
                    $str.='<span class="name-detail block">Status : Waiting for approve </span>';
                }else{
                    $str .='<span class="name-detail block">Status : Payment Rejected</span>';
                }
            }else{
                $str .='<span class="name-detail block">Status : Not Paid</span>';
                
            }
        }
        echo $str;
    }    




    

}
