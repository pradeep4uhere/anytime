<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Master;
use Illuminate\Http\Request;

class HomeController extends Master
{
    
    public function __construct()
    {
         //$this->middleware('auth');
    }

    public function index() {
         return view(Master::loadFrontTheme('home.index'));
    }

    public function login() {
         return Master::Render('login.login');
    }

}
