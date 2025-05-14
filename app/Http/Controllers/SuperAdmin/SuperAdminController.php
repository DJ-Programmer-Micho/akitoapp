<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function dashboard(){
        return view('super-admins.pages.dashboards.index');
    }
    public function ticker(){
        return view('super-admins.pages.tickers.index');
    }
    public function brand(){
        return view('super-admins.pages.brands.index');
    }
    public function soon(){
        return view('super-admins.pages.comingsoon.index');
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
    public function intensity(){
        return view('super-admins.pages.intensities.index');
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
    public function driverTeam(){
        return view('super-admins.pages.driverteam.index');
    }
    public function driverTeamStore(){
        return view('super-admins.pages.driverteam.index-add');
    }
    public function driverTeamEdit($local, $id){
        return view('super-admins.pages.driverteam.index-edit',[
            "d_id" => $id
        ]);
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
    public function customerProfile($local, int $id){
        return view('super-admins.pages.customerprofile.index',[
            "c_id" => $id
        ]);
    }
    public function customerList(){
        return view('super-admins.pages.customerlist.index');
    }
    public function customerRanking(){
        return view('super-admins.pages.customerranking.index');
    }
    public function customerOrder($local, $id){
        return view('super-admins.pages.customerorder.index',[
            "p_id" => $id
        ]);
    }
    public function customerDiscount(){
        return view('super-admins.pages.customerdiscount.index');
    }

    public function settingLogo(){
        return view('super-admins.pages.setting.logo.index');
    }
    public function settingHero(){
        return view('super-admins.pages.setting.hero.index');
    }
    public function settingEmail(){
        return view('super-admins.pages.setting.email.index');
    }
    public function settingInfo(){
        return view('super-admins.pages.setting.information.index');
    }
    public function settingRecaptcha(){
        return view('super-admins.pages.setting.recaptcha.index');
    }
    public function settingBanner(){
        return view('super-admins.pages.setting.banner.index');
    }
    public function settingLanguage(){
        return view('super-admins.pages.setting.language.index');
    }
    public function settingPrice(){
        return view('super-admins.pages.setting.checkout.index');
    }
}