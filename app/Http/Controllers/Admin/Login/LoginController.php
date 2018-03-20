<?php
namespace App\Http\Controllers\Admin\Login;
use App\Http\Controllers\Master;
use Illuminate\Http\Request;

class LoginController extends Master
{
    
    public function __construct()
    {
	     //$this->middleware('auth');
    }

    public function login() {
         return Master::Render('login.login');
    }

}
