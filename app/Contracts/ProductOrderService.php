<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface ProductOrderService
{
    public function countProductOrdersByCompany($companyId);
    public function getProductOrdersByCompany($companyId);
}