<?php
// define path
$public_path = public_path();           // /var/www/html/marketadmin/public
$files_path = $public_path.'/files';
$base_path = base_path();               // /var/www/html/marketadmin

$public_url = env('APP_URL');    // http://marketadmin.localhost/
$files_url = $public_url.'files/';
$assets_url = $public_url.'assets/';

$localmode = false;
if(env('APP_ENV') == 'local') {
    $localmode = true;
}

return [
    'THEME'=>'prssystem',
    'ADMIN_URL_CSS'=>$public_url.'/public/assets/css/',
    'ADMIN_URL_JS'=>$public_url.'/public/assets/js/',
	'ADMIN_URL_IMAGE'=>$public_url.'/public/assets/img/',

    'THEME_URL_CSS'=>$public_url.'/public/theme/prssystem/css/',
    'THEME_URL_JS'=>$public_url.'/public/theme/prssystem/js/',
    'THEME_URL_IMAGE'=>$public_url.'/public/theme/prssystem/img/',
];

?>