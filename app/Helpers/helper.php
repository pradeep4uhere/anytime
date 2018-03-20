<?php

if (! function_exists('fooFormatText')) {
	function fooFormatText()
	{
			dd('smoothgraph');
	}
}

if (! function_exists('defaultTheme')) {
	function defaultTheme() {
		$defaultTheme = (session('default_theme') !==null) ? session('default_theme') : '';
		return $defaultTheme;
	}
}

if (! function_exists('loadFrontTheme')) {
	function loadFrontTheme($path) {
		if(session('default_theme') == null){
			$default_theme = \App\SystemConfig::getSystemVal('DEFAULT_THEME');
			\Session::put('default_theme', $default_theme);
		}
		return session('default_theme').'.'.$path;
	}
}

function getDateFormat($date_time, $type=Null) {
    
    switch ($type) {
        case '1':
            $date_return = date('M d Y h:i a', strtotime($date_time));
            break;
        case '2':
            $date_return = date('M d / Y', strtotime($date_time));
            break;
        case '3':
            $date_return = date('d M Y', strtotime($date_time));
            break;                
        case '4':
            $date_return = date('M d, Y h:i A', strtotime($date_time));
            break;
        case '5':
            $date_return = date('d M, Y', strtotime($date_time));
            break;  
        case '6':
            $date_return = date('M d, Y', strtotime($date_time));
            break;                  
        case 'Y':
            $date_return = date('Y', strtotime($date_time));
            break;
        default:
            $date_return = date('d/m/Y', strtotime($date_time));
            break;             
    }
    
    return $date_return;
}

function getcommentDateFormat($date_time) {

    $current_date = date('Y-m-d');
    $db_date = date('Y-m-d', strtotime($date_time));
    if($current_date == $db_date) {
        $date_diff = time()-strtotime($date_time);
        $hour = floor($date_diff/3600);
        $minute = floor(($date_diff-($hour*3600))/60);

        $date_return = $hour?$hour.' hours '.$minute.' minutes ago':$minute.' minutes ago';
    }
    else {
        $date_return = date('d M, Y', strtotime($date_time));
    }
    return $date_return;
}

if (! function_exists('getCartProduct')) {
	function getCartProduct() {
		$cartDetail=[];
		if(Auth::check()){
			$userid = Auth::User()->id;
			$cartDetail = \App\Cart::select('id')->where(['user_id'=>$userid,'order_status'=>'0'])->count();
		}
		else{
			$sessionId = Session::getId();
			$cartDetail = \App\Cart::select('id')->where(['session_id'=>$sessionId,'order_status'=>'0'])->count();
		}
		return $cartDetail;
	}
}

if (! function_exists('getStarRatingImage')) {
	function getStarRatingImage($starValue) {
		$html = '';
		if($starValue){
			$exp = explode('.',$starValue);
			for($i=0; $i < $exp[0]; $i++){
				$html .= '<span class="icon-remove"></span>';
			}
			if(isset($exp[1])){
				$html .= '<span class="glyphicon glyphicon-search"></span>';
			}
		
		}
		return $html;
	}
}

if (! function_exists('getUserImage')) {
	function getUserImage($imgValue) {

		if($imgValue)
					$imgSrc =  Config::get('constants.users_url').$imgValue;
				else
					$imgSrc =  Config::get('constants.users_default_url').'default-image-95x95.png';
		
		return $imgSrc;
	}
}

if (! function_exists('getTotalOrder')) {
	function getTotalOrder() {
		$ordToata=1;
		/*if(Auth::check()){
			$userid = Auth::User()->id;
			$ordToata = \App\OrdersTemp::select('id')->where(['user_id'=>$userid,'order_status'=>'0'])->count();
		}
		else{
			$sessionId = Session::getId();
			$ordToata = \App\OrdersTemp::select('id')->where(['session_id'=>$sessionId,'order_status'=>'0'])->count();
		}*/
		return $ordToata;
	}
}


// here $value is price $currency1 is from currency and $currency2 is to currency
if (! function_exists('convertCurrency')) {
	function convertCurrency($value,$currency1,$currency2) { /**$currency2 = session currency id**/

		$currency1Value = \App\Currency::select('currency_value')->where('id',$currency1)->first();
		
		@$dollerValue = $value*(1/$currency1Value->currency_value);
			
		$currency2Value = \App\Currency::select('currency_value')->where('id',$currency2)->first();
		$return = $dollerValue*$currency2Value->currency_value;
			return round($return,4);
	}
}

if (! function_exists('numberFormat')) {
	function numberFormat($price, $currencyId=Null)
	{
		$currencyVal = $price;
		//$currencyVal = ($currencyId) ? convertCurrency($price,$currencyId,session('default_currency_id')) : $price;
		
		return number_format($currencyVal,2);
	}
}

if (! function_exists('getUpdatedPrice')) {
	function getUpdatedPrice($old_value, $old_currency_val, $new_currency_val)
	{
        $doller_value = $old_value*(1/$old_currency_val);
        $new_value = $doller_value*$new_currency_val;
        $updated_price = round($new_value,4);
        return $updated_price;
	}
}

if (! function_exists('printData')) {
	function printData($data)
	{
			return strip_tags($data);
	}
}

if(!function_exists('getAllActiveLang')){
	function getAllActiveLang(){
		$languages = \App\Language::where('status', '1')->orderBy('id', 'asc')->select(['id','languageCode','languageFlag'])->get()->toArray();
		return $languages;
	}
}

if( ! function_exists('generateReadMoreLink')){

	function generateReadMoreLink($text, $limit, $url, $readMoreText = 'Read More') {

			$end = "<br><br><a href=\"$url\">$readMoreText</a>";

			return str_limit($text, $limit, $end);
	}

}

function isMobile() {

	$checkMobile = preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|iPad|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);

	return $checkMobile;

}

function createTree(&$list, $parent){
	$tree = array();
	foreach ($parent as $k=>$l){
			$l['checked'] = false;
			$l['name'] = $l['categorydesc']['name'];
			$l['children'] = [];
			if(isset($list[$l['id']])){
					$l['children'] = createTree($list, $list[$l['id']]);

			}
			$tree[] = $l;
	} 
	return $tree;
}

function getAllMarketplaceCat(){
	$cat_data_set = \App\Category::where('status','1')->with('categorydesc')->select(['id','url','custom_url','parent_id'])->get()->toArray();
					//dd($cat_data_set);
			if(count($cat_data_set)){
					foreach ($cat_data_set as $a){
							$new[$a['parent_id']][] = $a;
					}
					$tree = createTree($new, $new[0]); // changed         
					return $tree;    
			}
			else{
					return [];
			}
}

function getSellerConfig(){
	
	return \App\Shop::where('id',session('shop_id'))->select('product_option','product_specification','product_weight_dimention','seo_product')->first();
	
}

function cleanValue($value) {
	return trim(filter_var($value, FILTER_SANITIZE_STRING));
}
			
function getYoutubeId($url){
	parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
	return $my_array_of_vars['v'];    
}  

function getVimeoId($vimeo_url){
	if(preg_match("/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/", $vimeo_url, $output_array)) {
		return $output_array[5];
	}
}

if(!function_exists('getCallByActionTitle')){
	function getCallByActionTitle($callinactionid, $default_langid){
		$call_in_action = \App\CallToActionDesc::where(['action_id'=>$callinactionid,'lang_id'=>$default_langid])->select('title')->first();
		if(count($call_in_action)){
			return $call_in_action->title;
		}      
	}
}

if(!function_exists('getCurrencyCode')){
	function getCurrencyCode($currency_id){
		$curr = \App\Currency::where('id',$currency_id)->first();
		//dd($curr,$curr->currency_code);
		if(isset($curr->currency_code)){
			return $curr->currency_code;	
		}else{
			
		}
			
	}
}

if(!function_exists('getProductBlogUrl')){
	function getProductBlogUrl($urlArr){
		if(isset($urlArr['type']) && $urlArr['type']=='blog'){
			$url = action('BlogDetailController@display',$urlArr['id']);
		}else{
				$url = action('ProductDetailController@display',$urlArr['id']);
		}
		return $url;
	}
}


/* 
Params $URL - url to hit , $reqmethod -request method like post,get etc, $datatosend - data to send in post request , $datatype - data type which you are sending like application/json ...
*/

if(!function_exists('CallExternalPDFAPI')){
	function CallExternalPDFAPI($requestarray){
		$url = $requestarray['url'];
		$reqmethod = $requestarray['reqmethod'];
		
		$datatype = $requestarray['datatype'];
		if(isset($requestarray['datatosend'])){
			$data = $requestarray['datatosend'];
			$data_string = json_encode($data);
		}else{
			$data_string = '';
		}
		
		$ch = curl_init($url);             
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $reqmethod);                   
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                   
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                       
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                  
		    'Content-Type: '.$datatype,                                              
		    'Content-Length: '.strlen($data_string))
		);                        
		$response = curl_exec($ch);
		return $response;
	}
}

function getFilesCount($path){
	$fnames = scandir($path);
	$imgCount = 0;
	foreach($fnames as $file){
		if(is_file($path.'/'.$file)){
			$imgCount++;	
		}
	}
	return $imgCount;
}

function currentDateTime($time='Y') {
	if($time=='N') {
		return date('Y-m-d');
	}
	else {
		return date('Y-m-d H:i:s');
	}
}

function userRequireRule($validation_type){
	$validation_rule = '';
	switch ($validation_type) {
		case 'name':
			$validation_rule = nameRule();
			break;
		case 'email':
			$validation_rule = emailRule();
			break;
		case 'date':
			$validation_rule = dateRule();
			break;
		case 'number':
			$validation_rule = numberRule();
			break;
		case 'image':
			$validation_rule = imageRule();
			break;
		default:
			$validation_rule = 'Required';
			break;
	}

	return $validation_rule;
}

//validation functions
function titleRule() {
	return 'Required|Min:3|Max:100';
}

function numberRule() {
	return 'Required|numeric';
}

function imageRule($width=null,$height=null,$max_size=null){
	
	if(!empty($width) && !empty($height) && !empty($max_size)){
		return 'Required|mimes:jpeg,jpg,png,gif|max:'.($max_size*1000).'|dimensions:min_width='.$width.',min_height='.$height;
	}else if(!empty($width) && !empty($height)){
		return 'Required|mimes:jpeg,jpg,png,gif|dimensions:min_width='.$width.',min_height='.$height;
	}else if(!empty($max_size)){
		return 'Required|mimes:jpeg,jpg,png,gif|max:'.($max_size*1000);
	}else if(!empty($width)){
		return 'Required|mimes:jpeg,jpg,png,gif|dimensions:min_width='.$width;
	}else if(!empty($height)){
		return 'Required|mimes:jpeg,jpg,png,gif|dimensions:min_height='.$height;
	}else{
		return 'Required|mimes:jpeg,jpg,png,gif';
	}	
}

function nameRule() {
	return 'Required|Min:3';
}

function AddressRule() {
	return 'Required|Min:5';
}

function contactNoRule() {
	return 'Required|Min:3|numeric';
}

function numericRule() {
	return 'numeric';
}

function emailRule($table_name='', $field='') {
	if($table_name != '' && $field != '') {
		return 'Required|email|unique:'.$table_name.','.$field;
	}
	else {
		return 'Required|email';
	}
}

function passwordRule() {
	return 'Required|Min:6';
}

function confirmPasswordRule($password_field_name) {
	return 'Required|Min:6|same:'.$password_field_name;
}

function arrayRule($min__val=1) {
	return 'Required|array|Min:'.$min__val;
}

function dateRule(){
	return 'Required|date';
}

function zipRule(){
	return 'Required|Min:3|numeric';
}

//validation functions ends

function pageClass($left='',$right=''){
	$sideClass = '';
      if($left && $right){
        $class = 'content col-md-8 col-sm-6';
        $sideClass = 'content-sidebar';
      }elseif($left || $right){
        $class = 'content col-md-10 col-sm-9';
        $sideClass = 'content-sidebar';
      }else{
        $class = 'content';
      }

    return ['main'=>$class,'sideClass'=>$sideClass];
}	

function themeUrl($folder,$file){
	return \Config::get('constants.theme_url').session('default_theme').'/'.$folder.'/'.$file;
}

function themeJsUrl($folder,$file){
	return \Config::get('constants.theme_url').session('default_theme').'/'.$folder.'/'.$file;
}

if(!function_exists('getProductUrl')){
	function getProductUrl($url){
		return action('ProductDetailController@display',$url);
	}
}

if(!function_exists('getBlogDetailUrl')){
	function getBlogDetailUrl($url){
		return action('BlogController@blogDetails',$url);

		//return session('lang_code').'/blog/'.$url;
	}
}

if(!function_exists('getProductTableUrl')){
	function getProductTableUrl($productname, $sku){
		return str_slug($productname,'-')."/".$sku;
	}
}

function getAttributeUserValue($attribute_id) {
	//dd($attribute_id, Auth::id());
	$return_value = '';
	$user_data = \App\UserAttribute::attributeUserValue($attribute_id, Auth::id());
	if(count($user_data) > 0) {
		if($user_data->attribute_value_id > 0) {
			$return_value = $user_data->attribute_value_id;
		}
		else {
			$return_value = $user_data->attribute_value;
		}
	}
	//dd($return_value);
	return $return_value;
}

function getDOBValue($attribute_id) {
	$dob_arr['y'] = '00'; $dob_arr['m'] = '00'; $dob_arr['d'] = '00';
	$dob = getAttributeUserValue($attribute_id);
	if(!empty($dob)) {
        $dob_ymd_arr = explode('-', $dob);
        $dob_arr['y'] = $dob_ymd_arr['0'];
        $dob_arr['m'] = $dob_ymd_arr['1'];
        $dob_arr['d'] = $dob_ymd_arr['2'];		
	}
	return $dob_arr;
}
function attrValImgUrl($image_name) {
	
	$attr_image_path = Config::get('constants.color_path').'/'.$image_name;
	if(!empty($image_name) && file_exists($attr_image_path)) {
	    $attr_image_url = Config::get('constants.color_url').$image_name;
	}
	else {
	    $attr_image_url = GeneralFunctions::getPaceholderImage('PRODUCT_IMAGE');
	}
	return $attr_image_url;	
}

function getProductImageUrl($image_name, $size='') {
	
	if($image_name && $size){
		$prd_url = Config::get('constants.product_url').$size.'/'.$image_name;
		$prd_path = Config::get('constants.product_path').'/'.$size.'/'.$image_name;
	}else {
		$prd_url = Config::get('constants.product_url').'thumb_105145/'.$image_name;
		$prd_path = Config::get('constants.product_path').'/thumb_105145/'.$image_name;
	}
	
	//dd($prd_path);
	if(file_exists($prd_path) && $image_name){
			return $prd_url;
	}else{
		$size_name = getSizeName($size);
		return GeneralFunctions::getPlaceholderImage($size_name);
	}
}

function getSizeName($size){
	if($size){
		switch($size){
			case 'large_405' :
				$size_name = 'PRODUCT_IMAGE_405';
				break;
			case 'medium_265360' :
				$size_name = 'PRODUCT_IMAGE_265x360';
				break;
			case 'thumb_105145' :
				$size_name = 'PRODUCT_IMAGE_105x145';
				break;
			case 'thumb_185185' : 
				$size_name = 'PRODUCT_IMAGE_185x185';
			case 'original':
				$size_name = 'PRODUCT_IMAGE';
				break;
		}
	}else{
		$size_name = 'PRODUCT_IMAGE_105x145';
	}
	return $size_name;
}

function getBlogImageUrl($image_name, $dir_name) {

	$blog_image_path = Config::get('constants.blog_path').'/'.$dir_name.'/'.$image_name;
	if(!empty($image_name) && file_exists($blog_image_path)) {
	    $blog_image_url = Config::get('constants.blog_url').$dir_name.'/'.$image_name;
	}
	else {
	    $blog_image_url = GeneralFunctions::getPlaceholderImage('BLOG_IMAGE');
	}
	return $blog_image_url;	
}

function getUserImageUrl($image_name='', $gender='') {
	$users_image_path = Config::get('constants.user_path').'/'.$image_name;
	if(!empty($image_name) && file_exists($users_image_path))
		$user_image_url = Config::get('constants.user_url').$image_name;
	elseif($gender == 'F')
	  	$user_image_url = GeneralFunctions::getPlaceholderImage('USER_IMAGE_FEMALE');
	else
		$user_image_url = GeneralFunctions::getPlaceholderImage('USER_IMAGE');

	return $user_image_url;
}

function generatedDD($value){
	$ddArr[] = ['key'=>'', 'value'=>'Please select'];
	foreach ($value as $key => $result) {
		$ddArr[] = ['key'=>$key, 'value'=>Lang::get($result)];
	}
	return $ddArr;
}

if(!function_exists('getFilterAttribute')){
	function getFilterAttribute(){

		// $max_price = \App\ProductPrice::max('special_price')->get();
		// dd($max_price);
		//$max_price = DB::table(with(new \App\ProductPrice)->getTable())->where('currency_id',Session::get('default_currency_id'))->max('special_price');
		$max_price = 900000000;

		return $filter_attributes = [

				/* star ratting */
				[
					'attr_id' => 5,
					'attr_name'=>'Product Type',
					'attr_type' => 'checkbox',
					'attr_value'=>[['slug'=>'blog'],['slug'=>'product'],['slug'=>'nonsalable']]
				],
				[
					'attr_id' => 2,
					'attr_name' => 'Price',
					'attr_type' => 'slider',
					'currency'  => Session::get('default_currency_code'),
					'attr_value'=> ['min_price'=>0,'max_price'=>$max_price] // will come from product db
				],
				[
					'attr_id' => 1 ,
					'attr_name' => 'Review',
					'attr_type'=>'starrating',
					'attr_value'=>['rating_min'=>0,'rating_max'=>5]
				]      
				
				// [
				// 'attr_id' => 3,
				// 'attr_name'=>'Shipping On time',
				// 'attr_type'=>'tab_button',
				// 'attr_value' => [['key'=>'Low','value'=>20,'smilystr'=>'icon-sad'],['key'=>'Normal','value'=>40,'smilystr'=>'icon-neutral'],['key'=>'Good','value'=>60,'smilystr'=>'icon-smile'],['key'=>'Great','value'=>80,'smilystr'=>'icon-smile'],['key'=>'Best','value'=>100,'smilystr'=>'icon-grin']]      
				// ],
				// [
				//   'attr_id' => 4,
				//   'attr_name' => 'Shipping to buyer country',
				//   'attr_type' => "checkbox",
				//   'attr_value'=>[['slug'=>'US'],['slug'=>'UK']]
				// ],
				
		];



		
	 }
}
if(!function_exists('getFilterSortingAttribute')){
	function getFilterSortingAttribute(){
		return $shorting_attributes = [
				[
					'attr_id'=>10,
					'attr_name'=>'Price',
					'attr_val_text' =>['higher to lower','lower to higher'],
					'order'=>['desc','asc']        
				],
				[
					'attr_id'=>11,
					'attr_name'=>'Spin',
					'attr_val_text' =>['higher to lower','lower to higher'],
					'order'=>['desc','asc']
				],
				// [
				//   'attr_id'=>12,
				//   'attr_name'=>'View',
				//   'attr_val_text' =>['higher to lower','lower to higher'],
				//   'order'=>['desc','asc']
				// ],
				[
					'attr_id'=>13,
					'attr_name'=>'Like',
					'attr_val_text' => ["higher to lower",'lower to higher'],
					'order' => ['desc','asc']
				],
				[
					'attr_id'=>13,
					'attr_name'=>'Share',
					'attr_val_text' => ["higher to lower",'lower to higher'],
					'order' => ['desc','asc']
				],
				[
					'attr_id'=>14,
					'attr_name'=>'Review',
					'attr_val_text' => ["higher to lower",'lower to higher'],
					'order' => ['desc','asc']
				],
				[ 
					'attr_id'=>15,
					'attr_name'=>'Name',
					'attr_val_text' => ["higher to lower",'lower to higher'],
					'order' => ['desc','asc']
				]

			];
	}
}

function createInvoiceInventoryShipment($order_formated_id) {

    $orders = \App\Orders::getOrderDetailInvoice($order_formated_id);
    if(count($orders) > 0) {

        //generate invoice
        $inv_obj = new \App\OrderInvoice;
        $inv_obj->order_id = $orders->id;
        $inv_obj->status = '1';
        $inv_obj->save();

        $orders_prod_arr = array();
        $ttl_qty = 0;
        foreach ($orders->orderDetailsInvoice as $value) {
            $orders_prod_arr[] = ['invoice_id'=>$inv_obj->id, 'order_id'=>$orders->id, 'product_id'=>$value->product_id, 'quantity'=>$value->quantity];

            $ttl_qty += $value->quantity;
        }
        //dd($orders_prod_arr); 
        \App\OrderInvoiceDetail::insert($orders_prod_arr);

        $inv_formated_id = \App\OrderInvoice::getInvoiceFormattedId().$inv_obj->id;
        \App\OrderInvoice::where(['id'=>$inv_obj->id])->update(['formatted_id'=>$inv_formated_id, 'total_qty'=>$ttl_qty]);

        //generate inventory
        $inventory_obj = new \App\OrderInventory;
        $inventory_obj->order_id = $orders->id;
        $inventory_obj->invoice_id = $inv_obj->id;
        $inventory_obj->status = '1';
        $inventory_obj->save();

        $inventory_formated_id = \App\OrderInventory::getInventoryFormattedId().$inventory_obj->id;
        \App\OrderInventory::where(['id'=>$inventory_obj->id])->update(['formatted_id'=>$inventory_formated_id]);

        //generate shipment
        $shipment_obj = new \App\OrderShipment;
        $shipment_obj->order_id = $orders->id;
        $shipment_obj->invoice_id = $inv_obj->id;
        $shipment_obj->inventory_id = $inventory_obj->id;
        $shipment_obj->status = '1';
        $shipment_obj->save();

        $shipment_formated_id = \App\OrderShipment::getShipmentFormattedId().$shipment_obj->id;
        \App\OrderShipment::where(['id'=>$shipment_obj->id])->update(['formatted_id'=>$shipment_formated_id]);            
    }	
}

function getDepartmentName($role_id) {
	return \App\RoleDepartment::getDepartmentName($role_id);
}

function getPagination($type='') {
	if($type == 'limit') {
		$limit = 50;
		return $limit;
	}
	else {
	   	$limit_opt = array(10,20,50,100,200);
	   	foreach($limit_opt as $value){
	       $data[] = array('key' => $value, 'value' => $value);
	   	}
	   	return json_encode($data);		
	}
}

function tableGeneralAction(){
	$general_actions['edit'] = Lang::get('common.edit');
    $general_actions['view'] = Lang::get('common.view');
    $general_actions['delete'] = Lang::get('common.delete');
    return json_encode($general_actions);
}

function getBulkActionOption(){

	return json_encode([
			['id'=>0,'name'=>'-- Please select --'],
			['id'=>1,'name'=>'Active'],
			['id'=>2,'name'=>'Inactive'],
			['id'=>3,'name'=>'Delete']
		]);
}

function getSiteLogo($system_name) {
    $logo_name =  GeneralFunctions::systemConfig($system_name);
    $logo_url =  Config::get('constants.site_logo_url').$logo_name;
    return $logo_url;
}