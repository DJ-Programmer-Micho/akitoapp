<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function dashboard(){
        return view('super-admins.pages.dashboards.index');
    }
    public function brand(){
        return view('super-admins.pages.brands.index');
    }
    public function category(){
        return view('super-admins.pages.categories.index');
    }
    public function tag(){
        return view('super-admins.pages.tags.index');
    }
    public function color(){
        return view('super-admins.pages.colors.index');
    }
    public function size(){
        return view('super-admins.pages.sizes.index');
    }
    public function material(){
        return view('super-admins.pages.materials.index');
    }
    public function capacity(){
        return view('super-admins.pages.capacities.index');
    }
    public function tProduct(){
        return view('super-admins.pages.tproducts.index');
    }
    public function cProduct(){
        return view('super-admins.pages.cproducts.index');
    }
    public function eProduct($local, $id){
        return view('super-admins.pages.eproducts.index',[
            "p_id" => $id
        ]);
    }
    public function recommendProduct(){
        return view('super-admins.pages.recommendproduct.index');
    }
    public function recommendProductEdit($local, $id){
        return view('super-admins.pages.recommendproductedit.index',[
            "p_id" => $id
        ]);
    }
    public function adjustProduct(){
        return view('super-admins.pages.adjustproducts.index');
    }
    public function user(){
        return view('super-admins.pages.users.index');
    }
    public function profile(){
        return view('super-admins.pages.profiles.index');
    }
    public function orderManagements(){
        return view('super-admins.pages.order.index');
    }
    public function orderManagementsViewer($local, $id){
        return view('super-admins.pages.orderviewer.index',[
            "p_id" => $id
        ]);
    }
    public function orderInvoice($local, $id){
        return view('super-admins.pdf.orderinvoice.index',[
            "p_id" => $id
        ]);
    }
    public function deliveryZones(){
        return view('super-admins.pages.delivery.index');
    }
    public function shippingCost(){
        return view('super-admins.pages.shippingcost.index');
    }
}