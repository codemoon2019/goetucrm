<?php

namespace App\Services\Tickets\Requesters;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PartnerTicketRequesterAccessor
{
    protected $product;
    protected $user;

    public function __construct(Product $product, User $user)
    {
        $this->product = $product;
        $this->user = $user;
    }

    public function getMerchants()
    {
        $partnerIds = $this->user->partner->downlines->pluck('id');
        $columns = [
            'users.id', 
            'users.image',
            'users.username',
            'users.first_name', 
            'users.last_name', 
            'self_partner_companies.company_name AS self_company_name',
            'users.company_id',
            'partner_companies.company_name',
            'upline_users.id AS upline_user_id',
        ];
        
        return DB::table('users')
            ->select($columns)
            ->distinct()
            ->join('partners', 'partners.id', '=', 'users.reference_id')
            ->leftJoin(
                'partners as upline_partners',
                'upline_partners.id', '=', 'partners.parent_id')
            ->leftJoin(
                'users as upline_users',
                'upline_users.reference_id', '=', 'upline_partners.id')
            ->join(
                'partner_companies', 
                'partner_companies.partner_id', '=', 'users.company_id')
            ->join(
                'partner_products', 
                'partner_products.partner_id', '=', 'upline_partners.id')
            ->join(
                'products as sub_products', 
                'sub_products.id', '=', 'partner_products.product_id')
            ->join(
                'partner_companies as self_partner_companies',
                'self_partner_companies.partner_id', '=', 'partners.id')
            ->join('products', 'products.id', '=', 'sub_products.parent_id')
            ->whereIn('partners.partner_type_id', [3])
            ->whereIn('partners.id', $partnerIds)
            ->where('sub_products.status', 'A')
            ->where('products.id', $this->product->id)
            ->where('products.status', 'A')
            ->where(function($query) {
                $query
                    ->where('upline_users.is_original_partner', true)
                    ->orWhere('upline_users.is_original_partner', null);
            })
            ->where('users.status', 'A')
            ->orderBy('partner_companies.company_name')
            ->orderBy('users.first_name')
            ->orderBy('users.last_name')
            ->get();
    }

    public function getPartners()
    {
        $partnerIds = $this->user->partner->downlines->pluck('id');
        $partnerIds[] = $this->user->partner->id;
        $columns = [
            'users.id', 
            'users.image',
            'users.username',
            'users.first_name', 
            'users.last_name', 
            'users.company_id',
            'partner_companies.company_name',
            'upline_users.id AS upline_user_id',
        ];

        return DB::table('users')
            ->select($columns)
            ->distinct()
            ->join('partners', 'partners.id', '=', 'users.reference_id')
            ->leftJoin(
                'partners as upline_partners',
                'upline_partners.id', '=', 'partners.parent_id')
            ->leftJoin(
                'users as upline_users',
                'upline_users.reference_id', '=', 'upline_partners.id')
            ->join(
                'partner_companies', 
                'partner_companies.partner_id', '=', 'users.company_id')
            ->join(
                'partner_products', 
                'partner_products.partner_id', '=', 'partners.id')
            ->join(
                'products as sub_products', 
                'sub_products.id', '=', 'partner_products.product_id')
            ->join('products', 'products.id', '=', 'sub_products.parent_id')
            ->whereIn('partners.partner_type_id', [1, 2, 4, 5, 7])
            ->whereIn('partners.id', $partnerIds)
            ->where('sub_products.status', 'A')
            ->where('products.id', $this->product->id)
            ->where('products.status', 'A')
            ->where(function($query) {
                $query
                    ->where('upline_users.is_original_partner', true)
                    ->orWhere('upline_users.is_original_partner', null);
            })
            ->orderBy('partner_companies.company_name')
            ->orderBy('users.first_name')
            ->orderBy('users.last_name')
            ->get();
    }
}