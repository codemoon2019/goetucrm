<?php

namespace App\Services\Products;

use App\Models\User;
use App\Services\Users\UserClassification;

class ProductAccessor
{
    public $products;
    public $productsGroups;

    public function __construct(User $user)
    {
        $userClassification = new UserClassification($user);

        if ($userClassification->isInternal ||
            $userClassification->isInternalDepartmentHead) {

            $this->products = (new InternalProductAccessor)->getProducts($user);
        } else if ($userClassification->isAdmin) {
            $this->products = (new AdminProductAccessor)->getProducts($user);
        } else if ($userClassification->isCompany) {
            $this->products = (new PartnerProductAccessor)->getProducts($user);
        } else if ($userClassification->isPartner) {
            $this->products = (new PartnerProductAccessor)->getProducts($user);
        } else if ($userClassification->isMerchant) {
            $uplineUser = $user->partner->upline->connectedUser;
            $this->products = (new PartnerProductAccessor)->getProducts($uplineUser);
        } else if ($userClassification->isBranch) {
            $uplineUser = $user->partner->upline->upline->connectedUser;
            $this->products = (new PartnerProductAccessor)->getProducts($uplineUser);
        } else {
            $this->products = collect([]);
        }

        $this->productsGroups = $this->products->groupBy('company_id');
    }
}