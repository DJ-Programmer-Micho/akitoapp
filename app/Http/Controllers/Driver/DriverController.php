<?php

namespace App\Http\Controllers\Driver;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DriverController extends Controller
{
    public function allDriverTask(){
        return view('super-admins.pages.tasks.alldrivertasks.index');
    }
    public function driverTask(){
        return view('super-admins.pages.tasks.drivertasks.index');
    }
}
