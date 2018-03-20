<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Master;
use Illuminate\Http\Request;

class AdminController extends Master
{
    
    public function __construct()
    {
	     //$this->middleware('auth');
    }

    public function index() {
         return Master::Render('login.login');
    }

    public function login() {
         return Master::Render('login.login');
    }

}
