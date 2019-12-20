<?php

namespace App\Http\Controllers\Single;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SingleController extends Controller
{
    //详情页展示
    public function single(){

        return view('single.single');
    }

}
