<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MerchantWelcomeEmail;
use App\Models\SubTaskTemplateHeader;
use App\Models\ProductTemplateHeader;
use App\Models\Product;
use Yajra\Datatables\Datatables;
use Cache;

class InventoryController extends Controller
{
    public function index(){
        return view("inventory.purchaseorder");
    } 

    public function receivingpurchaseorder(){
        return view("inventory.receivingpurchaseorder");
    }
    
}
