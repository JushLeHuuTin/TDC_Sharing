<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    //
    function categories(){
        return view('pages.admin.categoryManage');
    }
}
