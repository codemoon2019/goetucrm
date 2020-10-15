<?php

namespace App\Services\Products;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class InternalProductAccessor
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
        $userTypeIds = $user->userTypes->pluck('id')->all();
        return DB::table('products')
            ->select($this->columns)
            ->distinct()
            ->join(
                'partner_companies',
                'partner_companies.partner_id', '=', 'products.company_id')
            ->join(
                'user_type_product_accesses',
                'user_type_product_accesses.product_id', '=', 'products.id')
            ->whereIn('user_type_product_accesses.user_type_id', $userTypeIds)
            ->where('status', 'A')
            ->get();
    }
}