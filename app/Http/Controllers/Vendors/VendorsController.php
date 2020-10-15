<?php

namespace App\Http\Controllers\Vendors;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VendorsController extends Controller
{
    public function index(){
        return view("vendors.list");
    }
    public function create(){
        return view("vendors.create");
    }
    public function profile(){
        return view("vendors.details.profile");
    }
    public function contacts(){
        return view("vendors.details.contact");
    }
    public function products(){
        return view("vendors.details.products");
    }
}
