<?php
/**
 * Created by PhpStorm.
 * User: Jaspher Ramile
 * Date: 10/5/18
 * Time: 7:09 PM
 */

namespace App\Contracts;


interface DashboardService extends BaseService
{
    public function fetchCompanies();

    public function fetchAgentSales();

    public function fetchProductSale(int $productId = null, int $companyId = null, $partnerTypeId = null, $startDate = null, $endDate = null);
 
    public function fetchProductSaleBar(int $productId = null, int $companyId = null, $partnerTypeId = null, $startDate = null, $endDate = null);

    public function fetchPartnerSale(int $partnerId = null, int $productId = null, $startDate = null, $endDate = null);
}