<?php

namespace App\Services\Products;

use App\Contracts\ProductOrderService;
use App\Models\ProductOrder;
use App\Services\BaseServiceImpl;

class ProductOrderServiceImpl extends BaseServiceImpl implements ProductOrderService
{
    public function countProductOrdersByCompany($companyId)
    {
        return ProductOrder::isActive()
            ->with(['product', 'user'])
            ->whereHas('subTaskHeader')
            ->whereCompany($companyId)
            ->count();
    }

    public function getProductOrdersByCompanyNoGrouping($companyId=null)
    {
        return ProductOrder::isActive()
            ->with('partnerCompany')
            ->with(['product', 'user', 'subTaskHeader.subtasks'])
            ->whereHas('subTaskHeader')
            ->whereCompany($companyId)
            ->get();
    }

    public function getProductOrdersByCompany($companyId=null)
    {
        return $this->getProductOrdersByCompanyNoGrouping($companyId)
            ->sortBy('partnerCompany.company_name', SORT_NATURAL|SORT_FLAG_CASE)
            ->groupBy('company_id');
    }
}