<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$siteprefix = Request::segment(1);//'en';

if(empty($siteprefix)){
    $default_lang = \App\Model\Language::getDefaultLanguge();
    $redirect_url = Request::root().'/'.$default_lang->languageCode;
    return Redirect::to($redirect_url)->send();
    exit;

}else{

    $langprefix = \App\Model\Language::select('languageCode', 'id')->where('languageCode', $siteprefix)->first();

    if(empty($langprefix)){

        $default_lang = \App\Model\Language::getDefaultLanguge();
        $redirect_url = Request::root().'/'.$default_lang->languageCode;
        return Redirect::to($redirect_url)->send();
        exit;
    }
}  
/*
|--------------------------------------------------------------------------
| Front Section Start Here, all global Pages 
|--------------------------------------------------------------------------
|
| This value is the name of your application. This value is used when the
| framework needs to place the application's name in a notification or
| any other location as required by the application or its packages.
|
*/
Route::group(array('prefix' =>$siteprefix.'/'), function () {
    Route::get('/','HomeController@index');
	Route::get('/login','HomeController@Login');
});




/*
|--------------------------------------------------------------------------
| Admin Section Start Here 
|--------------------------------------------------------------------------
|
| This value is the name of your application. This value is used when the
| framework needs to place the application's name in a notification or
| any other location as required by the application or its packages.
|
*/
Route::group(array('prefix' =>$siteprefix.'/admin'), function () {
    Route::get('/','Admin\AdminController@index');
    Route::get('/login','Admin\Login\LoginController@login');
});
