<?php

namespace App\Services\Products;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class PartnerProductAccessor
{
    protected $columns = [
        'products.id',
        'products.display_picture',
        'products.name',
        'products.code',
        'products.company_id',
        'partner_companies.company_name',
    ];

    public function getProducts(User $user)
    {
        return DB::table('products')
            ->select($this->columns)
            ->distinct()
            ->join(
                'partner_companies',
                'partner_companies.partner_id', '=', 'products.company_id')
            ->join(
                'products as sub_products', 
                'products.id', '=', 'sub_products.parent_id')
            ->join(
                'partner_products',
                'partner_products.product_id', '=', 'sub_products.id')
            ->join(
                'user_type_product_accesses',
                'user_type_product_accesses.product_id', '=', 'products.id')
            ->where('partner_products.partner_id', $user->reference_id)
            ->where('products.status', 'A')
            ->where('sub_products.status', 'A')
            ->get();
    }
}