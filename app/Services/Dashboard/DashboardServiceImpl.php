<?php

namespace App\Services\Dashboard;

use App\Services\BaseServiceImpl;
use App\Contracts\DashboardService;
use App\Models\Company;
use App\Models\InvoiceHeader;
use App\Models\InvoiceDetail;
use App\Models\Partner;
use App\Models\PartnerCompany;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class DashboardServiceImpl extends BaseServiceImpl implements DashboardService
{
    public function fetchCompanies()
    {
        return $companies = Partner::with(['departments' => function($query) {
            $query->with('ticketHeaders')->orderBy('description');
        }])
            ->with('partner_company')
            ->where('partner_type_id', '7')
            ->where('status', 'A')
            ->get()
            ->sortBy(function($company) {
                return $company->partner_company->company_name;
            });
    }

    public function fetchProductSale(int $productId = null, int $companyId = null, $partnerTypeId = null, $startDate = null, $endDate = null)
    { 
        // $invoice = function($query) {
        //     $query->where('order_id', 'id');
        // };

        //Get Partner Id
        // $partnersId = Partner::where('company_id', $companyId)
        // ->when($partnerTypeId, function($query, $partnerTypeId){
        //     $query->where('partner_type_id',  $partnerTypeId);
        // })

        //Get Partners Id under company
        $partnersId = Partner::where('company_id', $companyId)
        ->when($partnerTypeId, function($query, $partnerTypeId){
            $query->where('partner_type_id',  $partnerTypeId);
        })
        ->where('status', 'A')
        ->pluck('id')
        ->toArray();
        
        //Get Products under company
        $products =  Partner::where('id', $companyId)
        ->with(['products'=> function($query) use ($productId){
            $query->when($productId, function($query, $productId){
                return $query->where('products.id', $productId);
        });
        }])->first();


    $invoiceHeaderIds = InvoiceHeader::whereIn('partner_id', $partnersId)
    ->when($startDate && $endDate, function($query, $startDate, $endDate){
        $query->whereSalesBetween('createted_at', [$startDate, $endDate]);
    })
    ->pluck('id')
    ->toArray();

    $invoiceDetails = InvoiceDetail::with('product:id,name')
        ->when($productId, function($query, $productId){
            $query->where('product_id', $productId);
        })
        ->select('product_id', \DB::raw('sum(amount) as total'))
        ->whereIn('invoice_id', $invoiceHeaderIds)
        ->groupBy('product_id')
        ->get();



    return $invoiceDetails;

    //    if($productId != null) { 
    //         $products =  Partner::where('id', $companyId)
    //             ->with('products')
    //             ->first();

    //         // $partnersId = Partner::where('parent_id', $companyId)
    //         //             ->orWhere('id', $companyId)
    //         //                 ->pluck('id')->toArray();
    //         $item = InvoiceHeader::whereIn('partner_id', $partnersId)
    //         ->where('status', 'P')
    //                 ->with(['details'
    //                     => function($query) use($products){
    //                         $query->whereIn('product_id', $products);
    //                     }
    //                 ])->get();
    //         $product = collect([]);
    //         $product->push($item); 
                    // foreach ($products->products as $product) {
                    //     $item = $product->whereHas('productOrders')
                    //         ->with(['productOrders'  => function($query) use($companyId, $partnersId){
                    //             $query->whereIn('partner_id', $partnersId)
                    //                 ->with(['invoiceHeaders'
                    //                     => function ($query){
                    //                         $query->where('status', 'P')
                    //                                 ->with('details');
                    //                     }
                    //                     ]);
                    //         }])->get();
                    //  foreach ($products->products as $product) {
                    //     $item = $product->whereHas('productOrders')
                    //         ->with(['productOrders'  => function($query) use($companyId, $partnersId){
                    //             $query->whereIn('partner_id', $partnersId)
                    //                 ->with(['invoiceHeaders'
                    //                     => function ($query){
                    //                         $query->where('status', 'P')
                    //                                 ->with('details');
                    //                     }
                    //                     ]);
                    //         }])->get();
                    //$d->with('invoiceHeaders');
                    //}])->with('invoiceHeaders');
                    // }
                    // ->with(['products' 
                    // => function($query){
                    //     $query->whereHas('productOrders');
                    // }])->get() :
                    //->where('partner_id', $companyId)
                    //->where('status', 'Application Signed')->get() :
                    //->partnerProductInvoice($companyId)->with('invoiceDetails')->where('partner_id', $companyId)->where('product_id', $productId)->get() :
        //     }
        // else{
        //     $products =  Partner::where('id', $companyId)
        //                         ->with('products')
        //                             ->first();

        //     //$partnersId = Partner::where('parent_id', $companyId)->orWhere('id', $companyId)->pluck('id')->toArray();

        //     $item = InvoiceHeader::whereIn('partner_id', $partnersId)
        //     ->where('status', 'P')
        //             ->with(['details'
        //                 => function($query) use($products){
        //                     $query->whereIn('product_id', $products);
        //                 }
        //             ])->get();

        //     //$product = collect([]);
        //     $product = $item;  
           
        //     }
            
            // $product  =  Partner::where('id', $companyId)->where('status', 'A')
            // ->with(['products' =>function($query){
            //     $query->with(['invoiceHeaders'
            //     =>function ($query){
            //             $query->with('details');
            //     }]);
            //     }])
            // ->first();
            // ->map(function ($row) {
            //     return $row->groupBy('description');
            //     });
            // ->sum(function($partner){
            //     return $partner->products['buy_rate'];
            // });
        
            // $cmd = 'SELECT * FROM partner p
            // ';
            // return $products = DB::select(DB::raw($cmd));;
            // return $products = Partner::with(['products' => function($query){
            //    $query->pluck('products.id', 'products.name')->groupBy('products.id', 'products.name');
            // }])->where('partners.id', $companyId)
            // ->get();
            // $invoiceDetails = collect([]);
            // foreach($product as $p){
            //     foreach($p->details as $prod){
            //         $invoiceDetails->push($prod);
            //     };
            // }
            //return $invoiceDetails;
    }

    public function fetchProductSaleBar(int $productId = null, int $companyId = null, $partnerTypeId = null, $startDate = null, $endDate = null)
    {
         //Get Partners Id under company
         $partnersId = Partner::where('company_id', $companyId)
         ->when($partnerTypeId, function($query, $partnerTypeId){
             $query->where('partner_type_id',  $partnerTypeId);
         })
         ->where('status', 'A')
         ->pluck('id')
         ->toArray();
         
         //Get Products under company
         $products =  Partner::where('id', $companyId)
         ->with(['products'=> function($query) use ($productId){
             $query->when($productId, function($query, $productId){
                 return $query->where('products.id', $productId);
         });
         }])->first();
 
 
        $invoiceHeaderIds = InvoiceHeader::whereIn('partner_id', $partnersId)
        ->when($startDate && $endDate, function($query, $startDate, $endDate){
            $query->whereSalesBetween('createted_at', [$startDate, $endDate]);
        })
        ->pluck('id')
        ->toArray();
 
        $invoiceDetails = InvoiceDetail::with('product:id,name')
     
         ->select('product_id', \DB::raw('sum(amount) as total'), 
         \DB::raw("CONCAT_WS('-',MONTH(created_at),YEAR(created_at)) as monthyear"))
         ->whereIn('invoice_id', $invoiceHeaderIds)
         ->groupBy('monthyear', 'product_id')
         ->get();
 
        return $invoiceDetails->groupBy('monthyear');
    }

    public function fetchPartnerSale(int $partnerId = null, int $productId = null, $startDate = null, $endDate = null)
    {
        $partner = Partner::where('id', $partnerId)
            ->where('status', 'A')
            ->with(['products' =>function($query){
                $query->with(['invoiceDetails' => function($query) {
                   //$query->where('product_id', $productId) 
                }]); 
            }])
            ->first();

        $products = collect([]);
        foreach ($partner->products as $product) {
            $item = $product->invoiceDetails->groupBy(function($d) {
                return Carbon::parse($d->created_at)->format('m');
            })
                ->map(function ($row) {
                return $row->groupBy('description');
                });
                    // ->map(function ($row) {
                    //     return $row->sum('amount');
                    // });
            };

            $products->push([
                'data'=> $item,
                'sum' => $item->sum('amount')
            ]); 
        
       
   
        return $products;
    }

    public function fetchAgentSales()
    {
        $cmd = "select pc.company_name,pc.partner_id,sum(ih.total_due) as total from invoice_headers ih
                inner join partners p on p.id = ih.partner_id
                inner join partners pp on p.parent_id = pp.id
                inner join users u on (u.reference_id = pp.id and u.is_original_partner = 1)
                inner join partner_companies pc on u.company_id = pc.partner_id
                where ih.status = 'P' and ih.invoice_date <= NOW() - INTERVAL 0 DAY
                and ih.invoice_date >= NOW() - INTERVAL 60 DAY
                group by pc.company_name,pc.partner_id
                order by total desc";

        $agentSales = DB::select(DB::raw($cmd));
    }

    public function fetchCompanySales()
    {

    }

    public function fetchCompanyTickets()
    {

    }

    public function fetchCompanyProducts()
    {

    }
}